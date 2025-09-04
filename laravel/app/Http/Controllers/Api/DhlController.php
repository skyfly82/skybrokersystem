<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CalculatePriceRequest;
use App\Http\Requests\Api\CreateShipmentRequest;
use App\Services\Courier\CourierServiceFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DhlController extends Controller
{
    public function __construct(
        private CourierServiceFactory $courierFactory
    ) {}

    /**
     * Calculate shipping price for DHL
     */
    public function calculatePrice(CalculatePriceRequest $request): JsonResponse
    {
        try {
            $dhlService = $this->courierFactory->makeByCode('dhl');
            $result = $dhlService->calculatePrice($request->validated());

            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => 'Price calculated successfully',
            ]);

        } catch (\Exception $e) {
            Log::error('DHL price calculation failed', [
                'error' => $e->getMessage(),
                'data' => $request->validated(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Price calculation failed: '.$e->getMessage(),
            ], 422);
        }
    }

    /**
     * Create DHL shipment
     */
    public function createShipment(CreateShipmentRequest $request): JsonResponse
    {
        try {
            $dhlService = $this->courierFactory->makeByCode('dhl');
            $result = $dhlService->createShipment($request->validated());

            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => 'Shipment created successfully',
            ], 201);

        } catch (\Exception $e) {
            Log::error('DHL shipment creation failed', [
                'error' => $e->getMessage(),
                'data' => $request->validated(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Shipment creation failed: '.$e->getMessage(),
            ], 422);
        }
    }

    /**
     * Track DHL shipment
     */
    public function trackShipment(Request $request, string $trackingNumber): JsonResponse
    {
        try {
            $dhlService = $this->courierFactory->makeByCode('dhl');
            $result = $dhlService->trackShipment($trackingNumber);

            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => 'Tracking information retrieved successfully',
            ]);

        } catch (\Exception $e) {
            Log::error('DHL tracking failed', [
                'tracking_number' => $trackingNumber,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Tracking failed: '.$e->getMessage(),
            ], 422);
        }
    }

    /**
     * Cancel DHL shipment
     */
    public function cancelShipment(Request $request, string $trackingNumber): JsonResponse
    {
        try {
            $dhlService = $this->courierFactory->makeByCode('dhl');
            $result = $dhlService->cancelShipment($trackingNumber);

            return response()->json([
                'success' => true,
                'data' => ['cancelled' => $result],
                'message' => 'Shipment cancelled successfully',
            ]);

        } catch (\Exception $e) {
            Log::error('DHL cancellation failed', [
                'tracking_number' => $trackingNumber,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Cancellation failed: '.$e->getMessage(),
            ], 422);
        }
    }

    /**
     * Get DHL label
     */
    public function getLabel(Request $request, string $trackingNumber): JsonResponse
    {
        try {
            $format = $request->query('format', 'pdf');
            $size = $request->query('size', 'A4');

            $dhlService = $this->courierFactory->makeByCode('dhl');
            $labelData = $dhlService->getLabel($trackingNumber, $format, $size);

            $mimeType = $format === 'zpl' ? 'text/plain' : 'application/pdf';
            $extension = $format === 'zpl' ? 'zpl' : 'pdf';

            return response($labelData)
                ->header('Content-Type', $mimeType)
                ->header('Content-Disposition', "attachment; filename=\"dhl_label_{$trackingNumber}.{$extension}\"");

        } catch (\Exception $e) {
            Log::error('DHL label generation failed', [
                'tracking_number' => $trackingNumber,
                'format' => $request->query('format'),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Label generation failed: '.$e->getMessage(),
            ], 422);
        }
    }

    /**
     * Get available DHL services
     */
    public function getServices(): JsonResponse
    {
        try {
            $dhlService = $this->courierFactory->makeByCode('dhl');
            $services = $dhlService->getAvailableServices();

            return response()->json([
                'success' => true,
                'data' => $services,
                'message' => 'Services retrieved successfully',
            ]);

        } catch (\Exception $e) {
            Log::error('DHL services retrieval failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Services retrieval failed: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Handle DHL webhook
     */
    public function webhook(Request $request): JsonResponse
    {
        try {
            $dhlService = $this->courierFactory->makeByCode('dhl');
            $result = $dhlService->handleTrackingWebhook($request->all());

            Log::info('DHL webhook processed', [
                'data' => $request->all(),
                'result' => $result,
            ]);

            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => 'Webhook processed successfully',
            ]);

        } catch (\Exception $e) {
            Log::error('DHL webhook processing failed', [
                'data' => $request->all(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Webhook processing failed: '.$e->getMessage(),
            ], 422);
        }
    }
}
