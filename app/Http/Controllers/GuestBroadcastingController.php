<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;
use Pusher\Pusher;

class GuestBroadcastingController extends Controller
{
    public function auth(Request $request)
    {
        $user = auth()->user();

        // Ensure session is started for guests
        if (! $user) {
            $request->session()->start();
        }

        // We can just use Broadcast::auth and simulate a user if they are guest
        if ($request->channel_name === 'presence-tour.'.$request->tour_id || str_starts_with($request->channel_name, 'presence-tour.')) {
            $pusher = new Pusher(
                env('REVERB_APP_KEY'),
                env('REVERB_APP_SECRET'),
                env('REVERB_APP_ID'),
                [
                    'host' => env('REVERB_HOST'),
                    'port' => env('REVERB_PORT'),
                    'scheme' => env('REVERB_SCHEME'),
                ]
            );

            $userId = $user ? $user->id : session()->getId();
            $userInfo = [
                'name' => $user ? $user->name : 'Guest',
                'is_guest' => ! $user,
            ];

            return response($pusher->presence_auth($request->channel_name, $request->socket_id, $userId, $userInfo));
        }

        return Broadcast::auth($request);
    }
}
