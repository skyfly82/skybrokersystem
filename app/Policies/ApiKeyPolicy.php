<?php

/**
 * Cel: Policy dla autoryzacji operacji na kluczach API
 * Moduł: Auth
 * Odpowiedzialny: Claude-Code
 * Data: 2025-09-02
 */

namespace App\Policies;

use App\Models\CustomerUser;
use App\Models\ApiKey;

class ApiKeyPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(CustomerUser $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(CustomerUser $user, ApiKey $apiKey): bool
    {
        return $user->customer_id === $apiKey->customer_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(CustomerUser $user): bool
    {
        // Only customer admins can create API keys
        return $user->role === 'admin' && $user->customer && $user->customer->isActive();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(CustomerUser $user, ApiKey $apiKey): bool
    {
        return $user->customer_id === $apiKey->customer_id && $user->role === 'admin';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(CustomerUser $user, ApiKey $apiKey): bool
    {
        return $user->customer_id === $apiKey->customer_id && $user->role === 'admin';
    }

    /**
     * Determine whether the user can regenerate the API key.
     */
    public function regenerate(CustomerUser $user, ApiKey $apiKey): bool
    {
        return $user->customer_id === $apiKey->customer_id && $user->role === 'admin';
    }
}