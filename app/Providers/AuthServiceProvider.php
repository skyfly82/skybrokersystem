<?php

/**
 * Cel: Provider dla uwierzytelniania i autoryzacji z policies
 * Moduł: Auth
 * Odpowiedzialny: sky_fly82
 * Data: 2025-09-02
 */

namespace App\Providers;

use App\Models\ApiKey;
use App\Models\Order;
use App\Policies\ApiKeyPolicy;
use App\Policies\OrderPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Order::class => OrderPolicy::class,
        ApiKey::class => ApiKeyPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
