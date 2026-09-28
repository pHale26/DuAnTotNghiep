<?php

namespace App\Console\Commands;

use App\Models\Conversation;
use App\Services\ChatDistributionService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ReRouteOfflineChats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chat:re-route-offline';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Quét và phân phối lại cuộc trò chuyện nếu nhân viên phụ trách đã offline hoặc ngưng hoạt động';

    /**
     * Execute the console command.
     */
    public function handle(ChatDistributionService $distributionService)
    {
        $threshold = Carbon::now()->subMinutes(5);

        // Find all open conversations assigned to a CSKH
        $conversations = Conversation::where('status', 'open')
            ->whereNotNull('cskh_id')
            ->with('cskh')
            ->get();

        $reRoutedCount = 0;

        foreach ($conversations as $conversation) {
            $cskh = $conversation->cskh;

            // Re-route if CSKH is missing, inactive, or hasn't been active in the last 5 minutes
            if (! $cskh || ! $cskh->is_active || ! $cskh->last_seen_at || $cskh->last_seen_at->lessThan($threshold)) {
                $oldCskhId = $conversation->cskh_id;

                // Trigger re-assignment
                $newAgent = $distributionService->assign($conversation);

                if ($newAgent) {
                    $this->info("Re-routed conversation #{$conversation->id} from CSKH #{$oldCskhId} to online CSKH #{$newAgent->id}");
                } else {
                    $this->warn("Conversation #{$conversation->id} was unassigned because no CSKH agent is currently online");
                }
                $reRoutedCount++;
            }
        }

        $this->info("Completed scanning conversations. Re-routed {$reRoutedCount} conversations.");
    }
}
