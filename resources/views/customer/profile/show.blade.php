@extends('layouts.customer')

@section('title', 'Profil')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Profil</h2>
            <p class="mt-1 text-sm text-gray-600">
                Zarządzaj swoimi danymi i ustawieniami konta.
            </p>
        </div>
        @if(auth('customer_user')->user()->canCreateUsers() || auth('customer_user')->user()->is_primary)
        <a href="{{ route('customer.profile.edit') }}" 
           class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
            <i class="fas fa-edit mr-2"></i>
            Edytuj profil
        </a>
        @endif
    </div>
        <!-- User Information -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h3 class="text-lg font-heading font-medium text-black-coal mb-4">Dane użytkownika</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block font-body text-sm font-medium text-gray-700">Imię</label>
                        <p class="mt-1 font-body text-sm text-gray-900">{{ $user->first_name }}</p>
                    </div>
                    
                    <div>
                        <label class="block font-body text-sm font-medium text-gray-700">Nazwisko</label>
                        <p class="mt-1 font-body text-sm text-gray-900">{{ $user->last_name }}</p>
                    </div>
                    
                    <div>
                        <label class="block font-body text-sm font-medium text-gray-700">Email</label>
                        <p class="mt-1 font-body text-sm text-gray-900">{{ $user->email }}</p>
                        @if($user->email_verified_at)
                            <p class="text-xs text-green-600 mt-1">
                                <i class="fas fa-check-circle mr-1"></i>
                                Zweryfikowany {{ $user->email_verified_at->format('d.m.Y') }}
                            </p>
                        @else
                            <p class="text-xs text-red-600 mt-1">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                Nie zweryfikowany
                            </p>
                        @endif
                    </div>
                    
                    <div>
                        <label class="block font-body text-sm font-medium text-gray-700">Telefon</label>
                        <p class="mt-1 font-body text-sm text-gray-900">{{ $user->phone ?: 'Nie podano' }}</p>
                    </div>
                    
                    <div>
                        <label class="block font-body text-sm font-medium text-gray-700">Rola</label>
                        <p class="mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-body font-medium 
                                {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ $user->role === 'admin' ? 'Administrator' : 'Użytkownik' }}
                            </span>
                            @if($user->is_primary)
                                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-body font-medium bg-green-100 text-green-800">
                                    Konto główne
                                </span>
                            @endif
                        </p>
                    </div>
                    
                    <div>
                        <label class="block font-body text-sm font-medium text-gray-700">Ostatnie logowanie</label>
                        <p class="mt-1 font-body text-sm text-gray-900">
                            {{ $user->last_login_at ? $user->last_login_at->format('d.m.Y H:i') : 'Nigdy' }}
                        </p>
                        @if($user->last_login_ip)
                            <p class="text-xs text-gray-500">IP: {{ $user->last_login_ip }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Company Information -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h3 class="text-lg font-heading font-medium text-black-coal mb-4">Dane firmy</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block font-body text-sm font-medium text-gray-700">Nazwa firmy</label>
                        <p class="mt-1 font-body text-sm text-gray-900">{{ $customer->company_name }}</p>
                    </div>
                    
                    <div>
                        <label class="block font-body text-sm font-medium text-gray-700">NIP</label>
                        <p class="mt-1 font-body text-sm text-gray-900">{{ $customer->tax_number ?: 'Nie podano' }}</p>
                    </div>
                    
                    <div>
                        <label class="block font-body text-sm font-medium text-gray-700">Email firmy</label>
                        <p class="mt-1 font-body text-sm text-gray-900">{{ $customer->email }}</p>
                        @if($customer->email_verified)
                            <p class="text-xs text-green-600 mt-1">
                                <i class="fas fa-check-circle mr-1"></i>
                                Zweryfikowany
                            </p>
                        @else
                            <p class="text-xs text-red-600 mt-1">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                Nie zweryfikowany
                            </p>
                        @endif
                    </div>
                    
                    <div>
                        <label class="block font-body text-sm font-medium text-gray-700">Status konta</label>
                        <p class="mt-1">
                            @switch($customer->status)
                                @case('active')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-body font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        Aktywne
                                    </span>
                                    @break
                                @case('pending')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-body font-medium bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-clock mr-1"></i>
                                        Oczekuje zatwierdzenia
                                    </span>
                                    @break
                                @case('suspended')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-body font-medium bg-red-100 text-red-800">
                                        <i class="fas fa-ban mr-1"></i>
                                        Zawieszone
                                    </span>
                                    @break
                            @endswitch
                        </p>
                    </div>
                    
                    <div>
                        <label class="block font-body text-sm font-medium text-gray-700">Saldo konta</label>
                        <p class="mt-1 font-body text-lg font-semibold text-green-600">
                            {{ number_format($customer->balance, 2, ',', ' ') }} PLN
                        </p>
                    </div>
                    
                    <div>
                        <label class="block font-body text-sm font-medium text-gray-700">Limit kredytowy</label>
                        <p class="mt-1 font-body text-sm text-gray-900">
                            {{ $customer->credit_limit ? number_format($customer->credit_limit, 2, ',', ' ') . ' PLN' : 'Brak limitu' }}
                        </p>
                    </div>
                    
                    <div>
                        <label class="block font-body text-sm font-medium text-gray-700">Telefon firmy</label>
                        <p class="mt-1 font-body text-sm text-gray-900">{{ $customer->phone ?: 'Nie podano' }}</p>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block font-body text-sm font-medium text-gray-700">Adres firmy</label>
                        <p class="mt-1 font-body text-sm text-gray-900">{{ $customer->address ?: 'Nie podano' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Financial Information -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h3 class="text-lg font-heading font-medium text-black-coal mb-4">Dane finansowe</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block font-body text-sm font-medium text-gray-700">
                            Konto zwrotów COD
                            <span class="text-xs text-gray-500 block mt-1">Konto bankowe do zwrotów pobrań</span>
                        </label>
                        <p class="mt-1 font-body text-sm text-gray-900">
                            {{ $customer->cod_return_account ? $customer->cod_return_account : 'Nie podano' }}
                        </p>
                    </div>
                    
                    <div>
                        <label class="block font-body text-sm font-medium text-gray-700">
                            Konto rozliczeniowe
                            <span class="text-xs text-gray-500 block mt-1">Konto bankowe do rozliczeń za przesyłki</span>
                        </label>
                        <p class="mt-1 font-body text-sm text-gray-900">
                            {{ $customer->settlement_account ? $customer->settlement_account : 'Nie podano' }}
                        </p>
                    </div>
                </div>
                
                <div class="mt-4 p-3 bg-blue-50 rounded-lg border border-blue-200">
                    <div class="flex">
                        <i class="fas fa-info-circle text-blue-400 mt-0.5 mr-2"></i>
                        <div class="text-sm">
                            <p class="text-blue-800 font-medium">Informacje o kontach bankowych:</p>
                            <ul class="text-blue-700 mt-1 space-y-1 text-xs">
                                <li>• Konto COD - zwroty pobrań będą przekazywane na to konto</li>
                                <li>• Konto rozliczeniowe - rozliczenia za usługi kurierskie</li>
                                <li>• Te dane możesz edytować w ustawieniach profilu</li>
                                <li>• Zmiany wymagają zatwierdzenia przez administratora</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Account Statistics -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h3 class="text-lg font-heading font-medium text-black-coal mb-4">Statystyki konta</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center">
                        <p class="text-2xl font-heading font-bold text-skywave">{{ $customer->shipments()->count() }}</p>
                        <p class="font-body text-sm text-gray-500">Łączna liczba przesyłek</p>
                    </div>
                    
                    <div class="text-center">
                        <p class="text-2xl font-heading font-bold text-green-600">{{ $customer->payments()->where('status', 'completed')->count() }}</p>
                        <p class="font-body text-sm text-gray-500">Zrealizowane płatności</p>
                    </div>
                    
                    <div class="text-center">
                        <p class="text-2xl font-heading font-bold text-purple-600">
                            {{ $customer->users()->where('is_active', true)->count() }}
                        </p>
                        <p class="font-body text-sm text-gray-500">Aktywni użytkownicy</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Management Logs Section (only for admins) -->
        @if($user->canCreateUsers() || $user->is_primary)
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h3 class="text-lg font-heading font-medium text-black-coal mb-4">
                    <i class="fas fa-history text-skywave mr-2"></i>
                    Logi zarządzania
                </h3>
                
                <!-- Tabs -->
                <div class="border-b border-gray-200 mb-4" x-data="{ activeTab: 'changes' }">
                    <nav class="-mb-px flex space-x-8">
                        <button @click="activeTab = 'changes'" 
                                class="py-2 px-1 border-b-2 font-medium text-sm"
                                :class="activeTab === 'changes' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'">
                            Zmiany w profilu
                        </button>
                        <button @click="activeTab = 'logins'" 
                                class="py-2 px-1 border-b-2 font-medium text-sm"
                                :class="activeTab === 'logins' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'">
                            Ostatnie logowania
                        </button>
                    </nav>
                </div>
                
                <!-- Changes Tab Content -->
                <div x-show="activeTab === 'changes'" class="space-y-3">
                    @forelse($recentLogs as $log)
                    <div class="flex items-start justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                <i class="fas fa-{{ $log->event === 'updated' ? 'edit' : ($log->event === 'created' ? 'plus' : 'minus') }} text-{{ $log->event === 'updated' ? 'blue' : ($log->event === 'created' ? 'green' : 'red') }}-500 mt-1"></i>
                            </div>
                            <div class="flex-1">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $log->formatted_event }}
                                    @if($log->auditable_type === 'App\\Models\\Customer')
                                        danych firmy
                                    @else
                                        użytkownika
                                    @endif
                                </div>
                                <div class="text-xs text-gray-500">
                                    przez {{ $log->user_name }} ({{ $log->user_email }})
                                    @if($log->user_type === 'system_user')
                                        <span class="text-orange-600 font-medium">- Administrator SkyBroker</span>
                                    @endif
                                </div>
                                @if($log->description)
                                    <div class="text-xs text-gray-600 mt-1">{{ $log->description }}</div>
                                @endif
                                @if($log->changed_fields && count($log->changed_fields) > 0)
                                    <div class="text-xs text-gray-600 mt-1">
                                        Zmienione pola: 
                                        @foreach($log->changed_fields as $field => $change)
                                            <span class="font-medium">{{ $log->getFieldLabelAttribute($field) }}</span>@if(!$loop->last), @endif
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="text-xs text-gray-500 ml-3">
                            {{ $log->created_at->format('d.m.Y H:i') }}
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-history text-gray-300 text-3xl mb-2"></i>
                        <p>Brak ostatnich zmian w profilu</p>
                    </div>
                    @endforelse
                </div>
                
                <!-- Logins Tab Content -->
                <div x-show="activeTab === 'logins'" x-cloak class="space-y-3">
                    @forelse($loginLogs as $log)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0 h-8 w-8 rounded-full bg-green-100 flex items-center justify-center">
                                <i class="fas fa-sign-in-alt text-green-600 text-sm"></i>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ $log->user_name }}</div>
                                <div class="text-xs text-gray-500">{{ $log->user_email }}</div>
                                @if($log->ip_address)
                                    <div class="text-xs text-gray-500">IP: {{ $log->ip_address }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="text-xs text-gray-500">
                            {{ $log->created_at->format('d.m.Y H:i') }}
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-sign-in-alt text-gray-300 text-3xl mb-2"></i>
                        <p>Brak ostatnich logowań</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
        @endif

        <!-- API Information (only for primary user) -->
        @if($user->is_primary && $customer->api_key)
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h3 class="text-lg font-heading font-medium text-black-coal mb-4">Dostęp API</h3>
                
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <label class="block font-body text-sm font-medium text-gray-700 mb-1">Klucz API</label>
                            <code class="font-mono text-sm text-gray-900 bg-white px-2 py-1 rounded" id="api-key-display">
                                {{ substr($customer->api_key, 0, 8) . str_repeat('*', strlen($customer->api_key) - 16) . substr($customer->api_key, -8) }}
                            </code>
                            <code class="font-mono text-sm text-gray-900 bg-white px-2 py-1 rounded hidden" id="api-key-full">
                                {{ $customer->api_key }}
                            </code>
                        </div>
                        <button type="button" onclick="toggleApiKey()" class="ml-4 text-skywave hover:text-skywave/80 font-body text-sm">
                            <span id="toggle-text">Pokaż</span>
                        </button>
                    </div>
                    <p class="font-body text-xs text-gray-500 mt-2">
                        Używaj tego klucza w nagłówku: <code class="bg-white px-1 rounded">X-API-Key: {{ substr($customer->api_key, 0, 8) }}...</code>
                    </p>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<script>
function toggleApiKey() {
    const display = document.getElementById('api-key-display');
    const full = document.getElementById('api-key-full');
    const toggleText = document.getElementById('toggle-text');
    
    if (display.classList.contains('hidden')) {
        display.classList.remove('hidden');
        full.classList.add('hidden');
        toggleText.textContent = 'Pokaż';
    } else {
        display.classList.add('hidden');
        full.classList.remove('hidden');
        toggleText.textContent = 'Ukryj';
    }
}
</script>
@endsection