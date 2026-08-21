<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\SuperAdminMiddleware;
use App\Http\Middleware\SetTenantDatabase;
use App\Http\Middleware\AuthOrSession;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(at: '*');

        $middleware->alias([
            'superadmin' => SuperAdminMiddleware::class,
            'tenant.db' => SetTenantDatabase::class,
            'auth.or.session' => AuthOrSession::class,
        ]);
        
        // Apply tenant database middleware to all api routes (run FIRST)
        $middleware->api(prepend: [
            SetTenantDatabase::class,
        ]);

        // Apply tenant database middleware to all web routes
        $middleware->web(append: [
            SetTenantDatabase::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
