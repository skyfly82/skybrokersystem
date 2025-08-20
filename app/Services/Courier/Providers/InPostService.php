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
        $this->apiUrl = config('couriers.inpost.api_url');
        $this->token = config('couriers.inpost.token');
        $this->organizationId = config('couriers.inpost.organization_id');
    }

    public function createShipment(array $data): array
    {
        $payload = $this->buildShipmentPayload($data);
        
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Content-Type' => 'application/json'
        ])->post($this->apiUrl . '/v1/organizations/' . $this->organizationId . '/shipments', $payload);

        if (!$response->successful()) {
            throw new CourierServiceException('InPost API Error: ' . $response->body());
        }

        $responseData = $response->json();

        return [
            'tracking_number' => $responseData['tracking_number'],
            'external_id' => $responseData['id'],
            'cost' => [
                'net' => $responseData['calculated_charge_amount'] / 100,
                'gross' => ($responseData['calculated_charge_amount'] / 100) * 1.23,
                'currency' => 'PLN'
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

    public function getLabel(string $trackingNumber): string
    {
        $shipmentDetails = $this->getShipmentByTrackingNumber($trackingNumber);
        
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->get($this->apiUrl . '/v1/organizations/' . $this->organizationId . '/shipments/' . $shipmentDetails['id'] . '/label');

        if (!$response->successful()) {
            throw new CourierServiceException('InPost label retrieval error: ' . $response->body());
        }

        return $response->body();
    }

    public function calculatePrice(array $data): array
    {
        $services = $this->getAvailableServices();
        $basePrice = $this->calculateBasePrice($data);
        
        return collect($services)->map(function ($serviceName, $serviceCode) use ($basePrice) {
            $multiplier = match($serviceCode) {
                'inpost_locker_standard' => 1.0,
                'inpost_locker_express' => 1.5,
                'inpost_courier_standard' => 2.0,
                'inpost_courier_express' => 2.5,
                default => 1.0,
            };
            
            $price = $basePrice * $multiplier;
            
            return [
                'service_type' => $serviceCode,
                'service_name' => $serviceName,
                'price_net' => round($price, 2),
                'price_gross' => round($price * 1.23, 2),
                'currency' => 'PLN',
                'delivery_time' => match($serviceCode) {
                    'inpost_locker_express', 'inpost_courier_express' => '24h',
                    default => '48h',
                },
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
            return [
                'id' => $point['name'],
                'name' => $point['name'],
                'address' => $point['address']['line1'] . ', ' . $point['address']['line2'],
                'city' => $point['address']['city'],
                'postal_code' => $point['address']['post_code'],
                'coordinates' => [
                    'lat' => $point['location']['latitude'],
                    'lng' => $point['location']['longitude']
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
        $payload = [
            'receiver' => [
                'first_name' => $data['recipient']['first_name'] ?? $data['recipient']['name'],
                'last_name' => $data['recipient']['last_name'] ?? '',
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
            'service' => $data['service_type'],
            'reference' => $data['reference_number'] ?? null,
            'comments' => $data['notes'] ?? null,
        ];

        // Dodawanie adresu dla usług kurierskich
        if (str_contains($data['service_type'], 'courier')) {
            $payload['receiver']['address'] = [
                'street' => $data['recipient']['address'],
                'building_number' => $data['recipient']['building_number'] ?? '1',
                'city' => $data['recipient']['city'],
                'post_code' => $data['recipient']['postal_code'],
                'country_code' => $data['recipient']['country'] ?? 'PL'
            ];
        } else {
            // Dla paczkomatów
            $payload['receiver']['address'] = [
                'point' => $data['recipient']['pickup_point'] ?? $data['pickup_point']
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

        return $payload;
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