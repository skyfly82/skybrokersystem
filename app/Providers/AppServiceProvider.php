<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;

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
