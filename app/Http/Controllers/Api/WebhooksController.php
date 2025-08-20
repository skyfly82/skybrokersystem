<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Payment\PaymentService;
use App\Services\ShipmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhooksController extends Controller
{
    public function __construct(
        private PaymentService $paymentService,
        private ShipmentService $shipmentService
    ) {}

    public function paynow(Request $request): JsonResponse
    {
        try {
            Log::info('PayNow webhook received', $request->all());
            
            $this->paymentService->handleWebhook('paynow', $request->all());
            
            return response()->json(['status' => 'ok']);
        } catch (\Exception $e) {
            Log::error('PayNow webhook error: ' . $e->getMessage(), $request->all());
            return response()->json(['error' => 'Webhook processing failed'], 400);
        }
    }

    public function stripe(Request $request): JsonResponse
    {
        try {
            Log::info('Stripe webhook received', $request->all());
            
            $this->paymentService->handleWebhook('stripe', $request->all());
            
            return response()->json(['status' => 'ok']);
        } catch (\Exception $e) {
            Log::error('Stripe webhook error: ' . $e->getMessage(), $request->all());
            return response()->json(['error' => 'Webhook processing failed'], 400);
        }
    }

    public function inpost(Request $request): JsonResponse
    {
        try {
            Log::info('InPost webhook received', $request->all());
            
            // Handle InPost tracking status updates
            $this->shipmentService->handleTrackingWebhook('inpost', $request->all());
            
            return response()->json(['status' => 'ok']);
        } catch (\Exception $e) {
            Log::error('InPost webhook error: ' . $e->getMessage(), $request->all());
            return response()->json(['error' => 'Webhook processing failed'], 400);
        }
    }

    public function dhl(Request $request): JsonResponse
    {
        try {
            Log::info('DHL webhook received', $request->all());
            
            $this->shipmentService->handleTrackingWebhook('dhl', $request->all());
            
            return response()->json(['status' => 'ok']);
        } catch (\Exception $e) {
            Log::error('DHL webhook error: ' . $e->getMessage(), $request->all());
            return response()->json(['error' => 'Webhook processing failed'], 400);
        }
    }
}