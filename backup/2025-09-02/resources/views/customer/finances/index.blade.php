@extends('layouts.customer')

@section('title', 'Finanse')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Finanse</h2>
            <p class="mt-1 text-sm text-gray-600">
                Zarządzaj danymi finansowymi i ustawieniami płatności.
            </p>
        </div>
        @if(auth('customer_user')->user()->canCreateUsers() || auth('customer_user')->user()->is_primary)
        <a href="{{ route('customer.finances.index') }}#edit-form" 
           class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
            <i class="fas fa-edit mr-2"></i>
            Edytuj dane finansowe
        </a>
        @endif
    </div>

    <!-- Individual Account Number -->
    <div class="bg-gradient-to-r from-blue-500 to-blue-600 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-heading font-bold mb-2">
                        <i class="fas fa-credit-card mr-2"></i>
                        Twój indywidualny rachunek
                    </h3>
                    <p class="text-blue-100 text-sm mb-4">
                        Numer rachunku bankowego dedykowany dla Twojej firmy (generowany automatycznie)
                    </p>
                    
                    <div class="bg-white/20 rounded-lg p-4 backdrop-blur-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-xs text-blue-100 mb-1">Numer rachunku:</div>
                                <code class="text-lg font-mono font-bold text-white block" id="individual-account-display">
                                    {{ $customer->formatted_individual_account }}
                                </code>
                                <code class="text-lg font-mono font-bold text-white hidden" id="individual-account-full">
                                    {{ $customer->individual_account_number }}
                                </code>
                            </div>
                            <div class="flex space-x-2">
                                <button type="button" 
                                        onclick="toggleIndividualAccount()" 
                                        class="px-3 py-1 bg-white/20 hover:bg-white/30 rounded text-xs font-medium transition-colors">
                                    <span id="toggle-individual-text">Bez spacji</span>
                                </button>
                                <button type="button" 
                                        onclick="copyIndividualAccount()" 
                                        class="px-3 py-1 bg-white/20 hover:bg-white/30 rounded text-xs font-medium transition-colors">
                                    <i class="fas fa-copy mr-1"></i>
                                    Kopiuj
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="hidden md:block">
                    <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center">
                        <i class="fas fa-university text-2xl text-white"></i>
                    </div>
                </div>
            </div>
            
            <div class="mt-4 p-3 bg-white/10 rounded-lg border border-white/20">
                <div class="flex items-start space-x-2">
                    <i class="fas fa-info-circle text-blue-200 mt-0.5 flex-shrink-0"></i>
                    <div class="text-xs text-blue-100">
                        <p class="font-medium mb-1">Informacje o rachunku indywidualnym:</p>
                        <ul class="space-y-1 text-xs">
                            <li>• Numer generowany automatycznie na bazie ID klienta ({{ $customer->id }})</li>
                            <li>• Zgodny z wymogami mBank dla systemu collect</li>
                            <li>• Używaj tego numeru do wpłat i poleceń przelewu</li>
                            <li>• Rachunek jest unikalny i przypisany tylko do Twojej firmy</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Balance Overview -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
            <h3 class="text-lg font-heading font-medium text-black-coal mb-4">
                <i class="fas fa-wallet text-skywave mr-2"></i>
                Saldo i limity
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-green-800">Saldo konta</p>
                            <p class="text-2xl font-bold text-green-600">
                                {{ number_format($customer->balance, 2, ',', ' ') }} PLN
                            </p>
                        </div>
                        <div class="h-12 w-12 bg-green-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-coins text-green-600"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-blue-800">Limit kredytowy</p>
                            <p class="text-2xl font-bold text-blue-600">
                                {{ $customer->credit_limit ? number_format($customer->credit_limit, 2, ',', ' ') . ' PLN' : 'Brak limitu' }}
                            </p>
                        </div>
                        <div class="h-12 w-12 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-credit-card text-blue-600"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-purple-50 rounded-lg p-4 border border-purple-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-purple-800">Dostępne środki</p>
                            <p class="text-2xl font-bold text-purple-600">
                                {{ number_format(($customer->balance + ($customer->credit_limit ?? 0)), 2, ',', ' ') }} PLN
                            </p>
                            @if($customer->credit_limit)
                                <p class="text-xs text-purple-600 mt-1">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Zawiera limit kredytowy: {{ number_format($customer->credit_limit, 2, ',', ' ') }} PLN
                                </p>
                            @endif
                        </div>
                        <div class="h-12 w-12 bg-purple-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-piggy-bank text-purple-600"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Financial Information -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
            <h3 class="text-lg font-heading font-medium text-black-coal mb-4">
                <i class="fas fa-university text-skywave mr-2"></i>
                Konta bankowe
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-gray-50 rounded-lg p-4">
                    <label class="block font-body text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-undo mr-2 text-orange-500"></i>
                        Konto zwrotów COD
                        <span class="text-xs text-gray-500 block mt-1">Konto bankowe do zwrotów pobrań</span>
                    </label>
                    <div class="mt-2">
                        @if($customer->cod_return_account)
                            <code class="font-mono text-sm bg-white px-3 py-2 rounded border block">
                                {{ $customer->cod_return_account }}
                            </code>
                        @else
                            <div class="text-gray-500 italic bg-white px-3 py-2 rounded border block">
                                Nie podano
                            </div>
                        @endif
                    </div>
                </div>
                
                <div class="bg-gray-50 rounded-lg p-4">
                    <label class="block font-body text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calculator mr-2 text-blue-500"></i>
                        Konto rozliczeniowe
                        <span class="text-xs text-gray-500 block mt-1">Konto bankowe do rozliczeń za przesyłki</span>
                    </label>
                    <div class="mt-2">
                        @if($customer->settlement_account)
                            <code class="font-mono text-sm bg-white px-3 py-2 rounded border block">
                                {{ $customer->settlement_account }}
                            </code>
                        @else
                            <div class="text-gray-500 italic bg-white px-3 py-2 rounded border block">
                                Nie podano
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                <div class="flex">
                    <i class="fas fa-info-circle text-blue-400 mt-0.5 mr-3"></i>
                    <div class="text-sm">
                        <p class="text-blue-800 font-medium">Informacje o kontach bankowych:</p>
                        <ul class="text-blue-700 mt-2 space-y-1 text-sm">
                            <li>• <strong>Konto COD:</strong> Zwroty pobrań będą przekazywane na to konto</li>
                            <li>• <strong>Konto rozliczeniowe:</strong> Rozliczenia za usługi kurierskie</li>
                            <li>• <strong>Numer konta:</strong> Podawaj w formacie PL + 26 cyfr lub tylko 26 cyfr</li>
                            <li>• <strong>Zmiany:</strong> Wymagają zatwierdzenia przez administratora systemu</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Form (only visible for admins) -->
    @if(auth('customer_user')->user()->canCreateUsers() || auth('customer_user')->user()->is_primary)
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg" id="edit-form">
        <div class="p-6 text-gray-900">
            <h3 class="text-lg font-heading font-medium text-black-coal mb-4">
                <i class="fas fa-edit text-skywave mr-2"></i>
                Edycja danych finansowych
            </h3>
            
            <form method="POST" action="{{ route('customer.finances.update') }}">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="cod_return_account" class="block font-body text-sm font-medium text-gray-700">
                            Konto zwrotów COD
                            <span class="text-xs text-gray-500 block mt-1">Format: PL + 26 cyfr lub tylko 26 cyfr</span>
                        </label>
                        <input type="text" 
                               name="cod_return_account" 
                               id="cod_return_account"
                               value="{{ old('cod_return_account', $customer->cod_return_account) }}"
                               placeholder="PL12345678901234567890123456"
                               pattern="(PL)?[0-9]{26}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-skywave focus:border-skywave font-mono">
                        @error('cod_return_account')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="settlement_account" class="block font-body text-sm font-medium text-gray-700">
                            Konto rozliczeniowe
                            <span class="text-xs text-gray-500 block mt-1">Format: PL + 26 cyfr lub tylko 26 cyfr</span>
                        </label>
                        <input type="text" 
                               name="settlement_account" 
                               id="settlement_account"
                               value="{{ old('settlement_account', $customer->settlement_account) }}"
                               placeholder="PL12345678901234567890123456"
                               pattern="(PL)?[0-9]{26}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-skywave focus:border-skywave font-mono">
                        @error('settlement_account')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="mt-6">
                    <button type="submit" 
                            class="inline-flex items-center px-6 py-3 bg-skywave border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-skywave/90 focus:bg-skywave/90 active:bg-skywave/80 focus:outline-none focus:ring-2 focus:ring-skywave focus:ring-offset-2 transition ease-in-out duration-150">
                        <i class="fas fa-save mr-2"></i>
                        Zapisz zmiany
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Financial Statistics -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
            <h3 class="text-lg font-heading font-medium text-black-coal mb-4">
                <i class="fas fa-chart-pie text-skywave mr-2"></i>
                Statystyki finansowe
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="text-center bg-gray-50 rounded-lg p-4">
                    <p class="text-2xl font-heading font-bold text-skywave">{{ $customer->shipments()->count() }}</p>
                    <p class="font-body text-sm text-gray-500">Łączna liczba przesyłek</p>
                </div>
                
                <div class="text-center bg-gray-50 rounded-lg p-4">
                    <p class="text-2xl font-heading font-bold text-green-600">{{ $customer->payments()->where('status', 'completed')->count() }}</p>
                    <p class="font-body text-sm text-gray-500">Zrealizowane płatności</p>
                </div>
                
                <div class="text-center bg-gray-50 rounded-lg p-4">
                    <p class="text-2xl font-heading font-bold text-orange-600">
                        {{ $customer->payments()->where('status', 'pending')->count() }}
                    </p>
                    <p class="font-body text-sm text-gray-500">Płatności oczekujące</p>
                </div>
                
                <div class="text-center bg-gray-50 rounded-lg p-4">
                    <p class="text-2xl font-heading font-bold text-blue-600">
                        {{ number_format($customer->payments()->where('status', 'completed')->sum('amount'), 2, ',', ' ') }}
                    </p>
                    <p class="font-body text-sm text-gray-500">Suma płatności (PLN)</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleIndividualAccount() {
    const display = document.getElementById('individual-account-display');
    const full = document.getElementById('individual-account-full');
    const toggleText = document.getElementById('toggle-individual-text');
    
    if (display.classList.contains('hidden')) {
        display.classList.remove('hidden');
        full.classList.add('hidden');
        toggleText.textContent = 'Bez spacji';
    } else {
        display.classList.add('hidden');
        full.classList.remove('hidden');
        toggleText.textContent = 'Ze spacjami';
    }
}

function copyIndividualAccount() {
    const display = document.getElementById('individual-account-display');
    const full = document.getElementById('individual-account-full');
    const textToCopy = display.classList.contains('hidden') ? 
        full.textContent.trim() : 
        display.textContent.trim();
    
    navigator.clipboard.writeText(textToCopy).then(function() {
        // Show temporary success message
        const button = event.target.closest('button');
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-check mr-1"></i>Skopiowano!';
        button.classList.add('bg-green-500');
        
        setTimeout(() => {
            button.innerHTML = originalText;
            button.classList.remove('bg-green-500');
        }, 2000);
    }).catch(function(err) {
        console.error('Błąd podczas kopiowania: ', err);
        alert('Nie udało się skopiować numeru rachunku');
    });
}
</script>
@endsection