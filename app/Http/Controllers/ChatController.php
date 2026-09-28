<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Conversation;
use App\Models\Message;
use App\Services\ChatDistributionService;
use App\Services\ProfanityFilter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function getConversations()
    {
        $user = Auth::user();
        if ($user->hasAnyRole(['Admin', 'Staff'])) {
            $conversations = Conversation::with(['user', 'cskh', 'messages' => function ($q) {
                $q->latest()->limit(1);
            }])->orderBy('updated_at', 'desc')->get();
        } elseif ($user->hasAnyRole(['cskh'])) {
            $conversations = Conversation::with(['user', 'cskh', 'messages' => function ($q) {
                $q->latest()->limit(1);
            }])->where(function ($query) use ($user) {
                $query->where('cskh_id', $user->id)
                    ->orWhereNull('cskh_id');
            })->orderBy('updated_at', 'desc')->get();
        } else {
            $conversations = Conversation::with(['cskh', 'messages' => function ($q) {
                $q->latest()->limit(1);
            }])->where('user_id', $user->id)->orderBy('updated_at', 'desc')->get();
        }

        return response()->json($conversations);
    }

    public function getMessages($id)
    {
        $conversation = Conversation::with('messages.sender')->findOrFail($id);

        $user = Auth::user();
        if (! $user->hasAnyRole(['cskh', 'Admin', 'Staff']) && $conversation->user_id !== $user->id) {
            abort(403);
        }

        return response()->json($conversation->messages);
    }

    public function sendMessage(Request $request, $id)
    {
        $request->validate([
            'message' => 'nullable|string',
            'attachment' => 'nullable|file|max:10240', // max 10MB
        ]);

        $conversation = Conversation::findOrFail($id);

        $user = Auth::user();
        if (! $user->hasAnyRole(['cskh', 'Admin', 'Staff']) && $conversation->user_id !== $user->id) {
            abort(403);
        }

        // If admin/cskh replies, assign conversation to them
        if ($user->hasAnyRole(['cskh', 'Admin', 'Staff'])) {
            if ($conversation->cskh_id !== $user->id) {
                $conversation->cskh_id = $user->id;
                $conversation->routing_status = 'assigned';
                $conversation->assigned_at = now();
                $conversation->save();
            }
        } else {
            // Customer sending message: check if CSKH is offline or unassigned
            $chatDistributionService = app(ChatDistributionService::class);
            $shouldAssign = false;

            if ($conversation->cskh_id === null) {
                $shouldAssign = true;
            } else {
                $cskh = $conversation->cskh;
                if (! $cskh || ! $cskh->is_active || ! $cskh->last_seen_at || $cskh->last_seen_at->lessThan(now()->subMinutes(5))) {
                    $shouldAssign = true;
                }
            }

            if ($shouldAssign) {
                $chatDistributionService->assign($conversation);
            }
        }

        $path = null;
        $originalName = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $originalName = $file->getClientOriginalName();
            $path = $file->store('chat_attachments', 'public');
        }

        if (! $request->message && ! $path) {
            return response()->json(['error' => 'Tin nhắn hoặc tệp đính kèm là bắt buộc.'], 422);
        }

        $message = $conversation->messages()->create([
            'sender_id' => $user->id,
            'message' => ProfanityFilter::filter($request->message),
            'attachment_path' => $path ? '/storage/'.$path : null,
            'attachment_name' => $originalName,
        ]);

        $conversation->touch(); // Update updated_at

        broadcast(new MessageSent($message))->toOthers();

        return response()->json($message->load('sender'));
    }

    public function startConversation(Request $request)
    {
        $user = Auth::user();

        // Find existing open conversation or create new
        $conversation = Conversation::firstOrCreate(
            ['user_id' => $user->id, 'status' => 'open'],
            ['booking_id' => $request->booking_id ?? null]
        );

        $chatDistributionService = app(ChatDistributionService::class);
        $shouldAssign = false;

        if ($conversation->cskh_id === null) {
            $shouldAssign = true;
        } else {
            $cskh = $conversation->cskh;
            if (! $cskh || ! $cskh->is_active || ! $cskh->last_seen_at || $cskh->last_seen_at->lessThan(now()->subMinutes(5))) {
                $shouldAssign = true;
            }
        }

        if ($shouldAssign) {
            $chatDistributionService->assign($conversation);
            $conversation->refresh();
        }

        return response()->json($conversation);
    }

    public function markImportant(Request $request, $id, $messageId)
    {
        $conversation = Conversation::findOrFail($id);
        $user = Auth::user();

        if (! $user->hasAnyRole(['cskh', 'Admin', 'Staff']) && $conversation->user_id !== $user->id) {
            abort(403);
        }

        $message = $conversation->messages()->findOrFail($messageId);
        $message->is_important = ! $message->is_important;
        $message->save();

        // Optionally, broadcast an event here if real-time UI update is needed,
        // but simple response is enough if UI updates optimistically.
        return response()->json(['success' => true, 'is_important' => $message->is_important]);
    }

    public function getUnreadCount()
    {
        $user = Auth::user();

        if ($user->hasAnyRole(['Admin', 'Staff'])) {
            $count = Message::whereNull('read_at')
                ->where('sender_id', '!=', $user->id)
                ->whereHas('sender', function ($q) {
                    $q->whereDoesntHave('roles', function ($r) {
                        $r->whereIn('name', ['Admin', 'cskh', 'Staff']);
                    });
                })->count();
        } elseif ($user->hasAnyRole(['cskh'])) {
            $count = Message::whereNull('read_at')
                ->where('sender_id', '!=', $user->id)
                ->whereHas('sender', function ($q) {
                    $q->whereDoesntHave('roles', function ($r) {
                        $r->whereIn('name', ['Admin', 'cskh', 'Staff']);
                    });
                })
                ->whereHas('conversation', function ($q) use ($user) {
                    $q->where('status', 'open')
                        ->where(function ($sq) use ($user) {
                            $sq->where('cskh_id', $user->id)
                                ->orWhereNull('cskh_id');
                        });
                })->count();
        } else {
            // Get user's open conversation
            $conversation = Conversation::where('user_id', $user->id)
                ->where('status', 'open')
                ->first();

            if (! $conversation) {
                return response()->json(['count' => 0]);
            }

            // Count unread messages (messages sent by others and not read yet)
            $count = $conversation->messages()
                ->where('sender_id', '!=', $user->id)
                ->whereNull('read_at')
                ->count();
        }

        return response()->json(['count' => $count]);
    }

    public function markAsRead($id)
    {
        $conversation = Conversation::findOrFail($id);
        $user = Auth::user();

        if (! $user->hasAnyRole(['cskh', 'Admin', 'Staff']) && $conversation->user_id !== $user->id) {
            abort(403);
        }

        // Mark all messages in this conversation as read for current user
        $conversation->messages()
            ->where('sender_id', '!=', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }
}
