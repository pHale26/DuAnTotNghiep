<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UpdateLastSeenAt
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            // Only update last_seen_at if it's null or older than 1 minute
            if (! $user->last_seen_at || now()->diffInSeconds($user->last_seen_at) > 60) {
                // Using updateQuietly to avoid triggering any event handlers (saving/updating)
                $user->last_seen_at = now();
                $user->saveQuietly();
            }
        }

        return $next($request);
    }
}
