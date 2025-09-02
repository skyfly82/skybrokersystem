@extends('layouts.admin')

@section('header')
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-semibold text-gray-900">Konfiguracja Webhooków</h1>
        <button type="button" onclick="openAddWebhookModal()" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Dodaj Webhook
        </button>
    </div>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <!-- Webhooks Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            
            <!-- Freshdesk Webhook Tile -->
            <div class="bg-white overflow-hidden shadow rounded-lg hover:shadow-lg transition-shadow">
                <div class="p-6">
                    <!-- Header -->
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-lg font-medium text-gray-900">Freshdesk</h3>
                                <p class="text-sm text-gray-500">Customer Service</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $webhookSettings['freshdesk']['enabled'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $webhookSettings['freshdesk']['enabled'] ? 'Aktywne' : 'Nieaktywne' }}
                        </span>
                    </div>
                    
                    <!-- Configuration Info -->
                    <div class="space-y-2 mb-4">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">API URL:</span>
                            <span class="text-gray-900 truncate ml-2">
                                {{ $webhookSettings['freshdesk']['api_url'] ?? 'Nie skonfigurowane' }}
                            </span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">API Key:</span>
                            <span class="text-gray-900">
                                {{ $webhookSettings['freshdesk']['api_key'] ? '••••••••' : 'Nie skonfigurowane' }}
                            </span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Webhook URL:</span>
                            <button type="button" onclick="copyToClipboard('{{ route('admin.webhooks.freshdesk') }}')" class="text-blue-600 hover:text-blue-500 text-xs">
                                Kopiuj URL
                            </button>
                        </div>
                    </div>
                    
                    <!-- Actions -->
                    <div class="flex space-x-2">
                        <button type="button" onclick="openConfigModal('freshdesk')" class="flex-1 bg-blue-600 text-white text-sm py-2 px-3 rounded-md hover:bg-blue-700 transition-colors">
                            Konfiguruj
                        </button>
                        <button type="button" onclick="testConnection('freshdesk')" class="bg-gray-100 text-gray-700 text-sm py-2 px-3 rounded-md hover:bg-gray-200 transition-colors">
                            Test
                        </button>
                        <button type="button" onclick="deleteWebhook('freshdesk')" class="bg-red-100 text-red-700 text-sm py-2 px-3 rounded-md hover:bg-red-200 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Freshcaller Webhook Tile -->
            <div class="bg-white overflow-hidden shadow rounded-lg hover:shadow-lg transition-shadow">
                <div class="p-6">
                    <!-- Header -->
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-lg font-medium text-gray-900">Freshcaller</h3>
                                <p class="text-sm text-gray-500">VoIP & Phone System</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $webhookSettings['freshcaller']['enabled'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $webhookSettings['freshcaller']['enabled'] ? 'Aktywne' : 'Nieaktywne' }}
                        </span>
                    </div>
                    
                    <!-- Configuration Info -->
                    <div class="space-y-2 mb-4">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">API URL:</span>
                            <span class="text-gray-900 truncate ml-2">
                                {{ $webhookSettings['freshcaller']['api_url'] ?? 'Nie skonfigurowane' }}
                            </span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">API Key:</span>
                            <span class="text-gray-900">
                                {{ $webhookSettings['freshcaller']['api_key'] ? '••••••••' : 'Nie skonfigurowane' }}
                            </span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Webhook URL:</span>
                            <button type="button" onclick="copyToClipboard('{{ route('admin.webhooks.freshcaller') }}')" class="text-blue-600 hover:text-blue-500 text-xs">
                                Kopiuj URL
                            </button>
                        </div>
                    </div>
                    
                    <!-- Actions -->
                    <div class="flex space-x-2">
                        <button type="button" onclick="openConfigModal('freshcaller')" class="flex-1 bg-blue-600 text-white text-sm py-2 px-3 rounded-md hover:bg-blue-700 transition-colors">
                            Konfiguruj
                        </button>
                        <button type="button" onclick="testConnection('freshcaller')" class="bg-gray-100 text-gray-700 text-sm py-2 px-3 rounded-md hover:bg-gray-200 transition-colors">
                            Test
                        </button>
                        <button type="button" onclick="deleteWebhook('freshcaller')" class="bg-red-100 text-red-700 text-sm py-2 px-3 rounded-md hover:bg-red-200 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Add New Webhook Tile -->
            <div class="bg-white overflow-hidden shadow rounded-lg border-2 border-dashed border-gray-300 hover:border-gray-400 transition-colors">
                <button type="button" onclick="openAddWebhookModal()" class="w-full h-full p-6 text-center">
                    <div class="flex flex-col items-center justify-center">
                        <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Dodaj Webhook</h3>
                        <p class="text-sm text-gray-500">Kliknij aby dodać nową integrację webhook</p>
                    </div>
                </button>
            </div>
        </div>

        <!-- Recent Activity -->
        @if($webhook_logs->count() > 0)
        <div class="mt-8 bg-white overflow-hidden shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Ostatnia Aktywność Webhooków</h3>
            </div>
            <div class="max-h-96 overflow-y-auto">
                @foreach($webhook_logs as $log)
                    <div class="p-6 border-b border-gray-200 last:border-b-0">
                        <div class="flex justify-between items-start">
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ $log->service }}</div>
                                <div class="text-sm text-gray-500">{{ $log->event_type }} - {{ $log->created_at->format('d.m.Y H:i:s') }}</div>
                            </div>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $log->status === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $log->status === 'success' ? 'Sukces' : 'Błąd' }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Configuration Modal -->
<div id="configModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900" id="modalTitle">Konfiguracja Webhook</h3>
                <button type="button" onclick="closeConfigModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <form id="webhookForm" method="POST">
                @csrf
                @method('PUT')
                
                <div class="space-y-4">
                    <!-- API URL -->
                    <div>
                        <label for="modal_api_url" class="block text-sm font-medium text-gray-700">
                            API URL <span class="text-red-500">*</span>
                        </label>
                        <input type="url" name="api_url" id="modal_api_url" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                               placeholder="https://example.com/api">
                    </div>

                    <!-- API Key -->
                    <div>
                        <label for="modal_api_key" class="block text-sm font-medium text-gray-700">
                            API Key <span class="text-red-500">*</span>
                        </label>
                        <input type="password" name="api_key" id="modal_api_key" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Wprowadź API Key">
                    </div>

                    <!-- Webhook Secret -->
                    <div>
                        <label for="modal_webhook_secret" class="block text-sm font-medium text-gray-700">
                            Webhook Secret
                        </label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                            <input type="password" name="webhook_secret" id="modal_webhook_secret" 
                                   class="flex-1 border-gray-300 rounded-l-md focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Secret do weryfikacji webhook">
                            <button type="button" onclick="generateSecret()" 
                                    class="inline-flex items-center px-3 py-2 border border-l-0 border-gray-300 rounded-r-md bg-gray-50 text-gray-500 text-sm">
                                Generate
                            </button>
                        </div>
                    </div>

                    <!-- Enabled -->
                    <div class="flex items-center">
                        <input type="checkbox" name="enabled" id="modal_enabled" 
                               class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                        <label for="modal_enabled" class="ml-2 block text-sm text-gray-900">
                            Włącz webhook
                        </label>
                    </div>
                </div>
                
                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" onclick="closeConfigModal()" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                        Anuluj
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                        Zapisz
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add New Webhook Modal -->
<div id="addWebhookModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Dodaj Nowy Webhook</h3>
                <button type="button" onclick="closeAddWebhookModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <form id="addWebhookForm" method="POST" action="{{ route('admin.settings.webhooks.store') }}">
                @csrf
                
                <div class="space-y-4">
                    <!-- Service Name -->
                    <div>
                        <label for="add_service_name" class="block text-sm font-medium text-gray-700">
                            Nazwa Serwisu <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="service_name" id="add_service_name" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                               placeholder="np. Slack, Discord, itp.">
                    </div>

                    <!-- Service Type -->
                    <div>
                        <label for="add_service_type" class="block text-sm font-medium text-gray-700">
                            Typ Integracji <span class="text-red-500">*</span>
                        </label>
                        <select name="service_type" id="add_service_type" 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Wybierz typ...</option>
                            <option value="customer_service">Obsługa Klienta</option>
                            <option value="communication">Komunikacja</option>
                            <option value="payment">Płatności</option>
                            <option value="shipping">Wysyłka</option>
                            <option value="analytics">Analityka</option>
                            <option value="other">Inne</option>
                        </select>
                    </div>

                    <!-- API URL -->
                    <div>
                        <label for="add_api_url" class="block text-sm font-medium text-gray-700">
                            API URL <span class="text-red-500">*</span>
                        </label>
                        <input type="url" name="api_url" id="add_api_url" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                               placeholder="https://example.com/api">
                    </div>

                    <!-- API Key -->
                    <div>
                        <label for="add_api_key" class="block text-sm font-medium text-gray-700">
                            API Key <span class="text-red-500">*</span>
                        </label>
                        <input type="password" name="api_key" id="add_api_key" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Wprowadź API Key">
                    </div>
                </div>
                
                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" onclick="closeAddWebhookModal()" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                        Anuluj
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                        Dodaj Webhook
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Modal functions
function openConfigModal(service) {
    const modal = document.getElementById('configModal');
    const form = document.getElementById('webhookForm');
    const title = document.getElementById('modalTitle');
    
    title.textContent = `Konfiguracja ${service.charAt(0).toUpperCase() + service.slice(1)}`;
    form.action = `/admin/settings/webhooks/${service}`;
    
    // Pre-fill form with current values
    const settings = @json($webhookSettings);
    if (settings[service]) {
        document.getElementById('modal_api_url').value = settings[service].api_url || '';
        document.getElementById('modal_enabled').checked = settings[service].enabled || false;
    }
    
    modal.classList.remove('hidden');
}

function closeConfigModal() {
    document.getElementById('configModal').classList.add('hidden');
}

function openAddWebhookModal() {
    document.getElementById('addWebhookModal').classList.remove('hidden');
}

function closeAddWebhookModal() {
    document.getElementById('addWebhookModal').classList.add('hidden');
}

function generateSecret() {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    let secret = '';
    for (let i = 0; i < 32; i++) {
        secret += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    document.getElementById('modal_webhook_secret').value = secret;
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        // Show temporary success message
        const button = event.target;
        const originalText = button.textContent;
        button.textContent = 'Skopiowano!';
        button.classList.add('text-green-600');
        setTimeout(() => {
            button.textContent = originalText;
            button.classList.remove('text-green-600');
        }, 2000);
    });
}

function testConnection(service) {
    fetch(`/admin/settings/webhooks/${service}/test`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Test połączenia udany!');
        } else {
            alert('Test połączenia nieudany: ' + data.message);
        }
    })
    .catch(error => {
        alert('Błąd podczas testowania: ' + error.message);
    });
}

function deleteWebhook(service) {
    if (confirm(`Czy na pewno chcesz usunąć webhook ${service}?`)) {
        // Implementation for webhook deletion
        alert('Funkcja usuwania webhook zostanie wkrótce zaimplementowana.');
    }
}

// Close modals when clicking outside
window.onclick = function(event) {
    const configModal = document.getElementById('configModal');
    const addModal = document.getElementById('addWebhookModal');
    
    if (event.target == configModal) {
        closeConfigModal();
    }
    if (event.target == addModal) {
        closeAddWebhookModal();
    }
}
</script>
@endsection