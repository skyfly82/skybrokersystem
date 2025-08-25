@extends('layouts.customer')

@section('title', 'Profil')

@section('header')
    <div class="flex items-center justify-between">
        <h2 class="font-heading font-semibold text-xl text-black-coal leading-tight">
            {{ __('Profil') }}
        </h2>
        <a href="{{ route('customer.profile.edit') }}" 
           class="inline-flex items-center px-4 py-2 bg-skywave border border-transparent rounded-md font-body text-xs text-white uppercase tracking-widest hover:bg-skywave/90 focus:bg-skywave/90 active:bg-skywave/90 focus:outline-none focus:ring-2 focus:ring-skywave focus:ring-offset-2 transition ease-in-out duration-150">
            <i class="fas fa-edit mr-2"></i>
            Edytuj profil
        </a>
    </div>
@endsection

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
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