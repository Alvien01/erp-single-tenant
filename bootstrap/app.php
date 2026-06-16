<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Register middleware aliases
        $middleware->alias([
            'module' => \App\Http\Middleware\CheckModuleAccess::class,
            'menu-access' => \App\Http\Middleware\CheckMenuAccess::class,
        ]);

        // Append CheckMenuAccess to all web routes
        $middleware->web(append: [
            \App\Http\Middleware\CheckMenuAccess::class,
        ]);

        // Exclude Midtrans webhook from CSRF verification
        $middleware->validateCsrfTokens(except: [
            'midtrans/webhook',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
