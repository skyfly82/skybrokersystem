<?php

/**
 * Cel: Policy dla autoryzacji operacji na zamówieniach
 * Moduł: Orders
 * Odpowiedzialny: sky_fly82
 * Data: 2025-09-02
 */

namespace App\Policies;

use App\Models\CustomerUser;
use App\Models\Order;

class OrderPolicy
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
    public function view(CustomerUser $user, Order $order): bool
    {
        // User can view order if it belongs to their customer
        return $user->customer_id === $order->customer_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(CustomerUser $user): bool
    {
        // User can create orders if their customer is active
        return $user->customer && $user->customer->isActive();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(CustomerUser $user, Order $order): bool
    {
        // User can update order if it belongs to their customer and is not finalized
        return $user->customer_id === $order->customer_id &&
               ! in_array($order->status, ['completed', 'cancelled']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(CustomerUser $user, Order $order): bool
    {
        // User can delete/cancel order if it belongs to their customer and can be cancelled
        return $user->customer_id === $order->customer_id && $order->canBeCancelled();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(CustomerUser $user, Order $order): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(CustomerUser $user, Order $order): bool
    {
        return false;
    }
}
