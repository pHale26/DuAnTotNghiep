<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;

class NotificationController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if (! $user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        // Run the group split status check automatically so it works on local environment without cron setup
        try {
            Artisan::call('group-splits:update-status');
        } catch (\Exception $e) {
            // Ignore error
        }

        $notifications = $user->notifications()->paginate(10);

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $user->unreadNotifications->count(),
        ]);
    }

    public function markAsRead($id)
    {
        $user = auth()->user();
        if (! $user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $notification = $user->notifications()->find($id);
        if ($notification) {
            $notification->markAsRead();
        }

        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        $user = auth()->user();
        if (! $user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $user->unreadNotifications->markAsRead();

        return response()->json(['success' => true]);
    }

    public function unreadCount()
    {
        $user = auth()->user();
        if (! $user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        return response()->json([
            'unread_count' => $user->unreadNotifications->count(),
        ]);
    }
}
