<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\AuthMiddleware;
use App\Http\Middleware\GuestMiddleware;

return Application::configure(basePath: dirname(__DIR__))

    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        channels: __DIR__ . '/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Register middleware aliases
        $middleware->alias([
            'auth' => AuthMiddleware::class,
            'guest' => GuestMiddleware::class,
        ]);

        // Or use Laravel's default auth middleware
        // $middleware->alias([
        //     'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
        //     'guest' => \Illuminate\Auth\Middleware\RedirectIfAuthenticated::class,
        // ]);
    })

    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->withBroadcasting(__DIR__ . '/../routes/channels.php')
    ->create();
