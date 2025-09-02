<?php

/**
 * Cel: Główny dostawca usług aplikacji z rejestracją serwisów
 * Moduł: Core
 * Odpowiedzialny: Claude-Code
 * Data: 2025-09-02
 */

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;

// Service Contracts
use App\Services\Contracts\Auth\AuthServiceInterface;
use App\Services\Contracts\Orders\OrderServiceInterface;
use App\Services\Contracts\Payments\PaymentServiceInterface;

// Service Implementations
use App\Services\Auth\AuthService;
use App\Services\Orders\OrderService;
use App\Services\Payments\PaymentService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Polyfill dla paczek/providera wołających $app->make('files')
        $this->app->singleton('files', function () {
            return new Filesystem();
        });

        // Register service layer bindings
        $this->registerServiceBindings();
    }

    /**
     * Register service layer interface->implementation bindings
     */
    private function registerServiceBindings(): void
    {
        $this->app->bind(AuthServiceInterface::class, AuthService::class);
        $this->app->bind(OrderServiceInterface::class, OrderService::class);
        
        // PaymentService will be created in next step
        // $this->app->bind(PaymentServiceInterface::class, PaymentService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();
    }

    /**
     * Configure rate limiting for API endpoints
     */
    private function configureRateLimiting(): void
    {
        // Default API rate limiter
        RateLimiter::for('api', function (Request $request) {
            $perMinute = 60;
            $perHour = 1000;
            
            // Use API key or IP for identification
            $apiKey = $request->header('X-API-Key');
            $key = $apiKey ? "api-key:{$apiKey}" : "ip:{$request->ip()}";
            
            return [
                Limit::perMinute($perMinute)->by($key),
                Limit::perHour($perHour)->by($key),
            ];
        });

        // Strict rate limiting for authentication endpoints
        RateLimiter::for('auth', function (Request $request) {
            return [
                Limit::perMinute(5)->by($request->ip()),
                Limit::perHour(20)->by($request->ip()),
            ];
        });

        // More lenient limits for authenticated users
        RateLimiter::for('authenticated', function (Request $request) {
            $userId = $request->user()?->id;
            $key = $userId ? "user:{$userId}" : "ip:{$request->ip()}";
            
            return [
                Limit::perMinute(120)->by($key),
                Limit::perHour(2000)->by($key),
            ];
        });

        // No limits for webhooks from trusted sources
        RateLimiter::for('webhooks', function (Request $request) {
            return Limit::none();
        });
    }
}
