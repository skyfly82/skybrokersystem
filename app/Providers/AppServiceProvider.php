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
        // Define default API rate limiter used by `throttle:api`
        RateLimiter::for('api', function (Request $request) {
            $perMinute = (int) (config('map.api.rate_limit_per_minute') ?? 60);
            // Prefer API key as identifier if present, fallback to IP
            $apiKey = $request->header(config('map.api.header', 'X-API-Key'))
                ?? (string) $request->query('api_key');
            $key = $apiKey ?: $request->ip();
            return Limit::perMinute($perMinute)->by($key);
        });
    }
}
