@extends('layouts.admin')

@section('header')
    <h1 class="text-2xl font-semibold text-gray-900">Konfiguracja Webhooków</h1>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
        
        <!-- Freshdesk Configuration -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900">Freshdesk Integration</h3>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $freshdesk && $freshdesk->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $freshdesk && $freshdesk->is_active ? 'Aktywne' : 'Nieaktywne' }}
                    </span>
                </div>
            </div>
            <div class="p-6">
                <form method="POST" action="{{ route('admin.settings.webhooks.update', 'freshdesk') }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 gap-6">
                        <!-- API URL -->
                        <div>
                            <label for="freshdesk_api_url" class="block text-sm font-medium text-gray-700">
                                API URL <span class="text-red-500">*</span>
                            </label>
                            <input type="url" name="api_url" id="freshdesk_api_url" 
                                   value="{{ old('api_url', $freshdesk->api_url ?? '') }}" 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="https://yourdomain.freshdesk.com">
                            <p class="mt-1 text-sm text-gray-500">URL do Twojego Freshdesk API</p>
                            @error('api_url')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- API Key -->
                        <div>
                            <label for="freshdesk_api_key" class="block text-sm font-medium text-gray-700">
                                API Key <span class="text-red-500">*</span>
                            </label>
                            <input type="password" name="api_key" id="freshdesk_api_key" 
                                   value="{{ old('api_key', $freshdesk && $freshdesk->api_key ? '••••••••' : '') }}" 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Wprowadź API Key">
                            <p class="mt-1 text-sm text-gray-500">Klucz API z ustawień Freshdesk</p>
                            @error('api_key')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Webhook Secret -->
                        <div>
                            <label for="freshdesk_webhook_secret" class="block text-sm font-medium text-gray-700">
                                Webhook Secret
                            </label>
                            <div class="mt-1 flex rounded-md shadow-sm">
                                <input type="text" name="webhook_secret" id="freshdesk_webhook_secret" 
                                       value="{{ old('webhook_secret', $freshdesk->webhook_secret ?? '') }}" 
                                       class="flex-1 border-gray-300 rounded-l-md focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="Webhook secret dla zabezpieczenia" readonly>
                                <button type="button" onclick="generateWebhookSecret('freshdesk_webhook_secret')" 
                                        class="inline-flex items-center px-3 py-2 border border-l-0 border-gray-300 rounded-r-md bg-gray-50 text-gray-500 text-sm">
                                    Generuj
                                </button>
                            </div>
                            <p class="mt-1 text-sm text-gray-500">Secret do weryfikacji webhooków</p>
                            @error('webhook_secret')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Webhook URL (read-only) -->
                        <div>
                            <label for="freshdesk_webhook_url" class="block text-sm font-medium text-gray-700">
                                Webhook URL
                            </label>
                            <input type="url" id="freshdesk_webhook_url" 
                                   value="{{ route('webhooks.freshdesk') }}" 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-50 text-gray-500"
                                   readonly>
                            <p class="mt-1 text-sm text-gray-500">URL do konfiguracji w Freshdesk</p>
                            <button type="button" onclick="copyToClipboard('freshdesk_webhook_url')" 
                                    class="mt-1 text-sm text-blue-600 hover:text-blue-500">
                                Kopiuj do schowka
                            </button>
                        </div>

                        <!-- Active checkbox -->
                        <div class="flex items-center">
                            <input type="checkbox" name="is_active" id="freshdesk_is_active" 
                                   {{ old('is_active', $freshdesk->is_active ?? false) ? 'checked' : '' }}
                                   class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                            <label for="freshdesk_is_active" class="ml-2 block text-sm text-gray-900">
                                Aktywuj integrację Freshdesk
                            </label>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" onclick="testWebhook('freshdesk')" 
                                class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Test połączenia
                        </button>
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700">
                            Zapisz konfigurację
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Freshcaller Configuration -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900">Freshcaller Integration</h3>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $freshcaller && $freshcaller->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $freshcaller && $freshcaller->is_active ? 'Aktywne' : 'Nieaktywne' }}
                    </span>
                </div>
            </div>
            <div class="p-6">
                <form method="POST" action="{{ route('admin.settings.webhooks.update', 'freshcaller') }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 gap-6">
                        <!-- API URL -->
                        <div>
                            <label for="freshcaller_api_url" class="block text-sm font-medium text-gray-700">
                                API URL <span class="text-red-500">*</span>
                            </label>
                            <input type="url" name="api_url" id="freshcaller_api_url" 
                                   value="{{ old('api_url', $freshcaller->api_url ?? '') }}" 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="https://yourdomain.freshcaller.com">
                            <p class="mt-1 text-sm text-gray-500">URL do Twojego Freshcaller API</p>
                            @error('api_url')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- API Key -->
                        <div>
                            <label for="freshcaller_api_key" class="block text-sm font-medium text-gray-700">
                                API Key <span class="text-red-500">*</span>
                            </label>
                            <input type="password" name="api_key" id="freshcaller_api_key" 
                                   value="{{ old('api_key', $freshcaller && $freshcaller->api_key ? '••••••••' : '') }}" 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Wprowadź API Key">
                            <p class="mt-1 text-sm text-gray-500">Klucz API z ustawień Freshcaller</p>
                            @error('api_key')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Webhook Secret -->
                        <div>
                            <label for="freshcaller_webhook_secret" class="block text-sm font-medium text-gray-700">
                                Webhook Secret
                            </label>
                            <div class="mt-1 flex rounded-md shadow-sm">
                                <input type="text" name="webhook_secret" id="freshcaller_webhook_secret" 
                                       value="{{ old('webhook_secret', $freshcaller->webhook_secret ?? '') }}" 
                                       class="flex-1 border-gray-300 rounded-l-md focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="Webhook secret dla zabezpieczenia" readonly>
                                <button type="button" onclick="generateWebhookSecret('freshcaller_webhook_secret')" 
                                        class="inline-flex items-center px-3 py-2 border border-l-0 border-gray-300 rounded-r-md bg-gray-50 text-gray-500 text-sm">
                                    Generuj
                                </button>
                            </div>
                            <p class="mt-1 text-sm text-gray-500">Secret do weryfikacji webhooków</p>
                            @error('webhook_secret')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Webhook URL (read-only) -->
                        <div>
                            <label for="freshcaller_webhook_url" class="block text-sm font-medium text-gray-700">
                                Webhook URL
                            </label>
                            <input type="url" id="freshcaller_webhook_url" 
                                   value="{{ route('webhooks.freshcaller') }}" 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-50 text-gray-500"
                                   readonly>
                            <p class="mt-1 text-sm text-gray-500">URL do konfiguracji w Freshcaller</p>
                            <button type="button" onclick="copyToClipboard('freshcaller_webhook_url')" 
                                    class="mt-1 text-sm text-blue-600 hover:text-blue-500">
                                Kopiuj do schowka
                            </button>
                        </div>

                        <!-- Active checkbox -->
                        <div class="flex items-center">
                            <input type="checkbox" name="is_active" id="freshcaller_is_active" 
                                   {{ old('is_active', $freshcaller->is_active ?? false) ? 'checked' : '' }}
                                   class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                            <label for="freshcaller_is_active" class="ml-2 block text-sm text-gray-900">
                                Aktywuj integrację Freshcaller
                            </label>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" onclick="testWebhook('freshcaller')" 
                                class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Test połączenia
                        </button>
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700">
                            Zapisz konfigurację
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Webhook Logs -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Logi Webhooków (ostatnie 10)</h3>
            </div>
            <div class="overflow-hidden">
                @if($webhook_logs->count() > 0)
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Czas</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Serwis</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Wiadomość</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($webhook_logs as $log)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $log->created_at->format('d.m.Y H:i:s') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ ucfirst($log->service) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full {{ $log->status === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $log->status === 'success' ? 'Sukces' : 'Błąd' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ Str::limit($log->message, 100) }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="p-6 text-center text-gray-500">
                        Brak logów webhooków
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function generateWebhookSecret(inputId) {
    const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    let result = '';
    for (let i = 0; i < 64; i++) {
        result += characters.charAt(Math.floor(Math.random() * characters.length));
    }
    document.getElementById(inputId).value = result;
}

function copyToClipboard(inputId) {
    const input = document.getElementById(inputId);
    input.select();
    document.execCommand('copy');
    
    // Show feedback
    const button = event.target;
    const originalText = button.textContent;
    button.textContent = 'Skopiowano!';
    button.classList.add('text-green-600');
    
    setTimeout(() => {
        button.textContent = originalText;
        button.classList.remove('text-green-600');
        button.classList.add('text-blue-600');
    }, 2000);
}

function testWebhook(service) {
    const button = event.target;
    const originalText = button.textContent;
    button.textContent = 'Testowanie...';
    button.disabled = true;
    
    fetch(`{{ route('admin.settings.webhooks.test', '') }}/${service}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Test połączenia zakończony sukcesem!');
        } else {
            alert('Test połączenia nieudany: ' + (data.message || 'Nieznany błąd'));
        }
    })
    .catch(error => {
        alert('Błąd podczas testowania połączenia: ' + error.message);
    })
    .finally(() => {
        button.textContent = originalText;
        button.disabled = false;
    });
}
</script>
@endsection