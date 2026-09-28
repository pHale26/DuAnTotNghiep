<?php

use App\Http\Middleware\CheckUserActive;
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\IsGuide;
use App\Http\Middleware\SetLocale;
use App\Http\Middleware\UpdateLastSeenAt;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');

        $middleware->validateCsrfTokens(except: [
            'api/*',
            'webhooks/*',
            'broadcasting/guest-auth',
        ]);

        $middleware->web(append: [
            SetLocale::class,
            CheckUserActive::class,
            UpdateLastSeenAt::class,
        ]);

        $middleware->redirectUsersTo(function (Request $request) {
            if (auth()->check()) {
                if (auth()->user()->hasAnyRole(['Admin'])) {
                    return route('admin.dashboard');
                }
                if (auth()->user()->hasAnyRole(['Staff', 'cskh'])) {
                    return route('admin.chat.index');
                }
                if (auth()->user()->role === 'guide') {
                    return route('guide.dashboard');
                }
            }

            return '/';
        });

        $middleware->alias([
            'admin' => IsAdmin::class,
            'guide' => IsGuide::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
