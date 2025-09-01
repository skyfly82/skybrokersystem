<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\CustomerActiveMiddleware;
use App\Http\Middleware\CustomerAdminMiddleware;
use App\Http\Middleware\ApiKeyMiddleware;
use App\Http\Middleware\SetLocale;
use App\Exceptions\Handler;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Global middleware
        $middleware->web(append: [
            SetLocale::class,
        ]);

        // Rejestracja middleware aliasów
        $middleware->alias([
            'admin' => AdminMiddleware::class,
            'customer.active' => CustomerActiveMiddleware::class,
            'customer.admin' => CustomerAdminMiddleware::class,
            'api.key' => ApiKeyMiddleware::class,
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);

        // Rate limiting dla API
        $middleware->throttleApi();
        
        // CORS dla API
        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Custom exception handling is handled in App\Exceptions\Handler
    })
    ->create();