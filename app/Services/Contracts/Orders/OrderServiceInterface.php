<?php

/**
 * Cel: Kontrakt dla serwisu zarządzania zamówieniami
 * Moduł: Orders
 * Odpowiedzialny: Claude-Code
 * Data: 2025-09-02
 */

namespace App\Services\Contracts\Orders;

use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface OrderServiceInterface
{
    /**
     * Get paginated orders for a customer
     */
    public function getOrdersForCustomer(int $customerId, array $filters = []): LengthAwarePaginator;

    /**
     * Create new order
     */
    public function createOrder(array $data): Order;

    /**
     * Update existing order
     */
    public function updateOrder(Order $order, array $data): Order;

    /**
     * Cancel order
     */
    public function cancelOrder(Order $order, ?string $reason = null): Order;

    /**
     * Update order status
     */
    public function updateOrderStatus(Order $order, string $status): Order;

    /**
     * Calculate order total
     */
    public function calculateOrderTotal(array $orderData): float;

    /**
     * Validate order data
     */
    public function validateOrderData(array $data): array;
}
