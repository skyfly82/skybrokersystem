<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentResource;
use App\Models\Payment;
use App\Services\Payment\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentsController extends Controller
{
    public function __construct(
        private PaymentService $paymentService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $customer = $request->user();
        
        $payments = $customer->payments()
            ->when($request->status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->latest()
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => PaymentResource::collection($payments->items()),
            'meta' => [
                'current_page' => $payments->currentPage(),
                'total' => $payments->total(),
            ]
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'type' => 'required|in:topup,shipment',
            'method' => 'required|in:card,bank_transfer,blik,simulation'
        ]);

        try {
            $payment = $this->paymentService->createPayment(
                $request->user(),
                $request->amount,
                $request->type,
                $request->method
            );

            $result = $this->paymentService->processPayment($payment);

            return response()->json([
                'success' => true,
                'data' => new PaymentResource($payment),
                'payment_url' => $result['payment_url'] ?? null
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function show(Payment $payment): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new PaymentResource($payment)
        ]);
    }
}