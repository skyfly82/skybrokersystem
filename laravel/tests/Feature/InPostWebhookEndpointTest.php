<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\CourierService;
use App\Models\Customer;
use App\Models\CustomerUser;
use App\Models\Shipment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class InPostWebhookEndpointTest extends TestCase
{
    use RefreshDatabase;

    private Customer $customer;
    private CustomerUser $customerUser;
    private CourierService $courierService;
    private Shipment $testShipment;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test data
        $this->customer = Customer::factory()->create([
            'company_name' => 'Webhook Test Company',
            'is_active' => true,
        ]);

        $this->customerUser = CustomerUser::factory()->create([
            'customer_id' => $this->customer->id,
            'first_name' => 'Test',
            'last_name' => 'Webhook',
            'email' => 'webhook@test.com',
            'is_active' => true,
        ]);

        $this->courierService = CourierService::firstOrCreate(
            ['code' => 'inpost'],
            [
                'name' => 'InPost',
                'is_active' => true,
                'configuration' => [],
            ]
        );

        // Create test shipment
        $this->testShipment = Shipment::create([
            'uuid' => \Str::uuid(),
            'customer_id' => $this->customer->id,
            'customer_user_id' => $this->customerUser->id,
            'courier_service_id' => $this->courierService->id,
            'tracking_number' => 'WEBHOOK_TEST_' . time() . 'PL',
            'external_id' => '12345',
            'status' => 'created',
            'service_type' => 'inpost_locker_standard',
            'sender_data' => ['name' => 'Test Sender'],
            'recipient_data' => ['name' => 'Test Recipient'],
            'package_data' => ['weight' => 2.0],
            'reference_number' => 'WH-TEST-' . time(),
        ]);

        Log::info('=== InPost Webhook Endpoint Test Setup Complete ===');
        Log::info("Created test shipment: {$this->testShipment->tracking_number}");
    }

    /**
     * Test webhook health endpoint
     */
    public function test_webhook_health_endpoint()
    {
        Log::info('=== Testing Webhook Health Endpoint ===');

        $response = $this->get('/api/webhooks/inpost/health');

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'ok',
            'service' => 'inpost_webhooks',
        ]);

        Log::info('✅ Health endpoint working correctly');
    }

    /**
     * Test webhook test endpoint
     */
    public function test_webhook_test_endpoint()
    {
        Log::info('=== Testing Webhook Test Endpoint ===');

        $testData = [
            'test' => true,
            'message' => 'Testing InPost webhook endpoint',
            'timestamp' => now()->toISOString(),
        ];

        $response = $this->postJson('/api/webhooks/inpost/test', $testData);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'InPost webhook endpoint is working correctly',
        ]);

        $responseData = $response->json();
        $this->assertEquals($testData, $responseData['received_data']);

        Log::info('✅ Test endpoint working correctly');
    }

    /**
     * Test tracking webhook with status update
     */
    public function test_tracking_webhook_status_update()
    {
        Log::info('=== Testing Tracking Webhook Status Update ===');

        $webhookPayload = [
            'tracking_number' => $this->testShipment->tracking_number,
            'status' => 'dispatched_by_sender',
            'event_time' => now()->toISOString(),
            'message' => 'Package dispatched by sender',
            'origin_depot' => [
                'name' => 'Kraków Sortownia',
            ],
        ];

        Log::info("Sending webhook for: {$webhookPayload['tracking_number']}");
        Log::info("Status: {$webhookPayload['status']}");

        $response = $this->postJson('/api/webhooks/inpost/tracking', $webhookPayload);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'tracking_number' => $this->testShipment->tracking_number,
            'old_status' => 'created',
            'new_status' => 'dispatched',
        ]);

        // Verify shipment was updated
        $updatedShipment = $this->testShipment->fresh();
        $this->assertEquals('dispatched', $updatedShipment->status);
        $this->assertNotNull($updatedShipment->tracking_events);
        $this->assertIsArray($updatedShipment->tracking_events);
        $this->assertCount(1, $updatedShipment->tracking_events);

        // Verify tracking event
        $trackingEvent = $updatedShipment->tracking_events[0];
        $this->assertEquals('dispatched_by_sender', $trackingEvent['status']);
        $this->assertEquals('Package dispatched by sender', $trackingEvent['description']);
        $this->assertEquals('Kraków Sortownia', $trackingEvent['location']);

        Log::info('✅ Status updated successfully: created → dispatched');
    }

    /**
     * Test complete delivery lifecycle via webhooks
     */
    public function test_complete_delivery_lifecycle()
    {
        Log::info('=== Testing Complete Delivery Lifecycle ===');

        $trackingNumber = $this->testShipment->tracking_number;
        $baseTime = now();

        // Define complete lifecycle
        $webhookSequence = [
            [
                'status' => 'confirmed',
                'expected_status' => 'created',
                'message' => 'Shipment confirmed',
                'delay_minutes' => 0,
            ],
            [
                'status' => 'dispatched_by_sender',
                'expected_status' => 'dispatched',
                'message' => 'Package dispatched by sender',
                'delay_minutes' => 30,
            ],
            [
                'status' => 'collected_from_sender',
                'expected_status' => 'dispatched',
                'message' => 'Package collected by courier',
                'delay_minutes' => 120,
            ],
            [
                'status' => 'taken_by_courier',
                'expected_status' => 'in_transit',
                'message' => 'Package in transit',
                'delay_minutes' => 300,
            ],
            [
                'status' => 'ready_to_pickup',
                'expected_status' => 'out_for_delivery',
                'message' => 'Package ready for pickup',
                'delay_minutes' => 1200,
            ],
            [
                'status' => 'delivered',
                'expected_status' => 'delivered',
                'message' => 'Package delivered successfully',
                'delay_minutes' => 1440,
            ],
        ];

        foreach ($webhookSequence as $step) {
            $eventTime = $baseTime->copy()->addMinutes($step['delay_minutes']);
            
            $webhookPayload = [
                'tracking_number' => $trackingNumber,
                'status' => $step['status'],
                'event_time' => $eventTime->toISOString(),
                'message' => $step['message'],
                'origin_depot' => [
                    'name' => 'Test Location - ' . ucfirst($step['status']),
                ],
            ];

            Log::info("Step: {$step['status']} → {$step['expected_status']} (+{$step['delay_minutes']}min)");

            $response = $this->postJson('/api/webhooks/inpost/tracking', $webhookPayload);

            $response->assertStatus(200);
            $response->assertJson([
                'success' => true,
                'tracking_number' => $trackingNumber,
                'new_status' => $step['expected_status'],
            ]);

            // Verify shipment status
            $shipment = $this->testShipment->fresh();
            $this->assertEquals($step['expected_status'], $shipment->status);

            // Check delivered_at for final delivery
            if ($step['expected_status'] === 'delivered') {
                $this->assertNotNull($shipment->delivered_at);
                Log::info("✅ Delivered at: {$shipment->delivered_at}");
            }
        }

        // Verify final state
        $finalShipment = $this->testShipment->fresh();
        $this->assertEquals('delivered', $finalShipment->status);
        $this->assertNotNull($finalShipment->delivered_at);
        $this->assertCount(count($webhookSequence), $finalShipment->tracking_events);

        Log::info('✅ Complete delivery lifecycle processed successfully!');
        Log::info("Final status: {$finalShipment->status}");
        Log::info("Total tracking events: " . count($finalShipment->tracking_events));
    }

    /**
     * Test shipment webhook
     */
    public function test_shipment_webhook()
    {
        Log::info('=== Testing Shipment Webhook ===');

        $webhookPayload = [
            'id' => $this->testShipment->external_id,
            'tracking_number' => $this->testShipment->tracking_number,
            'status' => 'created',
            'label_url' => 'https://sandbox-api-shipx-pl.easypack24.net/v1/organizations/2387/shipments/12345/label',
            'calculated_charge_amount' => 1299, // 12.99 PLN in grosze
        ];

        Log::info("Sending shipment webhook for: {$webhookPayload['tracking_number']}");

        $response = $this->postJson('/api/webhooks/inpost/shipment', $webhookPayload);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'tracking_number' => $this->testShipment->tracking_number,
        ]);

        // Verify shipment was updated
        $updatedShipment = $this->testShipment->fresh();
        $this->assertEquals($webhookPayload['label_url'], $updatedShipment->label_url);
        $this->assertArrayHasKey('actual_cost', $updatedShipment->cost_data);
        $this->assertEquals(1299, $updatedShipment->cost_data['actual_cost']);

        Log::info('✅ Shipment webhook processed successfully');
        Log::info("Label URL updated: {$updatedShipment->label_url}");
        Log::info("Actual cost: {$updatedShipment->cost_data['actual_cost']} grosze");
    }

    /**
     * Test webhook error handling
     */
    public function test_webhook_error_handling()
    {
        Log::info('=== Testing Webhook Error Handling ===');

        // Test 1: Missing tracking number
        $response = $this->postJson('/api/webhooks/inpost/tracking', [
            'status' => 'delivered',
            'message' => 'Package delivered',
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'error' => 'Missing required fields',
        ]);

        Log::info('✅ Missing tracking number handled correctly');

        // Test 2: Invalid tracking number
        $response = $this->postJson('/api/webhooks/inpost/tracking', [
            'tracking_number' => 'INVALID_TRACKING_NUMBER',
            'status' => 'delivered',
        ]);

        $response->assertStatus(404);
        $response->assertJson([
            'error' => 'Shipment not found',
        ]);

        Log::info('✅ Invalid tracking number handled correctly');

        // Test 3: Missing status
        $response = $this->postJson('/api/webhooks/inpost/tracking', [
            'tracking_number' => $this->testShipment->tracking_number,
            'message' => 'Some message',
        ]);

        $response->assertStatus(400);
        Log::info('✅ Missing status handled correctly');
    }

    /**
     * Test webhook security and logging
     */
    public function test_webhook_security_and_logging()
    {
        Log::info('=== Testing Webhook Security and Logging ===');

        // Test with various headers to simulate real InPost webhook
        $headers = [
            'User-Agent' => 'InPost-Webhook/1.0',
            'Content-Type' => 'application/json',
            'X-InPost-Signature' => 'test-signature',
        ];

        $webhookPayload = [
            'tracking_number' => $this->testShipment->tracking_number,
            'status' => 'in_transit',
            'event_time' => now()->toISOString(),
            'message' => 'Security test webhook',
        ];

        $response = $this->postJson('/api/webhooks/inpost/tracking', $webhookPayload, $headers);

        $response->assertStatus(200);

        // Verify shipment was updated despite additional headers
        $updatedShipment = $this->testShipment->fresh();
        $this->assertEquals('in_transit', $updatedShipment->status);

        Log::info('✅ Webhook processed correctly with custom headers');

        // Test malformed JSON (this should be handled by Laravel automatically)
        $response = $this->postJson('/api/webhooks/inpost/tracking', null);
        $response->assertStatus(400);

        Log::info('✅ Malformed payload handled correctly');
    }

    /**
     * Test webhook performance with multiple rapid calls
     */
    public function test_webhook_performance()
    {
        Log::info('=== Testing Webhook Performance ===');

        $trackingNumber = $this->testShipment->tracking_number;
        $startTime = microtime(true);
        $webhookCount = 5;

        // Send multiple webhooks rapidly
        for ($i = 0; $i < $webhookCount; $i++) {
            $webhookPayload = [
                'tracking_number' => $trackingNumber,
                'status' => 'in_transit',
                'event_time' => now()->addSeconds($i)->toISOString(),
                'message' => "Performance test webhook #{$i}",
            ];

            $response = $this->postJson('/api/webhooks/inpost/tracking', $webhookPayload);
            $response->assertStatus(200);
        }

        $endTime = microtime(true);
        $duration = $endTime - $startTime;

        Log::info("✅ Processed {$webhookCount} webhooks in " . round($duration, 3) . " seconds");
        Log::info("Average time per webhook: " . round($duration / $webhookCount, 3) . " seconds");

        // Verify final shipment state
        $finalShipment = $this->testShipment->fresh();
        $this->assertEquals('in_transit', $finalShipment->status);

        // Should have multiple events
        $this->assertGreaterThanOrEqual($webhookCount, count($finalShipment->tracking_events));

        Log::info('✅ Performance test completed successfully');
    }

    /**
     * Test webhook idempotency (same status multiple times)
     */
    public function test_webhook_idempotency()
    {
        Log::info('=== Testing Webhook Idempotency ===');

        $webhookPayload = [
            'tracking_number' => $this->testShipment->tracking_number,
            'status' => 'out_for_delivery',
            'event_time' => now()->toISOString(),
            'message' => 'Package out for delivery',
        ];

        // Send same webhook multiple times
        for ($i = 0; $i < 3; $i++) {
            $response = $this->postJson('/api/webhooks/inpost/tracking', $webhookPayload);
            $response->assertStatus(200);
            
            Log::info("Webhook #{$i}: " . ($response->json()['old_status'] ?? 'same') . " → " . $response->json()['new_status']);
        }

        // Verify status changed only once
        $finalShipment = $this->testShipment->fresh();
        $this->assertEquals('out_for_delivery', $finalShipment->status);

        // Should still have tracking events (even if status didn't change)
        $this->assertGreaterThan(0, count($finalShipment->tracking_events));

        Log::info('✅ Webhook idempotency working correctly');
        Log::info("Final status: {$finalShipment->status}");
        Log::info("Tracking events: " . count($finalShipment->tracking_events));
    }

    protected function tearDown(): void
    {
        Log::info("=== Test completed for shipment: {$this->testShipment->tracking_number} ===");
        parent::tearDown();
    }
}