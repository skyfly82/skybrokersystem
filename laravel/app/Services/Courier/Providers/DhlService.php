<?php

declare(strict_types=1);

namespace App\Services\Courier\Providers;

use App\Exceptions\CourierServiceException;
use App\Services\Courier\CourierServiceInterface;
use Exception;
use Illuminate\Support\Facades\Log;
use SoapClient;
use SoapFault;

class DhlService implements CourierServiceInterface
{
    private ?string $username;

    private ?string $password;

    private ?string $accountNumber;

    private string $wsdlUrl;

    private bool $sandbox;

    private SoapClient $soapClient;

    public function __construct()
    {
        $this->sandbox = config('skybrokersystem.couriers.dhl.sandbox', true);
        $this->username = config('skybrokersystem.couriers.dhl.username');
        $this->password = config('skybrokersystem.couriers.dhl.password');
        $this->accountNumber = config('skybrokersystem.couriers.dhl.account_number');

        $this->wsdlUrl = $this->sandbox
            ? 'https://sandbox.dhl24.com.pl/webapi2?wsdl'
            : 'https://dhl24.com.pl/webapi2?wsdl';

        $this->initializeSoapClient();
    }

    private function initializeSoapClient(): void
    {
        try {
            $this->soapClient = new SoapClient($this->wsdlUrl, [
                'trace' => true,
                'exceptions' => true,
                'cache_wsdl' => WSDL_CACHE_NONE,
                'connection_timeout' => 30,
                'stream_context' => stream_context_create([
                    'http' => [
                        'timeout' => 30,
                        'user_agent' => 'SkyBrokerSystem/1.0.0',
                    ],
                ]),
            ]);
        } catch (SoapFault $e) {
            Log::error('DHL SOAP Client initialization failed', [
                'error' => $e->getMessage(),
                'wsdl' => $this->wsdlUrl,
            ]);
            throw new CourierServiceException('DHL API connection failed: '.$e->getMessage());
        }
    }

    public function createShipment(array $data): array
    {
        try {
            $shipmentData = $this->prepareShipmentData($data);

            $response = $this->soapClient->createShipment([
                'authData' => $this->getAuthData(),
                'shipmentData' => $shipmentData,
            ]);

            if (! $response || ! isset($response->createShipmentResult)) {
                throw new CourierServiceException('Invalid DHL API response');
            }

            $result = $response->createShipmentResult;

            if (! $result->isSuccess) {
                $errorMessage = $result->errorMessage ?? 'Unknown DHL API error';
                throw new CourierServiceException("DHL shipment creation failed: {$errorMessage}");
            }

            return [
                'success' => true,
                'tracking_number' => $result->shipmentNotificationNumber,
                'label_url' => $result->labelUrl ?? null,
                'shipment_id' => $result->shipmentId ?? null,
                'cost' => $result->cost ?? null,
                'currency' => 'PLN',
                'estimated_delivery' => $result->estimatedDeliveryDate ?? null,
            ];

        } catch (SoapFault $e) {
            Log::error('DHL createShipment SOAP error', [
                'error' => $e->getMessage(),
                'request' => $this->soapClient->__getLastRequest(),
                'response' => $this->soapClient->__getLastResponse(),
            ]);
            throw new CourierServiceException('DHL API error: '.$e->getMessage());
        } catch (Exception $e) {
            Log::error('DHL createShipment error', ['error' => $e->getMessage()]);
            throw new CourierServiceException('DHL shipment creation failed: '.$e->getMessage());
        }
    }

    public function trackShipment(string $trackingNumber): array
    {
        try {
            $response = $this->soapClient->getTrackAndTraceInfo([
                'authData' => $this->getAuthData(),
                'shipmentNotificationNumber' => $trackingNumber,
            ]);

            if (! $response || ! isset($response->getTrackAndTraceInfoResult)) {
                throw new CourierServiceException('Invalid DHL tracking response');
            }

            $result = $response->getTrackAndTraceInfoResult;

            if (! $result->isSuccess) {
                throw new CourierServiceException('DHL tracking failed: '.($result->errorMessage ?? 'Unknown error'));
            }

            return [
                'success' => true,
                'tracking_number' => $trackingNumber,
                'status' => $this->mapDhlStatus($result->status ?? ''),
                'status_description' => $result->statusDescription ?? '',
                'events' => $this->mapTrackingEvents($result->events ?? []),
                'estimated_delivery' => $result->estimatedDeliveryDate ?? null,
                'delivered_at' => $result->deliveredAt ?? null,
            ];

        } catch (SoapFault $e) {
            Log::error('DHL tracking SOAP error', [
                'tracking_number' => $trackingNumber,
                'error' => $e->getMessage(),
            ]);
            throw new CourierServiceException('DHL tracking API error: '.$e->getMessage());
        }
    }

    public function cancelShipment(string $trackingNumber): bool
    {
        try {
            $response = $this->soapClient->deleteShipment([
                'authData' => $this->getAuthData(),
                'shipmentNotificationNumber' => $trackingNumber,
            ]);

            if (! $response || ! isset($response->deleteShipmentResult)) {
                throw new CourierServiceException('Invalid DHL cancellation response');
            }

            $result = $response->deleteShipmentResult;

            if (! $result->isSuccess) {
                throw new CourierServiceException('DHL cancellation failed: '.($result->errorMessage ?? 'Unknown error'));
            }

            return true;

        } catch (SoapFault $e) {
            Log::error('DHL cancellation SOAP error', [
                'tracking_number' => $trackingNumber,
                'error' => $e->getMessage(),
            ]);
            throw new CourierServiceException('DHL cancellation API error: '.$e->getMessage());
        }
    }

    public function getLabel(string $trackingNumber, ?string $format = null, ?string $size = null): string
    {
        try {
            $labelType = $this->mapLabelFormat($format);

            $response = $this->soapClient->getLabel([
                'authData' => $this->getAuthData(),
                'shipmentNotificationNumber' => $trackingNumber,
                'labelType' => $labelType,
            ]);

            if (! $response || ! isset($response->getLabelResult)) {
                throw new CourierServiceException('Invalid DHL label response');
            }

            $result = $response->getLabelResult;

            if (! $result->isSuccess) {
                throw new CourierServiceException('DHL label generation failed: '.($result->errorMessage ?? 'Unknown error'));
            }

            return base64_decode($result->labelContent);

        } catch (SoapFault $e) {
            Log::error('DHL label SOAP error', [
                'tracking_number' => $trackingNumber,
                'error' => $e->getMessage(),
            ]);
            throw new CourierServiceException('DHL label API error: '.$e->getMessage());
        }
    }

    public function calculatePrice(array $data): array
    {
        try {
            $shipmentData = $this->prepareShipmentData($data, true); // calculation mode

            $response = $this->soapClient->getCost([
                'authData' => $this->getAuthData(),
                'shipmentData' => $shipmentData,
            ]);

            if (! $response || ! isset($response->getCostResult)) {
                throw new CourierServiceException('Invalid DHL price calculation response');
            }

            $result = $response->getCostResult;

            if (! $result->isSuccess) {
                throw new CourierServiceException('DHL price calculation failed: '.($result->errorMessage ?? 'Unknown error'));
            }

            return [
                'success' => true,
                'total_cost' => $result->totalCost,
                'base_cost' => $result->baseCost ?? $result->totalCost,
                'fuel_surcharge' => $result->fuelSurcharge ?? 0,
                'additional_services' => $result->additionalServicesCost ?? 0,
                'currency' => 'PLN',
                'estimated_delivery' => $result->estimatedDeliveryDate ?? null,
            ];

        } catch (SoapFault $e) {
            Log::error('DHL price calculation SOAP error', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);
            throw new CourierServiceException('DHL price calculation API error: '.$e->getMessage());
        }
    }

    public function getAvailableServices(): array
    {
        return [
            'standard' => 'DHL Standard',
            'express' => 'DHL Express',
            'evening' => 'DHL Evening Delivery',
            'saturday' => 'DHL Saturday Delivery',
            'cod' => 'Cash on Delivery',
            'insurance' => 'Insurance',
            'pallet' => 'Pallet Service',
        ];
    }

    public function getId(): int
    {
        return 2; // DHL service ID
    }

    public function handleTrackingWebhook(array $data): array
    {
        // DHL webhook handling logic
        return [
            'success' => true,
            'tracking_number' => $data['shipmentNotificationNumber'] ?? '',
            'status' => $this->mapDhlStatus($data['status'] ?? ''),
            'event_time' => $data['eventTime'] ?? now(),
            'location' => $data['location'] ?? '',
        ];
    }

    private function getAuthData(): array
    {
        return [
            'username' => $this->username,
            'password' => $this->password,
        ];
    }

    private function prepareShipmentData(array $data, bool $calculationMode = false): array
    {
        $pieceDefinitions = [];

        foreach ($data['pieces'] ?? [] as $piece) {
            $pieceDefinitions[] = [
                'type' => $this->mapPackageType($piece['type'] ?? 'package'),
                'width' => (int) ($piece['width'] ?? 0),
                'height' => (int) ($piece['height'] ?? 0),
                'length' => (int) ($piece['length'] ?? 0),
                'weight' => (float) ($piece['weight'] ?? 0),
                'quantity' => (int) ($piece['quantity'] ?? 1),
                'nonStandard' => $piece['non_standard'] ?? false,
            ];
        }

        $shipmentData = [
            'shipmentInfo' => [
                'dropOffType' => $data['pickup_type'] ?? 'REGULAR_PICKUP',
                'serviceType' => $data['service_type'] ?? 'DHL_STANDARD',
                'billingAccountNumber' => $this->accountNumber,
                'paymentType' => 'SHIPPER',
                'labelType' => $data['label_format'] ?? 'BLP',
                'content' => $data['content_description'] ?? 'Goods',
                'comment' => $data['comment'] ?? '',
                'reference' => $data['reference'] ?? '',
            ],
            'shipper' => [
                'name' => $data['sender']['company_name'] ?? $data['sender']['name'],
                'postalCode' => $data['sender']['postal_code'],
                'city' => $data['sender']['city'],
                'street' => $data['sender']['street'],
                'houseNumber' => $data['sender']['house_number'] ?? '',
                'apartmentNumber' => $data['sender']['apartment_number'] ?? '',
                'contactPerson' => $data['sender']['contact_person'] ?? $data['sender']['name'],
                'contactPhone' => $data['sender']['phone'],
                'contactEmail' => $data['sender']['email'] ?? '',
            ],
            'receiver' => [
                'name' => $data['recipient']['company_name'] ?? $data['recipient']['name'],
                'postalCode' => $data['recipient']['postal_code'],
                'city' => $data['recipient']['city'],
                'street' => $data['recipient']['street'],
                'houseNumber' => $data['recipient']['house_number'] ?? '',
                'apartmentNumber' => $data['recipient']['apartment_number'] ?? '',
                'contactPerson' => $data['recipient']['contact_person'] ?? $data['recipient']['name'],
                'contactPhone' => $data['recipient']['phone'],
                'contactEmail' => $data['recipient']['email'] ?? '',
                'country' => $data['recipient']['country_code'] ?? 'PL',
            ],
            'pieceList' => $pieceDefinitions,
        ];

        // Add special services
        $specialServices = [];

        if (! empty($data['cod_amount'])) {
            $specialServices[] = [
                'serviceType' => 'COD',
                'serviceValue' => (float) $data['cod_amount'],
                'collectOnDeliveryForm' => $data['cod_payment_method'] ?? 'BANK_TRANSFER',
            ];

            // COD requires insurance
            $specialServices[] = [
                'serviceType' => 'UBEZP',
                'serviceValue' => (float) $data['cod_amount'],
            ];
        }

        if (! empty($data['insurance_amount'])) {
            $specialServices[] = [
                'serviceType' => 'UBEZP',
                'serviceValue' => (float) $data['insurance_amount'],
            ];
        }

        if (! empty($data['saturday_delivery'])) {
            $specialServices[] = [
                'serviceType' => 'SOBOTA',
                'serviceValue' => 0,
            ];
        }

        if (! empty($specialServices)) {
            $shipmentData['specialServices'] = $specialServices;
        }

        return $shipmentData;
    }

    private function mapPackageType(string $type): string
    {
        return match ($type) {
            'pallet' => 'PALLET',
            'envelope' => 'ENVELOPE',
            'package' => 'PACKAGE',
            default => 'PACKAGE'
        };
    }

    private function mapLabelFormat(?string $format): string
    {
        return match ($format) {
            'pdf' => 'BLP',
            'zpl' => 'BLP_ZPL',
            'a4' => 'LBLP',
            default => 'BLP'
        };
    }

    private function mapDhlStatus(string $dhlStatus): string
    {
        return match (strtoupper($dhlStatus)) {
            'CREATED', 'REGISTERED' => 'created',
            'COLLECTED', 'PICKED_UP' => 'picked_up',
            'IN_TRANSIT', 'SORTING', 'FORWARDED' => 'in_transit',
            'OUT_FOR_DELIVERY' => 'out_for_delivery',
            'DELIVERED' => 'delivered',
            'EXCEPTION', 'DAMAGED' => 'exception',
            'RETURNED' => 'returned',
            'CANCELLED' => 'cancelled',
            default => 'unknown'
        };
    }

    private function mapTrackingEvents(array $events): array
    {
        return array_map(function ($event) {
            return [
                'status' => $this->mapDhlStatus($event->status ?? ''),
                'description' => $event->description ?? '',
                'location' => $event->location ?? '',
                'timestamp' => $event->timestamp ?? null,
                'terminal' => $event->terminal ?? '',
            ];
        }, $events);
    }
}
