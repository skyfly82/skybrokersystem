<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Courier;

use App\Exceptions\CourierServiceException;
use App\Services\Courier\Providers\DhlService;
use Mockery;
use SoapClient;
use Tests\TestCase;

class DhlServiceTest extends TestCase
{
    private DhlService $dhlService;

    private $mockSoapClient;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock configuration
        config([
            'skybrokersystem.couriers.dhl.sandbox' => true,
            'skybrokersystem.couriers.dhl.username' => 'test_user',
            'skybrokersystem.couriers.dhl.password' => 'test_pass',
            'skybrokersystem.couriers.dhl.account_number' => 'TEST123',
        ]);

        $this->mockSoapClient = Mockery::mock(SoapClient::class);
        $this->dhlService = new DhlService;

        // Use reflection to inject mock SOAP client
        $reflection = new \ReflectionClass($this->dhlService);
        $property = $reflection->getProperty('soapClient');
        $property->setAccessible(true);
        $property->setValue($this->dhlService, $this->mockSoapClient);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_id_returns_correct_courier_id(): void
    {
        $this->assertEquals(2, $this->dhlService->getId());
    }

    public function test_get_available_services_returns_correct_services(): void
    {
        $services = $this->dhlService->getAvailableServices();

        $this->assertIsArray($services);
        $this->assertArrayHasKey('standard', $services);
        $this->assertArrayHasKey('express', $services);
        $this->assertArrayHasKey('evening', $services);
        $this->assertArrayHasKey('saturday', $services);
        $this->assertArrayHasKey('cod', $services);
        $this->assertArrayHasKey('insurance', $services);
        $this->assertArrayHasKey('pallet', $services);
    }

    public function test_calculate_price_success(): void
    {
        $mockResponse = (object) [
            'getCostResult' => (object) [
                'isSuccess' => true,
                'totalCost' => 25.50,
                'baseCost' => 20.00,
                'fuelSurcharge' => 3.50,
                'additionalServicesCost' => 2.00,
                'estimatedDeliveryDate' => '2025-09-03',
            ],
        ];

        $this->mockSoapClient
            ->shouldReceive('getCost')
            ->once()
            ->andReturn($mockResponse);

        $data = [
            'sender' => [
                'name' => 'Test Sender',
                'postal_code' => '00-001',
                'city' => 'Warsaw',
                'street' => 'Test Street',
                'phone' => '123456789',
            ],
            'recipient' => [
                'name' => 'Test Recipient',
                'postal_code' => '10-001',
                'city' => 'Krakow',
                'street' => 'Test Street 2',
                'phone' => '987654321',
            ],
            'pieces' => [
                [
                    'width' => 20,
                    'height' => 15,
                    'length' => 30,
                    'weight' => 2.5,
                    'quantity' => 1,
                ],
            ],
        ];

        $result = $this->dhlService->calculatePrice($data);

        $this->assertTrue($result['success']);
        $this->assertEquals(25.50, $result['total_cost']);
        $this->assertEquals(20.00, $result['base_cost']);
        $this->assertEquals(3.50, $result['fuel_surcharge']);
        $this->assertEquals(2.00, $result['additional_services']);
        $this->assertEquals('PLN', $result['currency']);
        $this->assertEquals('2025-09-03', $result['estimated_delivery']);
    }

    public function test_calculate_price_failure(): void
    {
        $mockResponse = (object) [
            'getCostResult' => (object) [
                'isSuccess' => false,
                'errorMessage' => 'Invalid postal code',
            ],
        ];

        $this->mockSoapClient
            ->shouldReceive('getCost')
            ->once()
            ->andReturn($mockResponse);

        $data = [
            'sender' => [
                'name' => 'Invalid Sender',
                'postal_code' => 'INVALID',
                'city' => 'Warsaw',
                'street' => 'Test Street',
                'phone' => '123456789',
            ],
            'recipient' => [
                'name' => 'Test Recipient',
                'postal_code' => '10-001',
                'city' => 'Krakow',
                'street' => 'Test Street 2',
                'phone' => '987654321',
            ],
            'pieces' => [
                [
                    'width' => 20,
                    'height' => 15,
                    'length' => 30,
                    'weight' => 1,
                    'quantity' => 1,
                ],
            ],
        ];

        $this->expectException(CourierServiceException::class);
        $this->expectExceptionMessage('DHL price calculation failed: Invalid postal code');

        $this->dhlService->calculatePrice($data);
    }

    public function test_create_shipment_success(): void
    {
        $mockResponse = (object) [
            'createShipmentResult' => (object) [
                'isSuccess' => true,
                'shipmentNotificationNumber' => 'DHL123456789',
                'labelUrl' => 'https://example.com/label.pdf',
                'shipmentId' => 'SHIP001',
                'cost' => 25.50,
                'estimatedDeliveryDate' => '2025-09-03',
            ],
        ];

        $this->mockSoapClient
            ->shouldReceive('createShipment')
            ->once()
            ->andReturn($mockResponse);

        $data = [
            'sender' => [
                'name' => 'Test Sender',
                'postal_code' => '00-001',
                'city' => 'Warsaw',
                'street' => 'Test Street',
                'phone' => '123456789',
            ],
            'recipient' => [
                'name' => 'Test Recipient',
                'postal_code' => '10-001',
                'city' => 'Krakow',
                'street' => 'Test Street 2',
                'phone' => '987654321',
            ],
            'pieces' => [
                [
                    'width' => 20,
                    'height' => 15,
                    'length' => 30,
                    'weight' => 2.5,
                    'quantity' => 1,
                ],
            ],
        ];

        $result = $this->dhlService->createShipment($data);

        $this->assertTrue($result['success']);
        $this->assertEquals('DHL123456789', $result['tracking_number']);
        $this->assertEquals('https://example.com/label.pdf', $result['label_url']);
        $this->assertEquals('SHIP001', $result['shipment_id']);
        $this->assertEquals(25.50, $result['cost']);
        $this->assertEquals('PLN', $result['currency']);
        $this->assertEquals('2025-09-03', $result['estimated_delivery']);
    }

    public function test_track_shipment_success(): void
    {
        $mockResponse = (object) [
            'getTrackAndTraceInfoResult' => (object) [
                'isSuccess' => true,
                'status' => 'IN_TRANSIT',
                'statusDescription' => 'Package in transit',
                'events' => [
                    (object) [
                        'status' => 'COLLECTED',
                        'description' => 'Package collected',
                        'location' => 'Warsaw',
                        'timestamp' => '2025-09-02 10:00:00',
                        'terminal' => 'WAW01',
                    ],
                ],
                'estimatedDeliveryDate' => '2025-09-03',
                'deliveredAt' => null,
            ],
        ];

        $this->mockSoapClient
            ->shouldReceive('getTrackAndTraceInfo')
            ->once()
            ->with([
                'authData' => [
                    'username' => 'test_user',
                    'password' => 'test_pass',
                ],
                'shipmentNotificationNumber' => 'DHL123456789',
            ])
            ->andReturn($mockResponse);

        $result = $this->dhlService->trackShipment('DHL123456789');

        $this->assertTrue($result['success']);
        $this->assertEquals('DHL123456789', $result['tracking_number']);
        $this->assertEquals('in_transit', $result['status']);
        $this->assertEquals('Package in transit', $result['status_description']);
        $this->assertIsArray($result['events']);
        $this->assertCount(1, $result['events']);
        $this->assertEquals('picked_up', $result['events'][0]['status']);
    }

    public function test_cancel_shipment_success(): void
    {
        $mockResponse = (object) [
            'deleteShipmentResult' => (object) [
                'isSuccess' => true,
            ],
        ];

        $this->mockSoapClient
            ->shouldReceive('deleteShipment')
            ->once()
            ->andReturn($mockResponse);

        $result = $this->dhlService->cancelShipment('DHL123456789');

        $this->assertTrue($result);
    }

    public function test_handle_tracking_webhook(): void
    {
        $webhookData = [
            'shipmentNotificationNumber' => 'DHL123456789',
            'status' => 'DELIVERED',
            'eventTime' => '2025-09-03 15:30:00',
            'location' => 'Krakow',
        ];

        $result = $this->dhlService->handleTrackingWebhook($webhookData);

        $this->assertTrue($result['success']);
        $this->assertEquals('DHL123456789', $result['tracking_number']);
        $this->assertEquals('delivered', $result['status']);
        $this->assertEquals('2025-09-03 15:30:00', $result['event_time']);
        $this->assertEquals('Krakow', $result['location']);
    }
}
