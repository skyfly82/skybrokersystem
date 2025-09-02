<?php

/**
 * Cel: Bootstrap aplikacji z modularnym routingiem
 * Moduł: Core
 * Odpowiedzialny: Claude-Code
 * Data: 2025-09-02
 */

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\ApiKeyMiddleware;
use App\Http\Middleware\CheckApiKey;
use App\Http\Middleware\CustomerActiveMiddleware;
use App\Http\Middleware\CustomerAdminMiddleware;
use App\Http\Middleware\MarketingMiddleware;
use App\Http\Middleware\SecurityHeadersMiddleware;
use App\Http\Middleware\SetLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            // Load modular API routes
            $apiRoutePath = base_path('routes/api');
            if (is_dir($apiRoutePath)) {
                $files = glob($apiRoutePath.'/*.php');
                foreach ($files as $file) {
                    Route::middleware('api')
                        ->prefix('api')
                        ->group($file);
                }
            }

            // Load versioned API routes if they exist (routes/api/v1/, routes/api/v2/, etc.)
            $versionDirs = glob($apiRoutePath.'/v*', GLOB_ONLYDIR);
            foreach ($versionDirs as $versionDir) {
                $version = basename($versionDir);
                $files = glob($versionDir.'/*.php');
                foreach ($files as $file) {
                    Route::middleware('api')
                        ->prefix("api/{$version}")
                        ->group($file);
                }
            }
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Global middleware
        $middleware->web(append: [
            SetLocale::class,
            SecurityHeadersMiddleware::class,
        ]);

        $middleware->api(append: [
            SecurityHeadersMiddleware::class,
        ]);

        // Rejestracja middleware aliasów
        $middleware->alias([
            'admin' => AdminMiddleware::class,
            'customer.active' => CustomerActiveMiddleware::class,
            'customer.admin' => CustomerAdminMiddleware::class,
            'marketing' => MarketingMiddleware::class,
            'api.key' => ApiKeyMiddleware::class,
            'check.apikey' => CheckApiKey::class,
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);

        // Rate limiting dla API
        $middleware->throttleApi();

        // CORS dla API (Sanctum) - tylko jeśli pakiet jest obecny
        if (class_exists(\Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class)) {
            $middleware->api(prepend: [
                \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            ]);
        }
    })
    ->withProviders([
        App\Providers\AuthServiceProvider::class,
    ])
    ->withExceptions(function (Exceptions $exceptions) {
        // Custom exception handling is handled in App\Exceptions\Handler
    })
    ->create();
