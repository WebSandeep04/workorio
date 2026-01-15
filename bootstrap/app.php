<?php

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
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'superadmin' => \App\Http\Middleware\SuperAdminMiddleware::class,
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'tenant.db' => \App\Http\Middleware\SetTenantDatabase::class,
            'auth.or.session' => \App\Http\Middleware\AuthOrSession::class,
        ]);
        
        // Apply tenant database middleware to all api routes (run FIRST)
        $middleware->api(prepend: [
            \App\Http\Middleware\SetTenantDatabase::class,
        ]);

        // Apply tenant database middleware to all web routes
        $middleware->web(append: [
            \App\Http\Middleware\SetTenantDatabase::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
