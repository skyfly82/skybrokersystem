<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Customer;
use App\Models\CustomerUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

class ShipmentCreationFlowTest extends TestCase
{
    use RefreshDatabase;

    private $customer;
    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test customer with sufficient balance
        $this->customer = Customer::factory()->create([
            'company_name' => 'Test Company',
            'current_balance' => 1000.00,
            'credit_limit' => 500.00,
            'is_active' => true,
            'address' => 'ul. Testowa',
            'city' => 'Warszawa',
            'postal_code' => '00-001',
            'phone' => '+48123456789',
            'email' => 'test@example.com'
        ]);

        $this->user = CustomerUser::factory()->create([
            'customer_id' => $this->customer->id,
            'first_name' => 'Jan',
            'last_name' => 'Kowalski',
            'email' => 'jan@test.com',
            'is_active' => true
        ]);
    }

    public function test_it_can_calculate_shipping_prices()
    {
        $this->actingAs($this->user, 'customer_user');

        // Mock InPost API response
        Http::fake([
            'sandbox-api-shipx-pl.easypack24.net/*' => Http::response([
                'success' => true,
                'data' => [
                    [
                        'service_type' => 'inpost_locker_standard',
                        'price_net' => 10.00,
                        'price_gross' => 12.30,
                        'delivery_time' => '48h'
                    ]
                ]
            ])
        ]);

        $response = $this->postJson(route('customer.shipments.calculate-price'), [
            'courier_code' => 'inpost',
            'package' => [
                'weight' => 1.0,
                'length' => 20.0,
                'width' => 15.0,
                'height' => 10.0,
                'value' => 100
            ],
            'sender' => [
                'name' => 'Jan Kowalski',
                'company' => 'Test Company',
                'address' => 'ul. Testowa 1',
                'city' => 'Warszawa',
                'postal_code' => '00-001',
                'phone' => '+48123456789',
                'email' => 'jan@test.com'
            ],
            'recipient' => [
                'name' => 'Anna Nowak',
                'address' => 'ul. Odbiorcza 2',
                'city' => 'Kraków',
                'postal_code' => '30-001',
                'phone' => '+48987654321',
                'email' => 'anna@test.com'
            ]
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'prices' => [
                '*' => [
                    'service_type',
                    'service_name',
                    'price_net',
                    'price_gross'
                ]
            ]
        ]);
    }

    public function test_it_can_create_bulk_shipments_with_balance_payment()
    {
        $this->actingAs($this->user, 'customer_user');

        // Mock InPost API createShipment response
        Http::fake([
            '*/v1/organizations/*/shipments' => Http::response([
                'id' => 'test-shipment-123',
                'tracking_number' => 'TEST123456789',
                'calculated_charge_amount' => 1230, // 12.30 PLN in grosz
                'label_url' => 'https://api.inpost.pl/labels/test123.pdf'
            ])
        ]);

        $shipmentData = [
            'type' => 'small',
            'dimensions' => [
                'weight' => 1.0,
                'length' => 20.0,
                'width' => 15.0,
                'height' => 10.0
            ],
            'sender' => [
                'name' => 'Jan Kowalski',
                'company' => 'Test Company',
                'street' => 'ul. Testowa',
                'building_number' => '1',
                'apartment_number' => '',
                'city' => 'Warszawa',
                'postal_code' => '00-001',
                'phone' => '+48123456789',
                'email' => 'jan@test.com'
            ],
            'recipient' => [
                'name' => 'Anna Nowak',
                'street' => 'ul. Odbiorcza',
                'building_number' => '2',
                'apartment_number' => '',
                'city' => 'Kraków',
                'postal_code' => '30-001',
                'phone' => '+48987654321',
                'email' => 'anna@test.com'
            ],
            'selectedOffer' => [
                'id' => 'inpost_locker_standard',
                'price' => 12.30
            ],
            'services' => [],
            'notes' => 'Test shipment'
        ];

        $response = $this->postJson(route('customer.shipments.bulk-create'), [
            'shipments' => [$shipmentData],
            'payment_method' => 'balance',
            'total_amount' => 12.30
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true
        ]);

        // Check if shipment was created in database
        $this->assertDatabaseHas('shipments', [
            'tracking_number' => 'TEST123456789',
            'customer_id' => $this->customer->id,
            'status' => 'printed'
        ]);

        // Check if balance was deducted
        $this->customer->refresh();
        $this->assertEquals(987.70, $this->customer->current_balance);
    }

    public function test_it_can_create_bulk_shipments_with_online_payment()
    {
        $this->actingAs($this->user, 'customer_user');

        // Mock InPost API createShipment response
        Http::fake([
            '*/v1/organizations/*/shipments' => Http::response([
                'id' => 'test-shipment-456',
                'tracking_number' => 'TEST987654321',
                'calculated_charge_amount' => 1230, // 12.30 PLN in grosz
                'label_url' => 'https://api.inpost.pl/labels/test456.pdf'
            ])
        ]);

        $shipmentData = [
            'type' => 'small',
            'dimensions' => [
                'weight' => 1.0,
                'length' => 20.0,
                'width' => 15.0,
                'height' => 10.0
            ],
            'sender' => [
                'name' => 'Jan Kowalski',
                'company' => 'Test Company',
                'street' => 'ul. Testowa',
                'building_number' => '1',
                'apartment_number' => '',
                'city' => 'Warszawa',
                'postal_code' => '00-001',
                'phone' => '+48123456789',
                'email' => 'jan@test.com'
            ],
            'recipient' => [
                'name' => 'Anna Nowak',
                'street' => 'ul. Odbiorcza',
                'building_number' => '2',
                'apartment_number' => '',
                'city' => 'Kraków',
                'postal_code' => '30-001',
                'phone' => '+48987654321',
                'email' => 'anna@test.com'
            ],
            'selectedOffer' => [
                'id' => 'inpost_courier_standard',
                'price' => 18.90
            ],
            'services' => [],
            'notes' => 'Test courier shipment'
        ];

        $response = $this->postJson(route('customer.shipments.bulk-create'), [
            'shipments' => [$shipmentData],
            'payment_method' => 'online',
            'total_amount' => 18.90
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true
        ]);

        $responseData = $response->json();
        $this->assertArrayHasKey('redirect_url', $responseData);
        $this->assertStringContains('payment.simulate', $responseData['redirect_url']);

        // Check if shipment was created with 'created' status (not paid yet)
        $this->assertDatabaseHas('shipments', [
            'tracking_number' => 'TEST987654321',
            'customer_id' => $this->customer->id,
            'status' => 'printed' // InPost creates it as printed, payment status is separate
        ]);
    }

    public function test_it_rejects_shipment_with_insufficient_balance()
    {
        $this->actingAs($this->user, 'customer_user');
        
        // Set low balance
        $this->customer->update(['current_balance' => 5.00]);

        $shipmentData = [
            'type' => 'small',
            'selectedOffer' => ['id' => 'inpost_locker_standard', 'price' => 50.00],
            'sender' => [
                'name' => 'Jan Kowalski',
                'street' => 'ul. Testowa',
                'building_number' => '1',
                'city' => 'Warszawa',
                'postal_code' => '00-001',
                'phone' => '+48123456789',
                'email' => 'jan@test.com'
            ],
            'recipient' => [
                'name' => 'Anna Nowak',
                'street' => 'ul. Odbiorcza',
                'building_number' => '2',
                'city' => 'Kraków',
                'postal_code' => '30-001',
                'phone' => '+48987654321',
                'email' => 'anna@test.com'
            ]
        ];

        $response = $this->postJson(route('customer.shipments.bulk-create'), [
            'shipments' => [$shipmentData],
            'payment_method' => 'balance',
            'total_amount' => 50.00
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'success' => false,
            'message' => 'Niewystarczające saldo na koncie. Dostępne: 5.00 PLN'
        ]);
    }
}