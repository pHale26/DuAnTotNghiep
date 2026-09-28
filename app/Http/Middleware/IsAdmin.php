<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->hasAnyRole(['Admin', 'Staff', 'cskh'])) {
            return $next($request);
        }

        abort(403, 'Bạn không có quyền truy cập trang quản trị.');
    }
}
