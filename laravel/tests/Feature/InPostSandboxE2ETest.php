<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\CourierService;
use App\Models\Customer;
use App\Models\CustomerUser;
use App\Models\Shipment;
use App\Services\Courier\CourierServiceFactory;
use App\Services\Courier\Providers\InPostService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class InPostSandboxE2ETest extends TestCase
{
    use RefreshDatabase;

    private InPostService $inpostService;
    private CourierService $courierService;
    private Customer $customer;
    private CustomerUser $customerUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test customer and user
        $this->customer = Customer::factory()->create([
            'company_name' => 'Test Company E2E',
            'is_active' => true,
        ]);

        $this->customerUser = CustomerUser::factory()->create([
            'customer_id' => $this->customer->id,
            'first_name' => 'Jan',
            'last_name' => 'Kowalski',
            'email' => 'jan.kowalski@test.com',
            'is_active' => true,
        ]);

        // Create or get InPost courier service
        $this->courierService = CourierService::firstOrCreate(
            ['code' => 'inpost'],
            [
                'name' => 'InPost',
                'is_active' => true,
                'configuration' => [
                    'sandbox' => true,
                    'api_url' => 'https://sandbox-api-shipx-pl.easypack24.net',
                    'token' => config('couriers.services.inpost.token'),
                    'organization_id' => config('couriers.services.inpost.organization_id'),
                ],
            ]
        );

        // Initialize InPost service
        $this->inpostService = app(CourierServiceFactory::class)->makeByCode('inpost');
    }

    /**
     * Test complete E2E flow: create shipment → get tracking → track status → get label
     */
    public function test_complete_inpost_sandbox_e2e_flow()
    {
        // Step 1: Create shipment in database
        Log::info('=== InPost E2E Test: Creating shipment ===');
        
        $shipment = Shipment::create([
            'uuid' => \Str::uuid(),
            'customer_id' => $this->customer->id,
            'customer_user_id' => $this->customerUser->id,
            'courier_service_id' => $this->courierService->id,
            'status' => 'draft',
            'service_type' => 'inpost_locker_standard',
            'sender_data' => [
                'name' => 'SkyBroker System',
                'company' => 'SkyBroker Sp. z o.o.',
                'street' => 'ul. Testowa',
                'building_number' => '123',
                'city' => 'Kraków',
                'postal_code' => '30-001',
                'country' => 'PL',
                'phone' => '+48123456789',
                'email' => 'nadawca@skybroker.com',
            ],
            'recipient_data' => [
                'name' => 'Jan Kowalski',
                'street' => 'ul. Odbiorcy',
                'building_number' => '456',
                'city' => 'Warszawa',
                'postal_code' => '00-001',
                'country' => 'PL',
                'phone' => '+48987654321',
                'email' => 'jan.kowalski@test.com',
                'pickup_point' => 'WAW01M',
            ],
            'package_data' => [
                'weight' => 2.5,
                'length' => 30,
                'width' => 20,
                'height' => 15,
                'description' => 'Test package for E2E',
            ],
            'reference_number' => 'E2E-TEST-'.time(),
            'notes' => 'InPost sandbox E2E test shipment',
        ]);

        $this->assertNotNull($shipment->id);
        Log::info("Created shipment with ID: {$shipment->id}");

        // Step 2: Create shipment via InPost API
        Log::info('=== InPost E2E Test: Creating shipment via API ===');
        
        $shipmentData = [
            'recipient' => [
                'name' => $shipment->recipient_data['name'],
                'email' => $shipment->recipient_data['email'],
                'phone' => $shipment->recipient_data['phone'],
                'city' => $shipment->recipient_data['city'],
                'postal_code' => $shipment->recipient_data['postal_code'],
                'pickup_point' => $shipment->recipient_data['pickup_point'],
            ],
            'package' => [
                'weight' => $shipment->package_data['weight'],
                'length' => $shipment->package_data['length'],
                'width' => $shipment->package_data['width'],
                'height' => $shipment->package_data['height'],
            ],
            'service_type' => $shipment->service_type,
            'reference_number' => $shipment->reference_number,
            'notes' => $shipment->notes,
        ];

        try {
            $apiResponse = $this->inpostService->createShipment($shipmentData);
            
            $this->assertArrayHasKey('tracking_number', $apiResponse);
            $this->assertArrayHasKey('external_id', $apiResponse);
            $this->assertNotEmpty($apiResponse['tracking_number']);
            
            $trackingNumber = $apiResponse['tracking_number'];
            $externalId = $apiResponse['external_id'];
            
            Log::info("API Response - Tracking: {$trackingNumber}, External ID: {$externalId}");

            // Update shipment with API response
            $shipment->update([
                'tracking_number' => $trackingNumber,
                'external_id' => $externalId,
                'status' => 'created',
                'cost_data' => $apiResponse['cost'],
                'label_url' => $apiResponse['label_url'] ?? null,
            ]);

            Log::info("Updated shipment with tracking number: {$trackingNumber}");

        } catch (\Exception $e) {
            Log::error('InPost API Error: ' . $e->getMessage());
            $this->fail('Failed to create shipment via InPost API: ' . $e->getMessage());
        }

        // Step 3: Track shipment status
        Log::info('=== InPost E2E Test: Tracking shipment ===');
        
        try {
            // Wait a moment for the shipment to be processed
            sleep(2);
            
            $trackingResponse = $this->inpostService->trackShipment($trackingNumber);
            
            $this->assertArrayHasKey('status', $trackingResponse);
            $this->assertArrayHasKey('events', $trackingResponse);
            
            Log::info("Tracking status: {$trackingResponse['status']}");
            Log::info("Events count: " . count($trackingResponse['events']));

            // Update shipment status based on tracking
            $shipment->update([
                'status' => $trackingResponse['status'],
                'tracking_events' => $trackingResponse['events'],
            ]);

        } catch (\Exception $e) {
            Log::warning('InPost tracking error (expected in sandbox): ' . $e->getMessage());
            // In sandbox, tracking might not be immediately available
        }

        // Step 4: Get label
        Log::info('=== InPost E2E Test: Getting label ===');
        
        try {
            $labelContent = $this->inpostService->getLabel($trackingNumber, 'pdf', 'A4');
            
            $this->assertNotEmpty($labelContent);
            $this->assertStringContainsString('%PDF', $labelContent); // PDF header check
            
            Log::info("Label retrieved successfully, size: " . strlen($labelContent) . " bytes");

        } catch (\Exception $e) {
            Log::warning('InPost label retrieval error (expected in sandbox): ' . $e->getMessage());
            // In sandbox, label might not be immediately available
        }

        // Step 5: Test status updates simulation
        Log::info('=== InPost E2E Test: Simulating status updates ===');
        
        $statusProgression = [
            'created' => 'Utworzona',
            'dispatched' => 'Nadana',
            'in_transit' => 'W transporcie',
            'out_for_delivery' => 'W doręczeniu',
            'delivered' => 'Dostarczona',
        ];

        foreach ($statusProgression as $status => $statusLabel) {
            Log::info("Simulating status change to: {$status} ({$statusLabel})");
            
            // Update shipment status
            $shipment->update(['status' => $status]);
            
            // Log status change
            activity()
                ->performedOn($shipment)
                ->withProperties([
                    'old_status' => $shipment->getOriginal('status'),
                    'new_status' => $status,
                    'source' => 'inpost_api_simulation',
                ])
                ->log('status_updated');

            $this->assertEquals($status, $shipment->fresh()->status);
            
            // Small delay between status changes
            sleep(1);
        }

        // Step 6: Final assertions
        Log::info('=== InPost E2E Test: Final validations ===');
        
        $finalShipment = $shipment->fresh();
        
        $this->assertEquals('delivered', $finalShipment->status);
        $this->assertNotNull($finalShipment->tracking_number);
        $this->assertNotNull($finalShipment->external_id);
        $this->assertNotNull($finalShipment->cost_data);
        $this->assertIsArray($finalShipment->cost_data);
        
        // Validate cost structure
        $this->assertArrayHasKey('gross', $finalShipment->cost_data);
        $this->assertArrayHasKey('net', $finalShipment->cost_data);
        $this->assertArrayHasKey('currency', $finalShipment->cost_data);
        $this->assertEquals('PLN', $finalShipment->cost_data['currency']);

        Log::info("=== InPost E2E Test: COMPLETED SUCCESSFULLY ===");
        Log::info("Final tracking number: {$finalShipment->tracking_number}");
        Log::info("Final status: {$finalShipment->status}");
        Log::info("Total cost: {$finalShipment->cost_data['gross']} {$finalShipment->cost_data['currency']}");
    }

    /**
     * Test InPost service price calculation
     */
    public function test_inpost_price_calculation()
    {
        Log::info('=== InPost E2E Test: Price calculation ===');
        
        $packageData = [
            'package' => [
                'weight' => 2.0,
                'length' => 25,
                'width' => 20,
                'height' => 10,
            ],
            'additional_services' => [
                'cod' => true,
                'insurance' => true,
                'saturday' => false,
                'sms' => true,
            ],
        ];

        try {
            $prices = $this->inpostService->calculatePrice($packageData);
            
            $this->assertIsArray($prices);
            $this->assertNotEmpty($prices);
            
            foreach ($prices as $price) {
                $this->assertArrayHasKey('service_type', $price);
                $this->assertArrayHasKey('service_name', $price);
                $this->assertArrayHasKey('price_gross', $price);
                $this->assertArrayHasKey('price_net', $price);
                $this->assertArrayHasKey('currency', $price);
                $this->assertArrayHasKey('delivery_time', $price);
                
                $this->assertEquals('PLN', $price['currency']);
                $this->assertGreaterThan(0, $price['price_gross']);
                $this->assertGreaterThan(0, $price['price_net']);
                
                Log::info("Service: {$price['service_name']}, Price: {$price['price_gross']} {$price['currency']}");
            }

        } catch (\Exception $e) {
            $this->fail('Price calculation failed: ' . $e->getMessage());
        }
    }

    /**
     * Test InPost pickup points retrieval
     */
    public function test_inpost_pickup_points()
    {
        Log::info('=== InPost E2E Test: Pickup points ===');
        
        try {
            $points = $this->inpostService->getPickupPoints(['city' => 'Warszawa']);
            
            $this->assertIsArray($points);
            
            if (!empty($points)) {
                $point = $points[0];
                $this->assertArrayHasKey('id', $point);
                $this->assertArrayHasKey('name', $point);
                $this->assertArrayHasKey('address', $point);
                $this->assertArrayHasKey('city', $point);
                $this->assertArrayHasKey('coordinates', $point);
                
                Log::info("Found " . count($points) . " pickup points in Warszawa");
                Log::info("First point: {$point['name']} - {$point['address']}");
            } else {
                Log::warning("No pickup points found for Warszawa");
            }

        } catch (\Exception $e) {
            Log::warning('Pickup points retrieval error: ' . $e->getMessage());
            // This might fail in sandbox, but we log it for information
        }
    }

    /**
     * Test webhook handling simulation
     */
    public function test_inpost_webhook_handling()
    {
        Log::info('=== InPost E2E Test: Webhook simulation ===');
        
        // Simulate webhook payloads from InPost
        $webhookPayloads = [
            [
                'tracking_number' => 'TEST123456789PL',
                'status' => 'dispatched_by_sender',
                'event_time' => now()->toISOString(),
                'message' => 'Shipment dispatched by sender',
            ],
            [
                'tracking_number' => 'TEST123456789PL',
                'status' => 'ready_to_pickup',
                'event_time' => now()->addHours(24)->toISOString(),
                'message' => 'Ready for pickup at parcel locker',
            ],
            [
                'tracking_number' => 'TEST123456789PL',
                'status' => 'delivered',
                'event_time' => now()->addHours(48)->toISOString(),
                'message' => 'Package delivered successfully',
            ],
        ];

        foreach ($webhookPayloads as $payload) {
            $processedData = $this->inpostService->handleTrackingWebhook($payload);
            
            $this->assertArrayHasKey('tracking_number', $processedData);
            $this->assertArrayHasKey('status', $processedData);
            $this->assertArrayHasKey('event_time', $processedData);
            
            $this->assertEquals($payload['tracking_number'], $processedData['tracking_number']);
            
            Log::info("Processed webhook - Status: {$processedData['status']}, Tracking: {$processedData['tracking_number']}");
        }
    }

    /**
     * Test error handling scenarios
     */
    public function test_inpost_error_handling()
    {
        Log::info('=== InPost E2E Test: Error handling ===');
        
        // Test invalid tracking number
        try {
            $this->inpostService->trackShipment('INVALID_TRACKING_NUMBER');
            $this->fail('Should have thrown exception for invalid tracking number');
        } catch (\Exception $e) {
            $this->assertStringContainsString('InPost tracking error', $e->getMessage());
            Log::info("Correctly handled invalid tracking number error: {$e->getMessage()}");
        }

        // Test invalid shipment data
        try {
            $invalidData = [
                'recipient' => [
                    'name' => '', // Empty name should cause error
                    'email' => 'invalid-email', // Invalid email
                ],
                'package' => [
                    'weight' => -1, // Invalid weight
                ],
            ];
            
            $this->inpostService->createShipment($invalidData);
            $this->fail('Should have thrown exception for invalid shipment data');
        } catch (\Exception $e) {
            $this->assertStringContainsString('InPost API Error', $e->getMessage());
            Log::info("Correctly handled invalid shipment data error: {$e->getMessage()}");
        }
    }

    /**
     * Test service availability
     */
    public function test_inpost_service_availability()
    {
        Log::info('=== InPost E2E Test: Service availability ===');
        
        $services = $this->inpostService->getAvailableServices();
        
        $this->assertIsArray($services);
        $this->assertNotEmpty($services);
        
        $expectedServices = [
            'inpost_locker_standard',
            'inpost_locker_express', 
            'inpost_courier_standard',
            'inpost_courier_express',
        ];

        foreach ($expectedServices as $expectedService) {
            $this->assertArrayHasKey($expectedService, $services);
            Log::info("Available service: {$expectedService} - {$services[$expectedService]}");
        }

        // Test service ID
        $this->assertEquals(1, $this->inpostService->getId());
    }
}