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
        // Append TenantMiddleware to the 'web' middleware group
        $middleware->web(append: [
            \App\Http\Middleware\TenantMiddleware::class,
        ]);

        // Register 'tenant' as an alias for use in specific routes
        $middleware->alias([
            'tenant' => \App\Http\Middleware\TenantMiddleware::class,
            'module' => \App\Http\Middleware\CheckModuleAccess::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
