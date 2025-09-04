<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Courier\CourierServiceFactory;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class DhlSettingsController extends Controller
{
    public function __construct(
        private CourierServiceFactory $courierFactory
    ) {}

    /**
     * Show DHL configuration page
     */
    public function config(): View
    {
        $config = config('skybrokersystem.couriers.dhl');

        return view('admin.settings.couriers.dhl.config', [
            'title' => 'Konfiguracja DHL',
            'description' => 'Ustawienia integracji z DHL WebAPI 2.0',
            'config' => $config,
            'is_enabled' => config('skybrokersystem.couriers.enabled_services.dhl', false),
        ]);
    }

    /**
     * Update DHL configuration
     */
    public function updateConfig(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'enabled' => 'boolean',
            'sandbox' => 'boolean',
            'username' => 'required_if:enabled,true|string|max:255',
            'password' => 'required_if:enabled,true|string|max:255',
            'account_number' => 'required_if:enabled,true|string|max:50',
            'services' => 'array',
            'services.*' => 'boolean',
        ]);

        try {
            // Update .env or config files would be done here
            // For now, we'll just show success message

            return redirect()
                ->route('admin.settings.couriers.dhl.config')
                ->with('success', 'Konfiguracja DHL została zaktualizowana pomyślnie.');

        } catch (Exception $e) {
            Log::error('DHL configuration update failed', [
                'error' => $e->getMessage(),
                'data' => $validated,
            ]);

            return redirect()
                ->route('admin.settings.couriers.dhl.config')
                ->with('error', 'Wystąpił błąd podczas aktualizacji konfiguracji: '.$e->getMessage());
        }
    }

    /**
     * Test DHL API connection
     */
    public function test(): View
    {
        $results = [
            'connection' => null,
            'authentication' => null,
            'services' => null,
            'errors' => [],
        ];

        try {
            $dhlService = $this->courierFactory->makeByCode('dhl');

            // Test 1: Get available services (basic connection test)
            try {
                $services = $dhlService->getAvailableServices();
                $results['connection'] = true;
                $results['services'] = $services;
            } catch (Exception $e) {
                $results['connection'] = false;
                $results['errors']['connection'] = $e->getMessage();
            }

            // Test 2: Test price calculation (authentication test)
            try {
                $testData = [
                    'sender' => [
                        'name' => 'Test Sender',
                        'postal_code' => '00-001',
                        'city' => 'Warszawa',
                        'street' => 'Testowa 1',
                        'phone' => '123456789',
                    ],
                    'recipient' => [
                        'name' => 'Test Recipient',
                        'postal_code' => '31-001',
                        'city' => 'Kraków',
                        'street' => 'Testowa 2',
                        'phone' => '987654321',
                    ],
                    'pieces' => [
                        [
                            'width' => 10,
                            'height' => 10,
                            'length' => 10,
                            'weight' => 1,
                            'quantity' => 1,
                        ],
                    ],
                ];

                $priceResult = $dhlService->calculatePrice($testData);
                $results['authentication'] = $priceResult['success'] ?? false;

            } catch (Exception $e) {
                $results['authentication'] = false;
                $results['errors']['authentication'] = $e->getMessage();
            }

        } catch (Exception $e) {
            $results['connection'] = false;
            $results['errors']['general'] = $e->getMessage();
        }

        return view('admin.settings.couriers.dhl.test', [
            'title' => 'Test API DHL',
            'description' => 'Testowanie połączenia z DHL WebAPI',
            'results' => $results,
        ]);
    }

    /**
     * Show DHL statistics
     */
    public function stats(): View
    {
        // Mock data - would be replaced with real statistics
        $stats = [
            'total_shipments' => 567,
            'shipments_this_month' => 89,
            'successful_deliveries' => 534,
            'failed_deliveries' => 12,
            'average_cost' => 25.50,
            'popular_services' => [
                'DHL Standard' => 345,
                'DHL Express' => 123,
                'DHL Saturday' => 67,
                'DHL Pallet' => 32,
            ],
            'monthly_data' => [
                'labels' => ['Sty', 'Lut', 'Mar', 'Kwi', 'Maj', 'Cze'],
                'shipments' => [45, 67, 89, 123, 98, 89],
                'costs' => [1234.50, 1789.30, 2456.70, 3123.90, 2567.40, 2234.60],
            ],
        ];

        return view('admin.settings.couriers.dhl.stats', [
            'title' => 'Statystyki DHL',
            'description' => 'Analiza wykorzystania usług DHL',
            'stats' => $stats,
        ]);
    }
}
