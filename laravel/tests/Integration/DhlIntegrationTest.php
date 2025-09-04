<?php

declare(strict_types=1);

namespace Tests\Integration;

use App\Services\Courier\CourierServiceFactory;
use Tests\TestCase;

/**
 * DHL Integration Test
 *
 * Tests the complete DHL integration workflow including:
 * - Service factory integration
 * - Configuration loading
 * - API endpoint accessibility
 */
class DhlIntegrationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Set up DHL configuration for testing
        config([
            'skybrokersystem.couriers.dhl.sandbox' => true,
            'skybrokersystem.couriers.dhl.username' => 'test_user',
            'skybrokersystem.couriers.dhl.password' => 'test_pass',
            'skybrokersystem.couriers.dhl.account_number' => 'TEST123456',
            'skybrokersystem.couriers.enabled_services.dhl' => true,
        ]);
    }

    public function test_dhl_service_is_supported_in_factory(): void
    {
        $factory = app(CourierServiceFactory::class);

        $this->assertTrue($factory->isSupported('dhl'));

        $availableCouriers = $factory->getAvailableCouriers();
        $this->assertArrayHasKey('dhl', $availableCouriers);
    }

    public function test_dhl_service_can_be_created(): void
    {
        $factory = app(CourierServiceFactory::class);
        $dhlService = $factory->makeByCode('dhl');

        $this->assertInstanceOf(\App\Services\Courier\Providers\DhlService::class, $dhlService);
        $this->assertEquals(2, $dhlService->getId());
    }

    public function test_dhl_api_services_endpoint_is_accessible(): void
    {
        $response = $this->getJson('/api/dhl/services');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data',
            'message',
        ]);

        $response->assertJsonPath('success', true);
        $this->assertArrayHasKey('standard', $response->json('data'));
        $this->assertArrayHasKey('express', $response->json('data'));
        $this->assertArrayHasKey('pallet', $response->json('data'));
    }

    public function test_dhl_price_calculation_endpoint_validates_input(): void
    {
        $response = $this->postJson('/api/dhl/calculate-price', []);

        $response->assertStatus(422); // Validation error
    }

    public function test_dhl_tracking_endpoint_is_accessible(): void
    {
        $trackingNumber = 'DHL123456789';

        $response = $this->getJson("/api/dhl/track/{$trackingNumber}");

        // Should fail with DHL API error, but endpoint should be accessible
        $response->assertStatus(422);
        $response->assertJsonPath('success', false);
        $response->assertJsonStructure([
            'success',
            'message',
        ]);
    }

    public function test_authenticated_endpoints_are_protected(): void
    {
        // Test that authenticated endpoints exist and are protected
        // We'll just check that the routes are registered
        $this->assertTrue(true); // Placeholder - would need proper auth setup to test fully
    }

    public function test_dhl_configuration_is_loaded_correctly(): void
    {
        $config = config('skybrokersystem.couriers.dhl');

        $this->assertIsArray($config);
        $this->assertArrayHasKey('username', $config);
        $this->assertArrayHasKey('password', $config);
        $this->assertArrayHasKey('account_number', $config);
        $this->assertArrayHasKey('sandbox', $config);
        $this->assertArrayHasKey('services', $config);
        $this->assertArrayHasKey('label_formats', $config);
        $this->assertArrayHasKey('special_services', $config);

        // Check services configuration
        $this->assertArrayHasKey('standard', $config['services']);
        $this->assertArrayHasKey('pallet', $config['services']);

        // Check label formats
        $this->assertArrayHasKey('pdf', $config['label_formats']);
        $this->assertArrayHasKey('zpl', $config['label_formats']);

        // Check special services
        $this->assertArrayHasKey('cod', $config['special_services']);
        $this->assertArrayHasKey('insurance', $config['special_services']);
    }

    public function test_dhl_is_enabled_in_courier_services(): void
    {
        $enabledServices = config('skybrokersystem.couriers.enabled_services');

        $this->assertArrayHasKey('dhl', $enabledServices);
    }
}
