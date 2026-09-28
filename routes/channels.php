<?php

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('conversation.{id}', function ($user, $id) {
    $conversation = Conversation::find($id);
    if (! $conversation) {
        return false;
    }

    // Allow if user is the customer OR if user has 'cskh' or 'admin' role
    return $user->id === $conversation->user_id || $user->hasAnyRole(['cskh', 'Admin', 'Staff']);
});

Broadcast::channel('admin.chat', function ($user) {
    return $user->hasAnyRole(['Admin', 'cskh', 'Staff']);
});

Broadcast::channel('tour.{id}', function (?User $user, $id) {
    // If we want a presence channel, we need to return an array of user data.
    // If not authenticated (handled by custom auth endpoint), they get a fake user.
    return [
        'id' => $user->id ?? session()->getId(),
        'name' => $user->name ?? 'Guest',
    ];
});
