<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class WebhookSettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:system_user', 'admin']);
    }

    public function index(): View
    {
        $webhookSettings = [
            'freshdesk' => [
                'api_url' => config('services.freshdesk.api_url'),
                'api_key' => config('services.freshdesk.api_key') ? '***' : null,
                'webhook_secret' => config('services.freshdesk.webhook_secret') ? '***' : null,
                'enabled' => config('services.freshdesk.enabled', false),
            ],
            'freshcaller' => [
                'api_url' => config('services.freshcaller.api_url'),
                'api_key' => config('services.freshcaller.api_key') ? '***' : null,
                'webhook_secret' => config('services.freshcaller.webhook_secret') ? '***' : null,
                'enabled' => config('services.freshcaller.enabled', false),
            ],
        ];

        // For now, we'll provide empty webhook logs
        // TODO: Implement webhook logging system
        $webhook_logs = collect([]);

        return view('admin.settings.webhooks', compact('webhookSettings', 'webhook_logs'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'service_name' => ['required', 'string', 'max:50', 'alpha_dash'],
            'service_type' => ['required', 'string', 'in:customer_service,communication,payment,shipping,analytics,other'],
            'api_url' => ['required', 'url', 'max:255'],
            'api_key' => ['required', 'string', 'max:255'],
        ]);

        try {
            $serviceName = strtolower($validated['service_name']);
            
            // Check if service already exists
            if (config("services.{$serviceName}.api_url")) {
                return redirect()->back()
                    ->with('error', 'Webhook o tej nazwie już istnieje.')
                    ->withInput();
            }

            // Add to services configuration
            $this->updateServiceConfig($serviceName, [
                'api_url' => $validated['api_url'],
                'api_key' => $validated['api_key'],
                'webhook_secret' => Str::random(32),
                'enabled' => true,
                'service_type' => $validated['service_type'],
            ]);

            // Add route to web.php (this would need manual addition for now)
            // TODO: Implement dynamic route registration
            
            return redirect()->route('admin.settings.webhooks.index')
                ->with('success', 'Nowy webhook został pomyślnie dodany.');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Błąd podczas dodawania webhook: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(string $service): RedirectResponse
    {
        $validServices = ['freshdesk', 'freshcaller'];
        
        if (in_array($service, $validServices)) {
            return redirect()->back()->with('error', 'Nie można usunąć wbudowanego webhook.');
        }

        try {
            // Remove from environment file
            $this->removeServiceConfig($service);
            
            return redirect()->route('admin.settings.webhooks.index')
                ->with('success', 'Webhook został usunięty.');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Błąd podczas usuwania webhook: ' . $e->getMessage());
        }
    }

    public function update(Request $request, string $service): RedirectResponse
    {
        $validServices = ['freshdesk', 'freshcaller'];
        
        if (!in_array($service, $validServices)) {
            return redirect()->back()->with('error', 'Nieprawidłowy serwis webhook.');
        }

        $validated = $request->validate([
            'api_url' => ['required', 'url', 'max:255'],
            'api_key' => ['required', 'string', 'max:255'],
            'webhook_secret' => ['nullable', 'string', 'max:255'],
            'enabled' => ['boolean'],
        ]);

        try {
            // Update environment file or configuration
            $this->updateServiceConfig($service, $validated);
            
            return redirect()->route('admin.settings.webhooks.index')
                ->with('success', 'Ustawienia webhook ' . ucfirst($service) . ' zostały zaktualizowane.');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Błąd podczas aktualizacji ustawień: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function generateSecret(Request $request, string $service): RedirectResponse
    {
        $validServices = ['freshdesk', 'freshcaller'];
        
        if (!in_array($service, $validServices)) {
            return redirect()->back()->with('error', 'Nieprawidłowy serwis webhook.');
        }

        try {
            $secret = Str::random(32);
            $this->updateServiceConfig($service, ['webhook_secret' => $secret]);
            
            return redirect()->route('admin.settings.webhooks.index')
                ->with('success', 'Nowy secret webhook został wygenerowany dla ' . ucfirst($service) . '.');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Błąd podczas generowania secret: ' . $e->getMessage());
        }
    }

    public function test(Request $request, string $service): RedirectResponse
    {
        $validServices = ['freshdesk', 'freshcaller'];
        
        if (!in_array($service, $validServices)) {
            return redirect()->back()->with('error', 'Nieprawidłowy serwis webhook.');
        }

        try {
            $apiUrl = config("services.{$service}.api_url");
            $apiKey = config("services.{$service}.api_key");

            if (!$apiUrl || !$apiKey) {
                return redirect()->back()
                    ->with('error', 'Brak konfiguracji API dla ' . ucfirst($service) . '. Skonfiguruj najpierw połączenie.');
            }

            // Test connection based on service
            $response = $this->testServiceConnection($service, $apiUrl, $apiKey);

            if ($response['success']) {
                return redirect()->route('admin.settings.webhooks.index')
                    ->with('success', 'Połączenie z ' . ucfirst($service) . ' zostało pomyślnie przetestowane.');
            } else {
                return redirect()->back()
                    ->with('error', 'Test połączenia nieudany: ' . $response['message']);
            }
            
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Błąd podczas testowania połączenia: ' . $e->getMessage());
        }
    }

    private function updateServiceConfig(string $service, array $config): void
    {
        $envFile = base_path('.env');
        $envContent = file_get_contents($envFile);

        $serviceUpper = strtoupper($service);
        
        foreach ($config as $key => $value) {
            $envKey = "{$serviceUpper}_" . strtoupper($key);
            $envValue = is_bool($value) ? ($value ? 'true' : 'false') : $value;
            
            // Check if key exists
            if (preg_match("/^{$envKey}=.*$/m", $envContent)) {
                $envContent = preg_replace("/^{$envKey}=.*$/m", "{$envKey}={$envValue}", $envContent);
            } else {
                $envContent .= "\n{$envKey}={$envValue}";
            }
        }

        file_put_contents($envFile, $envContent);
        
        // Clear config cache
        \Artisan::call('config:clear');
    }

    private function removeServiceConfig(string $service): void
    {
        $envFile = base_path('.env');
        $envContent = file_get_contents($envFile);

        $serviceUpper = strtoupper($service);
        $patterns = [
            "/^{$serviceUpper}_API_URL=.*$/m",
            "/^{$serviceUpper}_API_KEY=.*$/m",
            "/^{$serviceUpper}_WEBHOOK_SECRET=.*$/m",
            "/^{$serviceUpper}_ENABLED=.*$/m",
            "/^{$serviceUpper}_SERVICE_TYPE=.*$/m",
        ];
        
        foreach ($patterns as $pattern) {
            $envContent = preg_replace($pattern, '', $envContent);
        }

        // Clean up multiple newlines
        $envContent = preg_replace("/\n{3,}/", "\n\n", $envContent);

        file_put_contents($envFile, $envContent);
        
        // Clear config cache
        \Artisan::call('config:clear');
    }

    private function testServiceConnection(string $service, string $apiUrl, string $apiKey): array
    {
        try {
            switch ($service) {
                case 'freshdesk':
                    $response = Http::withBasicAuth($apiKey, 'X')
                        ->timeout(10)
                        ->get($apiUrl . '/api/v2/tickets', [
                            'per_page' => 1
                        ]);
                    break;
                    
                case 'freshcaller':
                    $response = Http::withHeaders([
                        'Authorization' => 'Token token=' . $apiKey
                    ])
                    ->timeout(10)
                    ->get($apiUrl . '/v1/accounts');
                    break;
                    
                default:
                    return ['success' => false, 'message' => 'Nieobsługiwany serwis'];
            }

            if ($response->successful()) {
                return ['success' => true, 'message' => 'Połączenie udane'];
            } else {
                return ['success' => false, 'message' => 'HTTP ' . $response->status() . ': ' . $response->body()];
            }
            
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}