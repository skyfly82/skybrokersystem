<?php

/**
 * Cel: Chudy kontroler API dla zarządzania zamówieniami
 * Moduł: Orders
 * Odpowiedzialny: Claude-Code
 * Data: 2025-09-02
 */

namespace App\Http\Controllers\Api\Orders;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Orders\StoreOrderRequest;
use App\Http\Requests\Api\Orders\UpdateOrderRequest;
use App\Models\Order;
use App\Services\Contracts\Orders\OrderServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderServiceInterface $orderService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $customerId = $request->user()->customer_id;
        $filters = $request->only(['status', 'date_from', 'date_to', 'per_page']);

        $orders = $this->orderService->getOrdersForCustomer($customerId, $filters);

        return response()->json([
            'data' => $orders->items(),
            'meta' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
            ],
        ]);
    }

    public function store(StoreOrderRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['customer_id'] = $request->user()->customer_id;
        $data['customer_user_id'] = $request->user()->id;

        $order = $this->orderService->createOrder($data);

        return response()->json([
            'message' => 'Order created successfully',
            'data' => $order,
        ], 201);
    }

    public function show(Request $request, Order $order): JsonResponse
    {
        $this->authorize('view', $order);

        return response()->json(['data' => $order->load(['shipments', 'payments'])]);
    }

    public function update(UpdateOrderRequest $request, Order $order): JsonResponse
    {
        // Authorization handled by Policy in the Request
        $updatedOrder = $this->orderService->updateOrder($order, $request->validated());

        return response()->json([
            'message' => 'Order updated successfully',
            'data' => $updatedOrder,
        ]);
    }

    public function destroy(Request $request, Order $order): JsonResponse
    {
        $this->authorize('delete', $order);
        $this->orderService->cancelOrder($order, $request->input('reason'));

        return response()->json([
            'message' => 'Order cancelled successfully',
        ]);
    }

    public function updateStatus(Request $request, Order $order): JsonResponse
    {
        $this->authorize('update', $order);
        $updatedOrder = $this->orderService->updateOrderStatus(
            $order,
            $request->input('status')
        );

        return response()->json([
            'message' => 'Order status updated successfully',
            'data' => $updatedOrder,
        ]);
    }

    public function cancel(Request $request, Order $order): JsonResponse
    {
        $this->authorize('delete', $order);
        $this->orderService->cancelOrder($order, $request->input('reason'));

        return response()->json([
            'message' => 'Order cancelled successfully',
        ]);
    }
}
