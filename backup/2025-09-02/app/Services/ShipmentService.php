<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Shipment;
use App\Models\Customer;
use App\Models\CustomerUser;
use App\Models\CourierApiLog;
use App\Services\Courier\CourierServiceFactory;
use App\Services\Notification\NotificationService;
use App\Events\ShipmentCreated;
use App\Events\ShipmentStatusUpdated;
use App\Exceptions\CourierServiceException;
use Illuminate\Support\Str;

class ShipmentService
{
    public function __construct(
        private CourierServiceFactory $courierFactory,
        private NotificationService $notificationService
    ) {}

    public function createShipment(array $data, Customer $customer, CustomerUser $user = null, bool $autoDeductBalance = true): Shipment
    {
        // Validate customer can create shipment
        if (!$customer->canCreateShipment()) {
            throw new \Exception('Customer cannot create shipments. Please check account status.');
        }

        $courierService = $this->courierFactory->makeByCode($data['courier_code']);
        
        $shipment = Shipment::create([
            'customer_id' => $customer->id,
            'customer_user_id' => $user?->id,
            'courier_service_id' => $courierService->getId(),
            'status' => 'created',
            'service_type' => $data['service_type'],
            'sender_data' => $data['sender'],
            'recipient_data' => $data['recipient'],
            'package_data' => $data['package'],
            'cod_amount' => $data['cod_amount'] ?? null,
            'insurance_amount' => $data['insurance_amount'] ?? null,
            'reference_number' => $data['reference_number'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        try {
            // Create shipment with courier
            $courierResponse = $courierService->createShipment($data);
            
            $shipment->update([
                'tracking_number' => $courierResponse['tracking_number'],
                'external_id' => $courierResponse['external_id'],
                'cost_data' => $courierResponse['cost'],
                'label_url' => $courierResponse['label_url'] ?? null,
                'status' => 'printed',
            ]);

            // Deduct cost from customer balance if configured
            if ($autoDeductBalance && $customer->current_balance >= $courierResponse['cost']['gross']) {
                $customer->deductBalance(
                    $courierResponse['cost']['gross'], 
                    "Shipment cost: {$shipment->tracking_number}"
                );
            }

            event(new ShipmentCreated($shipment));
            
        } catch (CourierServiceException $e) {
            $shipment->update([
                'status' => 'failed',
                'notes' => ($shipment->notes ? $shipment->notes . "\n" : '') . 'Error: ' . $e->getMessage()
            ]);
            throw $e;
        }

        return $shipment->fresh();
    }

    public function processInPostShipment(Shipment $shipment): void
    {
        // Skip if already processed (has tracking number)
        if ($shipment->tracking_number) {
            return;
        }

        // Get courier service
        $courierService = $this->courierFactory->makeById($shipment->courier_service_id);
        
        // Prepare data for InPost API
        $data = [
            'service_type' => $shipment->service_type,
            'sender' => $shipment->sender_data,
            'recipient' => $shipment->recipient_data,
            'package' => $shipment->package_data,
            'cod_amount' => $shipment->cod_amount,
            'insurance_amount' => $shipment->insurance_amount,
            'reference_number' => $shipment->reference_number,
            'notes' => $shipment->notes,
            'additional_services' => $shipment->additional_services ?? []
        ];

        try {
            // Create shipment with InPost
            $courierResponse = $courierService->createShipment($data);
            
            // Update shipment with InPost response
            $shipment->update([
                'tracking_number' => $courierResponse['tracking_number'],
                'external_id' => $courierResponse['external_id'],
                'cost_data' => $courierResponse['cost'],
                'label_url' => $courierResponse['label_url'] ?? null,
                'status' => 'printed', // Update status to printed after successful InPost creation
            ]);

            // Log successful creation
            CourierApiLog::create([
                'shipment_id' => $shipment->id,
                'courier_service' => 'inpost',
                'action' => 'create_shipment',
                'request_data' => $data,
                'response_data' => $courierResponse,
                'status' => 'success',
            ]);

            // TODO: Send notification about successful shipment creation
            // $this->notificationService->sendShipmentNotification(...);

        } catch (CourierServiceException $e) {
            // Log error
            CourierApiLog::create([
                'shipment_id' => $shipment->id,
                'courier_service' => 'inpost',
                'action' => 'create_shipment',
                'request_data' => $data,
                'response_data' => null,
                'status' => 'error',
                'error_message' => $e->getMessage(),
            ]);

            // Update shipment status to failed
            $shipment->update([
                'status' => 'failed',
                'notes' => ($shipment->notes ? $shipment->notes . "\n" : '') . 'InPost Error: ' . $e->getMessage()
            ]);

            throw $e;
        }
    }

    public function trackShipment(string $trackingNumber): array
    {
        $shipment = Shipment::where('tracking_number', $trackingNumber)->firstOrFail();
        $courierService = $this->courierFactory->makeById($shipment->courier_service_id);
        
        $tracking = $courierService->trackShipment($trackingNumber);
        
        // Update shipment status if changed
        $newStatus = $this->mapCourierStatusToShipmentStatus($tracking['status']);
        if ($newStatus !== $shipment->status) {
            $oldStatus = $shipment->status;
            $shipment->update([
                'status' => $newStatus,
                'tracking_events' => $tracking['events'],
                'delivered_at' => $newStatus === 'delivered' ? now() : $shipment->delivered_at,
            ]);

            event(new ShipmentStatusUpdated($shipment, $oldStatus));
        }
        
        return $tracking;
    }

    public function cancelShipment(Shipment $shipment): bool
    {
        if (!$shipment->canBeCancelled()) {
            throw new \Exception('Shipment cannot be cancelled in current status');
        }

        $courierService = $this->courierFactory->makeById($shipment->courier_service_id);
        
        try {
            $cancelled = $courierService->cancelShipment($shipment->tracking_number);
            
            if ($cancelled) {
                $oldStatus = $shipment->status;
                $shipment->update(['status' => 'cancelled']);
                
                // Refund cost to customer if already deducted
                if ($shipment->cost_data && $shipment->cost_data['gross'] > 0) {
                    $shipment->customer->addBalance(
                        $shipment->cost_data['gross'],
                        "Refund for cancelled shipment: {$shipment->tracking_number}"
                    );
                }

                event(new ShipmentStatusUpdated($shipment, $oldStatus));
            }
            
            return $cancelled;
        } catch (CourierServiceException $e) {
            throw new \Exception('Failed to cancel shipment: ' . $e->getMessage());
        }
    }

    public function getLabel(string $identifier, ?string $format = null, ?string $size = null): string
    {
        // Try to find shipment by tracking_number first, then by external_id
        $shipment = Shipment::where('tracking_number', $identifier)
            ->orWhere('external_id', $identifier)
            ->firstOrFail();
            
        $courierService = $this->courierFactory->makeById($shipment->courier_service_id);
        
        // Use tracking_number if available, otherwise use external_id
        $labelIdentifier = $shipment->tracking_number ?: $shipment->external_id;
        
        return $courierService->getLabel($labelIdentifier, $format, $size);
    }

    public function getLabelUrl(string $trackingNumber): string
    {
        $shipment = Shipment::where('tracking_number', $trackingNumber)->firstOrFail();
        
        if ($shipment->label_url) {
            return $shipment->label_url;
        }

        // Generate temporary URL for label download
        return route('customer.shipments.label', $shipment);
    }

    public function getShipmentByUuid(string $uuid): Shipment
    {
        return Shipment::where('uuid', $uuid)
            ->with(['customer', 'customerUser', 'courierService'])
            ->firstOrFail();
    }

    public function handleTrackingWebhook(string $courier, array $data): void
    {
        try {
            $courierService = $this->courierFactory->makeByCode($courier);
            $webhookData = $courierService->handleTrackingWebhook($data);
            
            if (!$webhookData['tracking_number']) {
                return;
            }

            $shipment = Shipment::where('tracking_number', $webhookData['tracking_number'])->first();
            
            if (!$shipment) {
                return;
            }

            $newStatus = $this->mapCourierStatusToShipmentStatus($webhookData['status']);
            
            if ($newStatus !== $shipment->status) {
                $oldStatus = $shipment->status;
                $shipment->update([
                    'status' => $newStatus,
                    'delivered_at' => $newStatus === 'delivered' ? now() : $shipment->delivered_at,
                ]);

                event(new ShipmentStatusUpdated($shipment, $oldStatus));
            }

        } catch (\Exception $e) {
            \Log::error('Tracking webhook processing failed', [
                'courier' => $courier,
                'error' => $e->getMessage(),
                'data' => $data
            ]);
        }
    }

    private function mapCourierStatusToShipmentStatus(string $courierStatus): string
    {
        return match(strtolower($courierStatus)) {
            'created', 'registered' => 'created',
            'printed', 'labeled' => 'printed',
            'collected', 'dispatched' => 'dispatched',
            'in_transit', 'on_the_way' => 'in_transit',
            'out_for_delivery' => 'out_for_delivery',
            'delivered' => 'delivered',
            'returned' => 'returned',
            'cancelled' => 'cancelled',
            default => 'created',
        };
    }
}