<?php

/**
 * Cel: Implementacja serwisu zarządzania zamówieniami
 * Moduł: Orders
 * Odpowiedzialny: Claude-Code
 * Data: 2025-09-02
 */

namespace App\Services\Orders;

use App\Models\Order;
use App\Services\Contracts\Orders\OrderServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class OrderService implements OrderServiceInterface
{
    public function getOrdersForCustomer(int $customerId, array $filters = []): LengthAwarePaginator
    {
        $query = Order::where('customer_id', $customerId);

        // Apply filters
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        return $query->with(['shipments', 'payments'])
                    ->orderBy('created_at', 'desc')
                    ->paginate($filters['per_page'] ?? 15);
    }

    public function createOrder(array $data): Order
    {
        $validatedData = $this->validateOrderData($data);

        DB::beginTransaction();
        
        try {
            $order = Order::create([
                'customer_id' => $validatedData['customer_id'],
                'customer_user_id' => $validatedData['customer_user_id'],
                'order_number' => Order::generateOrderNumber(),
                'status' => 'pending',
                'total_amount' => $this->calculateOrderTotal($validatedData),
                'currency' => $validatedData['currency'] ?? 'PLN',
                'shipping_data' => $validatedData['shipping_data'],
                'notes' => $validatedData['notes'] ?? null,
            ]);

            Log::info('Order created', ['order_id' => $order->id, 'customer_id' => $order->customer_id]);
            
            DB::commit();
            
            return $order->load(['customer', 'customerUser']);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create order', ['error' => $e->getMessage(), 'data' => $data]);
            throw $e;
        }
    }

    public function updateOrder(Order $order, array $data): Order
    {
        $validatedData = $this->validateOrderData($data, $order);

        DB::beginTransaction();

        try {
            $order->update([
                'shipping_data' => $validatedData['shipping_data'] ?? $order->shipping_data,
                'notes' => $validatedData['notes'] ?? $order->notes,
                'total_amount' => isset($validatedData['shipping_data']) 
                    ? $this->calculateOrderTotal(array_merge($order->toArray(), $validatedData))
                    : $order->total_amount,
            ]);

            Log::info('Order updated', ['order_id' => $order->id]);
            
            DB::commit();

            return $order->fresh();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update order', ['order_id' => $order->id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function cancelOrder(Order $order, string $reason = null): Order
    {
        if (!$order->canBeCancelled()) {
            throw ValidationException::withMessages([
                'order' => 'This order cannot be cancelled at this time.'
            ]);
        }

        $order->update([
            'status' => 'cancelled',
            'notes' => $order->notes . "\n\nCancellation reason: " . ($reason ?? 'Customer request'),
        ]);

        Log::info('Order cancelled', ['order_id' => $order->id, 'reason' => $reason]);

        return $order;
    }

    public function updateOrderStatus(Order $order, string $status): Order
    {
        $validStatuses = ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled', 'returned'];
        
        if (!in_array($status, $validStatuses)) {
            throw ValidationException::withMessages([
                'status' => 'Invalid order status.'
            ]);
        }

        $order->update(['status' => $status]);

        if ($status === 'delivered') {
            $order->update(['completed_at' => now()]);
        }

        Log::info('Order status updated', ['order_id' => $order->id, 'status' => $status]);

        return $order;
    }

    public function calculateOrderTotal(array $orderData): float
    {
        $total = 0.0;

        // Base shipping cost calculation - simplified for now
        if (isset($orderData['shipping_data'])) {
            $shippingData = $orderData['shipping_data'];
            
            // Add weight-based cost
            if (isset($shippingData['weight'])) {
                $total += $shippingData['weight'] * 0.5; // 0.5 PLN per kg
            }

            // Add distance-based cost (if available)
            if (isset($shippingData['distance'])) {
                $total += $shippingData['distance'] * 0.1; // 0.1 PLN per km
            }

            // Minimum cost
            $total = max($total, 15.0);
        }

        return round($total, 2);
    }

    public function validateOrderData(array $data, Order $existingOrder = null): array
    {
        // Basic validation - in real implementation would use Form Request
        $required = $existingOrder ? [] : ['customer_id', 'customer_user_id', 'shipping_data'];
        
        foreach ($required as $field) {
            if (empty($data[$field])) {
                throw ValidationException::withMessages([
                    $field => "The {$field} field is required."
                ]);
            }
        }

        return $data;
    }
}