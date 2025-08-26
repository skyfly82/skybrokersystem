<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\CustomerActiveMiddleware;
use App\Http\Middleware\CustomerAdminMiddleware;
use App\Http\Middleware\ApiKeyMiddleware;
use App\Http\Middleware\SetLocale;

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
        ]);

        // Rate limiting dla API
        $middleware->throttleApi();
        
        // CORS dla API
        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->unauthenticated(function ($request, $exception) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }

            // Determine which guard triggered the exception
            $guards = $exception->guards();
            
            if (in_array('system_user', $guards)) {
                return redirect()->guest(route('admin.login'));
            }
            
            if (in_array('customer_user', $guards)) {
                return redirect()->guest(route('customer.login'));
            }

            // Default fallback to admin login for any other guard
            return redirect()->guest(route('admin.login'));
        });
    })->create();