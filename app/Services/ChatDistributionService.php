<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ChatDistributionService
{
    /**
     * Get the next available support agent using the Least Connections algorithm.
     * An agent is available if:
     * - They are active (is_active = true)
     * - They have the role of cskh, Staff, or Admin (or any role permitted to handle chats)
     * - They have been seen online in the last 5 minutes (last_seen_at >= now() - 5 mins)
     *
     * Least Connections logic:
     * - Count the number of active/open conversations currently assigned to each online agent.
     * - Return the agent with the lowest count of open conversations.
     */
    public function getAvailableAgent(): ?User
    {
        // Define offline threshold (5 minutes)
        $threshold = Carbon::now()->subMinutes(5);

        $agents = User::role('cskh')
            ->where('is_active', true)
            ->where('last_seen_at', '>=', $threshold)
            ->get();

        if ($agents->isEmpty()) {
            // Fallback: If no cskh roles are online, check for 'Staff' or 'Admin'
            $agents = User::role(['Staff', 'Admin'])
                ->where('is_active', true)
                ->where('last_seen_at', '>=', $threshold)
                ->get();
        }

        if ($agents->isEmpty()) {
            return null;
        }

        // Count open conversations for each agent
        // We select the agent with the minimum number of open conversations
        $agentIds = $agents->pluck('id')->toArray();

        $conversationCounts = Conversation::whereIn('cskh_id', $agentIds)
            ->where('status', 'open')
            ->select('cskh_id', DB::raw('count(*) as aggregate'))
            ->groupBy('cskh_id')
            ->pluck('aggregate', 'cskh_id')
            ->toArray();

        // Find agent with minimum count
        $selectedAgent = null;
        $minCount = PHP_INT_MAX;

        foreach ($agents as $agent) {
            $count = $conversationCounts[$agent->id] ?? 0;
            if ($count < $minCount) {
                $minCount = $count;
                $selectedAgent = $agent;
            }
        }

        return $selectedAgent;
    }

    /**
     * Assign a conversation to an available agent.
     *
     * @return User|null Assigned agent, or null if no agent was online
     */
    public function assign(Conversation $conversation): ?User
    {
        $agent = $this->getAvailableAgent();

        if ($agent) {
            $conversation->update([
                'cskh_id' => $agent->id,
                'assigned_at' => Carbon::now(),
                'routing_status' => 'assigned',
            ]);

            return $agent;
        }

        // Mark as unassigned if no agent is available
        $conversation->update([
            'cskh_id' => null,
            'routing_status' => 'unassigned',
        ]);

        return null;
    }
}
