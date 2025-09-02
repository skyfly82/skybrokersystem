<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use App\Services\ShipmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class InPostWebhookController extends Controller
{
    public function __construct(
        private ShipmentService $shipmentService
    ) {}

    /**
     * Handle InPost tracking webhooks
     * 
     * Endpoint: POST /api/webhooks/inpost/tracking
     */
    public function trackingWebhook(Request $request): JsonResponse
    {
        $payload = $request->all();
        
        Log::info('InPost tracking webhook received', [
            'payload' => $payload,
            'headers' => $request->headers->all(),
            'ip' => $request->ip(),
        ]);

        try {
            // Validate webhook payload
            $this->validateInPostWebhook($payload);
            
            // Extract tracking data
            $trackingNumber = $payload['tracking_number'] ?? null;
            $status = $payload['status'] ?? null;
            $eventTime = $payload['event_time'] ?? now()->toISOString();
            
            if (!$trackingNumber || !$status) {
                Log::warning('InPost webhook missing required fields', $payload);
                return response()->json(['error' => 'Missing required fields'], 400);
            }

            // Find shipment by tracking number
            $shipment = Shipment::where('tracking_number', $trackingNumber)->first();
            
            if (!$shipment) {
                Log::warning("InPost webhook: shipment not found for tracking: {$trackingNumber}");
                return response()->json(['error' => 'Shipment not found'], 404);
            }

            // Map InPost status to our internal status
            $mappedStatus = $this->mapInPostStatus($status);
            $oldStatus = $shipment->status;

            // Update shipment if status changed
            if ($mappedStatus !== $oldStatus) {
                $updateData = [
                    'status' => $mappedStatus,
                    'delivered_at' => $mappedStatus === 'delivered' ? now() : $shipment->delivered_at,
                ];

                $shipment->update($updateData);

                // Add tracking event
                $trackingEvents = $shipment->tracking_events ?? [];
                $trackingEvents[] = [
                    'date' => $eventTime,
                    'status' => $status,
                    'description' => $payload['message'] ?? "Status changed to: {$status}",
                    'location' => $payload['origin_depot']['name'] ?? $payload['location'] ?? null,
                ];

                $shipment->update(['tracking_events' => $trackingEvents]);

                // Log activity
                activity()
                    ->performedOn($shipment)
                    ->withProperties([
                        'old_status' => $oldStatus,
                        'new_status' => $mappedStatus,
                        'inpost_status' => $status,
                        'webhook_data' => $payload,
                        'source' => 'inpost_webhook',
                    ])
                    ->log('webhook_status_update');

                Log::info("InPost webhook processed successfully", [
                    'tracking_number' => $trackingNumber,
                    'old_status' => $oldStatus,
                    'new_status' => $mappedStatus,
                    'inpost_status' => $status,
                ]);

                // TODO: Send notifications to customer
                // $this->notificationService->sendStatusUpdate($shipment, $oldStatus, $mappedStatus);

            } else {
                Log::info("InPost webhook: no status change needed", [
                    'tracking_number' => $trackingNumber,
                    'current_status' => $oldStatus,
                    'inpost_status' => $status,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Webhook processed successfully',
                'tracking_number' => $trackingNumber,
                'old_status' => $oldStatus,
                'new_status' => $mappedStatus,
            ]);

        } catch (\Exception $e) {
            Log::error('InPost webhook processing failed', [
                'error' => $e->getMessage(),
                'payload' => $payload,
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Webhook processing failed',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Handle InPost shipment creation webhooks
     * 
     * Endpoint: POST /api/webhooks/inpost/shipment
     */
    public function shipmentWebhook(Request $request): JsonResponse
    {
        $payload = $request->all();
        
        Log::info('InPost shipment webhook received', [
            'payload' => $payload,
            'ip' => $request->ip(),
        ]);

        try {
            $shipmentId = $payload['id'] ?? null;
            $trackingNumber = $payload['tracking_number'] ?? null;
            
            if (!$shipmentId || !$trackingNumber) {
                return response()->json(['error' => 'Missing required fields'], 400);
            }

            // Find shipment by external_id or tracking_number
            $shipment = Shipment::where('external_id', $shipmentId)
                ->orWhere('tracking_number', $trackingNumber)
                ->first();

            if (!$shipment) {
                Log::warning("InPost shipment webhook: shipment not found", [
                    'external_id' => $shipmentId,
                    'tracking_number' => $trackingNumber,
                ]);
                return response()->json(['error' => 'Shipment not found'], 404);
            }

            // Update shipment with additional data from webhook
            $updateData = [];
            
            if (isset($payload['label_url'])) {
                $updateData['label_url'] = $payload['label_url'];
            }
            
            if (isset($payload['calculated_charge_amount'])) {
                $currentCostData = $shipment->cost_data ?? [];
                $currentCostData['actual_cost'] = $payload['calculated_charge_amount'];
                $updateData['cost_data'] = $currentCostData;
            }

            if (!empty($updateData)) {
                $shipment->update($updateData);
                
                Log::info("InPost shipment webhook: updated shipment data", [
                    'tracking_number' => $trackingNumber,
                    'updates' => array_keys($updateData),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Shipment webhook processed successfully',
                'tracking_number' => $trackingNumber,
            ]);

        } catch (\Exception $e) {
            Log::error('InPost shipment webhook processing failed', [
                'error' => $e->getMessage(),
                'payload' => $payload,
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Webhook processing failed',
            ], 500);
        }
    }

    /**
     * Test endpoint for webhook functionality
     * 
     * Endpoint: POST /api/webhooks/inpost/test
     */
    public function testWebhook(Request $request): JsonResponse
    {
        Log::info('InPost webhook test endpoint called', [
            'payload' => $request->all(),
            'method' => $request->method(),
            'headers' => $request->headers->all(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'InPost webhook endpoint is working correctly',
            'timestamp' => now()->toISOString(),
            'received_data' => $request->all(),
        ]);
    }

    /**
     * Validate InPost webhook payload
     */
    private function validateInPostWebhook(array $payload): void
    {
        $requiredFields = ['tracking_number', 'status'];
        
        foreach ($requiredFields as $field) {
            if (!isset($payload[$field])) {
                throw new \InvalidArgumentException("Missing required field: {$field}");
            }
        }

        // Validate status is known InPost status
        $validStatuses = [
            'created', 'confirmed', 'dispatched_by_sender', 'collected_from_sender',
            'taken_by_courier', 'sent_from_source_branch', 'ready_to_pickup',
            'out_for_delivery', 'delivered', 'returned_to_sender', 'canceled'
        ];

        if (!in_array(strtolower($payload['status']), $validStatuses)) {
            Log::warning("Unknown InPost status received: {$payload['status']}");
        }
    }

    /**
     * Map InPost status to our internal status
     */
    private function mapInPostStatus(string $inpostStatus): string
    {
        return match (strtolower($inpostStatus)) {
            'created', 'confirmed' => 'created',
            'dispatched_by_sender', 'collected_from_sender' => 'dispatched',
            'taken_by_courier', 'sent_from_source_branch' => 'in_transit',
            'ready_to_pickup', 'out_for_delivery' => 'out_for_delivery',
            'delivered' => 'delivered',
            'returned_to_sender' => 'returned',
            'canceled' => 'cancelled',
            default => 'created',
        };
    }
}