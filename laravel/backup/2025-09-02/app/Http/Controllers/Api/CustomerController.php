<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $customer = $request->user();

        return response()->json([
            'success' => true,
            'data' => new CustomerResource($customer),
        ]);
    }

    public function balance(Request $request): JsonResponse
    {
        $customer = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'current_balance' => $customer->current_balance,
                'credit_limit' => $customer->credit_limit,
                'available_balance' => $customer->current_balance + $customer->credit_limit,
                'currency' => 'PLN',
            ],
        ]);
    }

    public function transactions(Request $request): JsonResponse
    {
        $customer = $request->user();

        $transactions = $customer->transactions()
            ->latest()
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $transactions->items(),
            'meta' => [
                'current_page' => $transactions->currentPage(),
                'total' => $transactions->total(),
            ],
        ]);
    }

    public function stats(Request $request): JsonResponse
    {
        $customer = $request->user();

        $stats = [
            'total_shipments' => $customer->shipments()->count(),
            'this_month_shipments' => $customer->getMonthlyShipmentsCount(),
            'pending_shipments' => $customer->shipments()->whereIn('status', ['created', 'printed', 'dispatched'])->count(),
            'delivered_shipments' => $customer->shipments()->where('status', 'delivered')->count(),
            'total_spent' => $customer->payments()->where('status', 'completed')->sum('amount'),
            'current_balance' => $customer->current_balance,
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }
}
