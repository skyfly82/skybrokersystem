<?php

declare(strict_types=1);

namespace App\Services\Courier\Providers;

use App\Services\Courier\CourierServiceInterface;
use Illuminate\Support\Facades\Http;
use App\Exceptions\CourierServiceException;
use Carbon\Carbon;

class InPostService implements CourierServiceInterface
{
    private string $apiUrl;
    private string $token;
    private string $organizationId;

    public function __construct()
    {
        $config = config('couriers.services.inpost');
        $this->apiUrl = $config['sandbox'] 
            ? ($config['sandbox_api_url'] ?? $config['api_url']) 
            : $config['api_url'];
        $this->token = $config['token'] ?? '';
        $this->organizationId = $config['organization_id'] ?? '';
    }

    public function createShipment(array $data): array
    {
        $payload = $this->buildShipmentPayload($data);
        
        // Debug: log the payload (commented out for production)
        // \Log::info('InPost API Payload:', $payload);
        
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Content-Type' => 'application/json'
        ])->post($this->apiUrl . '/v1/organizations/' . $this->organizationId . '/shipments', $payload);

        if (!$response->successful()) {
            throw new CourierServiceException('InPost API Error: ' . $response->body());
        }

        $responseData = $response->json();
        
        // Debug: log the response (commented out for production)  
        // \Log::info('InPost API Response:', $responseData);

        // Calculate additional services fees
        $additionalServices = $data['additional_services'] ?? [];
        $saturdayFee = isset($additionalServices['saturday']) && $additionalServices['saturday'] ? 9.99 : 0;
        $codFee = isset($additionalServices['cod']) && $additionalServices['cod'] ? 3.50 : 0;
        $smsFee = isset($additionalServices['sms']) && $additionalServices['sms'] ? 0.50 : 0;
        $insuranceFee = isset($additionalServices['insurance']) && $additionalServices['insurance'] ? 2.00 : 0;
        
        // InPost doesn't provide calculated_charge_amount immediately, so we estimate
        $estimatedBasePrice = $this->calculateBasePrice([
            'package' => [
                'weight' => $responseData['parcels'][0]['weight']['amount'] ?? 2,
                'length' => $responseData['parcels'][0]['dimensions']['length'] ?? 20,
                'width' => $responseData['parcels'][0]['dimensions']['width'] ?? 15,
                'height' => $responseData['parcels'][0]['dimensions']['height'] ?? 10
            ]
        ]);
        
        $totalAdditionalFees = $saturdayFee + $codFee + $smsFee + $insuranceFee;
        $totalNet = $estimatedBasePrice + $totalAdditionalFees;
        $totalGross = $totalNet * 1.23;

        return [
            'tracking_number' => $responseData['tracking_number'] ?? $responseData['parcels'][0]['tracking_number'] ?? null,
            'external_id' => (string) $responseData['id'],
            'cost' => [
                'base_price' => $estimatedBasePrice,
                'saturday_fee' => $saturdayFee,
                'cod_fee' => $codFee,
                'sms_fee' => $smsFee,
                'insurance_fee' => $insuranceFee,
                'net' => round($totalNet, 2),
                'gross' => round($totalGross, 2),
                'currency' => 'PLN',
                'vat_rate' => 23
            ],
            'label_url' => $responseData['label_url'] ?? null
        ];
    }

    public function trackShipment(string $trackingNumber): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->get($this->apiUrl . '/v1/tracking/' . $trackingNumber);

        if (!$response->successful()) {
            throw new CourierServiceException('InPost tracking error: ' . $response->body());
        }

        return $this->transformTrackingData($response->json());
    }

    public function cancelShipment(string $trackingNumber): bool
    {
        $shipmentDetails = $this->getShipmentByTrackingNumber($trackingNumber);
        
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->delete($this->apiUrl . '/v1/organizations/' . $this->organizationId . '/shipments/' . $shipmentDetails['id']);

        return $response->successful();
    }

    public function getLabel(string $identifier, ?string $format = null, ?string $size = null): string
    {
        // If identifier is numeric, it's likely an external_id, otherwise it's tracking_number
        $shipmentDetails = is_numeric($identifier) 
            ? $this->getShipmentById($identifier)
            : $this->getShipmentByTrackingNumber($identifier);
        
        // Get format and size from configuration if not provided
        $format = $format ?? config('skybrokersystem.couriers.label_format', 'pdf');
        $size = $size ?? config('skybrokersystem.couriers.label_size', 'A4');
        
        // Build query parameters for label format - try minimal params first
        $queryParams = [];
        
        $url = $this->apiUrl . '/v1/organizations/' . $this->organizationId . '/shipments/' . $shipmentDetails['id'] . '/label';
        
        // Add query parameters if any
        if (!empty($queryParams)) {
            $url .= '?' . http_build_query($queryParams);
        }
        
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->get($url);

        if (!$response->successful()) {
            throw new CourierServiceException('InPost label retrieval error: ' . $response->body());
        }

        return $response->body();
    }

    public function calculatePrice(array $data): array
    {
        $services = $this->getAvailableServices();
        $basePrice = $this->calculateBasePrice($data);
        
        // Additional services pricing
        $additionalServices = $data['additional_services'] ?? [];
        $saturdayFee = isset($additionalServices['saturday']) && $additionalServices['saturday'] ? 9.99 : 0;
        $codFee = isset($additionalServices['cod']) && $additionalServices['cod'] ? 3.50 : 0;
        $smsFee = isset($additionalServices['sms']) && $additionalServices['sms'] ? 0.50 : 0;
        $insuranceFee = isset($additionalServices['insurance']) && $additionalServices['insurance'] ? 2.00 : 0;
        
        return collect($services)->map(function ($serviceName, $serviceCode) use ($basePrice, $saturdayFee, $codFee, $smsFee, $insuranceFee) {
            $multiplier = match($serviceCode) {
                'inpost_locker_standard' => 1.0,
                'inpost_locker_express' => 1.5,
                'inpost_courier_standard' => 2.0,
                'inpost_courier_express' => 2.5,
                default => 1.0,
            };
            
            $baseServicePrice = $basePrice * $multiplier;
            $totalPrice = $baseServicePrice + $saturdayFee + $codFee + $smsFee + $insuranceFee;
            
            return [
                'service_type' => $serviceCode,
                'service_name' => $serviceName,
                'price_net' => round($totalPrice / 1.23, 2),
                'price_gross' => round($totalPrice, 2),
                'currency' => 'PLN',
                'delivery_time' => match($serviceCode) {
                    'inpost_locker_express', 'inpost_courier_express' => '24h',
                    default => '48h',
                },
                'additional_fees' => [
                    'saturday' => $saturdayFee,
                    'cod' => $codFee,
                    'sms' => $smsFee,
                    'insurance' => $insuranceFee,
                ],
            ];
        })->values()->toArray();
    }

    public function getAvailableServices(): array
    {
        return [
            'inpost_locker_standard' => 'Paczkomat Standard',
            'inpost_locker_express' => 'Paczkomat Express',
            'inpost_courier_standard' => 'Kurier Standard',
            'inpost_courier_express' => 'Kurier Express',
        ];
    }

    public function getPickupPoints(array $data): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->get($this->apiUrl . '/v1/points', [
            'city' => $data['city'] ?? '',
            'type' => 'parcel_locker',
            'status' => 'operating',
            'page_size' => 50
        ]);

        if (!$response->successful()) {
            throw new CourierServiceException('InPost pickup points error: ' . $response->body());
        }

        $points = $response->json()['items'] ?? [];

        return collect($points)->map(function ($point) {
            $address = $point['address'] ?? [];
            return [
                'id' => $point['name'] ?? '',
                'name' => $point['name'] ?? '',
                'address' => ($address['line1'] ?? '') . ', ' . ($address['line2'] ?? ''),
                'city' => $address['city'] ?? '',
                'postal_code' => $address['post_code'] ?? '',
                'coordinates' => [
                    'lat' => $point['location']['latitude'] ?? 0,
                    'lng' => $point['location']['longitude'] ?? 0
                ],
                'opening_hours' => $point['opening_hours'] ?? '24/7',
                'functions' => $point['functions'] ?? [],
                'payment_available' => in_array('payment', $point['functions'] ?? []),
            ];
        })->toArray();
    }

    public function getId(): int
    {
        return 1; // ID z tabeli courier_services
    }

    public function handleTrackingWebhook(array $data): array
    {
        // InPost webhook format
        return [
            'tracking_number' => $data['tracking_number'] ?? null,
            'status' => $data['status'] ?? null,
            'event_time' => $data['event_time'] ?? now()->toISOString(),
        ];
    }

    private function buildShipmentPayload(array $data): array
    {
        // Split name into first_name and last_name
        $fullName = $data['recipient']['name'] ?? '';
        $nameParts = explode(' ', $fullName, 2);
        $firstName = $nameParts[0] ?? 'Unknown';
        $lastName = $nameParts[1] ?? '';

        $payload = [
            'receiver' => [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $data['recipient']['email'],
                'phone' => $data['recipient']['phone']
            ],
            'parcels' => [
                [
                    'dimensions' => [
                        'length' => (string) ($data['package']['length'] ?? 20),
                        'width' => (string) ($data['package']['width'] ?? 15),
                        'height' => (string) ($data['package']['height'] ?? 10)
                    ],
                    'weight' => [
                        'amount' => (string) ($data['package']['weight'] ?? 1),
                        'unit' => 'kg'
                    ]
                ]
            ],
            'service' => $this->mapServiceType($data['service_type']),
            'reference' => $data['reference_number'] ?? null,
            'comments' => $data['notes'] ?? null,
        ];

        // Dodawanie adresu dla usług kurierskich
        if (str_contains($data['service_type'], 'courier') || str_contains($data['service_type'], 'kurier')) {
            // Parse address - if we have separate fields use them, otherwise parse combined address
            if (isset($data['recipient']['street']) && isset($data['recipient']['building_number'])) {
                $street = $data['recipient']['street'];
                $buildingNumber = $data['recipient']['building_number'];
                $apartmentNumber = $data['recipient']['apartment_number'] ?? null;
            } else {
                // Parse combined address like "ul. Kwiatowa 15/2"
                $fullAddress = $data['recipient']['address'] ?? '';
                $addressParts = explode(' ', $fullAddress);
                $buildingNumber = array_pop($addressParts);
                $street = implode(' ', $addressParts);
                $apartmentNumber = null;
                
                // Handle apartment numbers in format "15/2"
                if (strpos($buildingNumber, '/') !== false) {
                    [$buildingNumber, $apartmentNumber] = explode('/', $buildingNumber, 2);
                }
            }

            $payload['receiver']['address'] = [
                'street' => $street,
                'building_number' => $buildingNumber ?: '1',
                'city' => $data['recipient']['city'],
                'post_code' => $data['recipient']['postal_code'],
                'country_code' => $data['recipient']['country'] ?? 'PL'
            ];
            
            if ($apartmentNumber) {
                $payload['receiver']['address']['apartment_number'] = $apartmentNumber;
            }
        } else {
            // Dla paczkomatów
            $payload['receiver']['address'] = [
                'point' => $data['recipient']['pickup_point'] ?? $data['pickup_point'] ?? 'KRA010'
            ];
        }

        // COD
        if (isset($data['cod_amount']) && $data['cod_amount'] > 0) {
            $payload['cod'] = [
                'amount' => $data['cod_amount'] * 100, // InPost expects amount in grosz
                'currency' => 'PLN'
            ];
        }

        // Ubezpieczenie
        if (isset($data['insurance_amount']) && $data['insurance_amount'] > 0) {
            $payload['insurance'] = [
                'amount' => $data['insurance_amount'] * 100,
                'currency' => 'PLN'
            ];
        }

        // Dodatkowe usługi
        $additionalServices = $data['additional_services'] ?? [];
        
        // Weekend delivery
        if (isset($additionalServices['saturday']) && $additionalServices['saturday']) {
            $payload['only_choice_of_offer_service'] = true;
            $payload['additional_services'][] = 'weekend_delivery';
        }
        
        // SMS notification
        if (isset($additionalServices['sms']) && $additionalServices['sms']) {
            $payload['additional_services'][] = 'sms';
        }
        
        // Fragile package
        if (isset($additionalServices['fragile']) && $additionalServices['fragile']) {
            $payload['additional_services'][] = 'fragile';
        }

        return $payload;
    }

    private function mapServiceType(string $serviceType): string
    {
        return match($serviceType) {
            'inpost_locker_standard' => 'inpost_locker_standard',
            'inpost_locker_express' => 'inpost_locker_express', 
            'inpost_courier_standard', 'inpost_kurier_standard' => 'inpost_courier_standard',
            'inpost_courier_express', 'inpost_kurier_express' => 'inpost_courier_express',
            default => 'inpost_locker_standard',
        };
    }

    private function transformTrackingData(array $data): array
    {
        return [
            'status' => $this->mapStatus($data['status']),
            'events' => collect($data['tracking_details'] ?? [])->map(function ($event) {
                return [
                    'date' => $event['datetime'],
                    'status' => $event['status'],
                    'description' => $event['message'],
                    'location' => $event['origin_depot']['name'] ?? null
                ];
            })->toArray()
        ];
    }

    private function mapStatus(string $inpostStatus): string
    {
        return match(strtolower($inpostStatus)) {
            'created', 'confirmed' => 'created',
            'dispatched_by_sender' => 'dispatched',
            'collected_from_sender' => 'dispatched',
            'taken_by_courier', 'sent_from_source_branch' => 'in_transit',
            'ready_to_pickup', 'out_for_delivery' => 'out_for_delivery',
            'delivered' => 'delivered',
            'returned_to_sender' => 'returned',
            'canceled' => 'cancelled',
            default => 'created',
        };
    }

    private function getShipmentByTrackingNumber(string $trackingNumber): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->get($this->apiUrl . '/v1/organizations/' . $this->organizationId . '/shipments', [
            'tracking_number' => $trackingNumber
        ]);

        if (!$response->successful()) {
            throw new CourierServiceException('InPost shipment details error: ' . $response->body());
        }

        $shipments = $response->json()['items'] ?? [];
        
        if (empty($shipments)) {
            throw new CourierServiceException('Shipment not found');
        }

        return $shipments[0];
    }

    private function getShipmentById(string $shipmentId): array
    {
        // Try direct shipment endpoint first
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->get($this->apiUrl . '/v1/shipments/' . $shipmentId);

        if (!$response->successful()) {
            throw new CourierServiceException('InPost shipment details error: ' . $response->body());
        }

        return $response->json();
    }

    private function calculateBasePrice(array $data): float
    {
        $weight = $data['package']['weight'] ?? 1;
        $volume = ($data['package']['length'] ?? 20) * 
                  ($data['package']['width'] ?? 15) * 
                  ($data['package']['height'] ?? 10) / 1000000; // m3
        
        $basePrice = 15.00; // Cena bazowa
        
        // Dopłata za wagę
        if ($weight > 1) {
            $basePrice += ($weight - 1) * 2.50;
        }
        
        // Dopłata za objętość
        if ($volume > 0.01) { // 10L
            $basePrice += ($volume - 0.01) * 100;
        }
        
        return round($basePrice, 2);
    }
}