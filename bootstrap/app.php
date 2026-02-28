<?php

use App\Http\Middleware\CheckSubscription;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
          $middleware->alias([
            'admin' => \App\Http\Middleware\AdminOnly::class,
            // 'isRestaurantVerified' => \App\Http\Middleware\IsRestaurantVerified::class,
            'isRestaurantAuthenticated' => \App\Http\Middleware\IsRestaurantAuthenticated::class,
            'checkSubscription' => CheckSubscription::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();


    