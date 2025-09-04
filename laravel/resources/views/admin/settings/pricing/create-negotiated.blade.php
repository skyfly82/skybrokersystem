@extends('layouts.admin')

@section('title', 'Nowy cennik negocjowany')

@section('content')
<div class="max-w-6xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="md:flex md:items-center md:justify-between mb-6">
        <div class="flex-1 min-w-0">
            <nav class="flex mb-4" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('admin.settings.index') }}" class="text-gray-500 hover:text-gray-700">Ustawienia</a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <a href="{{ route('admin.settings.pricing.negotiated') }}" class="ml-1 text-gray-500 hover:text-gray-700 md:ml-2">Cenniki negocjowane</a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="ml-1 text-gray-700 md:ml-2">Nowy cennik</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                Utwórz cennik negocjowany
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Indywidualny cennik z analizą kosztów i przewidywaną rentowności
            </p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.settings.pricing.negotiated.store') }}" class="space-y-6">
        @csrf
        
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Main Form -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Customer Selection -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Wybór klienta</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <label for="customer_search" class="block text-sm font-medium text-gray-700">Wyszukaj klienta</label>
                                <div class="mt-1 relative">
                                    <input type="text" name="customer_search" id="customer_search" 
                                           class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                           placeholder="Nazwa firmy lub NIP...">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </div>
                                </div>
                                
                                <!-- Search Results -->
                                <div class="mt-2 max-h-60 overflow-y-auto border border-gray-300 rounded-md bg-white hidden" id="customer-results">
                                    <div class="p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100">
                                        <div class="flex items-center">
                                            <div class="h-8 w-8 bg-blue-500 rounded-full flex items-center justify-center mr-3">
                                                <span class="text-white text-xs font-bold">AL</span>
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">Allegro Sp. z o.o.</div>
                                                <div class="text-xs text-gray-500">NIP: 9512394488 • 2,840 paczek/mies.</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label for="segment" class="block text-sm font-medium text-gray-700">Segment klienta</label>
                                <select name="segment" id="segment" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Wybierz segment</option>
                                    <option value="premium">Premium (>1000 paczek/mies.)</option>
                                    <option value="corporate">Corporate (500-1000 paczek/mies.)</option>
                                    <option value="sme">SME (<500 paczek/mies.)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pricing Configuration -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Konfiguracja cenowa</h3>
                        <p class="mt-1 text-sm text-gray-500">Określ ceny i marże dla wybranych usług</p>
                    </div>
                    <div class="p-6">
                        <!-- Base Matrix Selection -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-3">Bazuj na macierzy</label>
                            <div class="grid grid-cols-3 gap-4">
                                <label class="relative flex cursor-pointer rounded-lg border border-gray-300 bg-white p-4 shadow-sm focus:outline-none hover:border-blue-300">
                                    <input type="radio" name="base_matrix" value="base" class="sr-only" checked>
                                    <span class="flex flex-1">
                                        <span class="flex flex-col">
                                            <span class="block text-sm font-medium text-gray-900">BASE</span>
                                            <span class="mt-1 flex items-center text-sm text-gray-500">Standardowe ceny</span>
                                        </span>
                                    </span>
                                    <div class="absolute -inset-px rounded-lg border-2 border-blue-600 pointer-events-none opacity-100"></div>
                                </label>
                                
                                <label class="relative flex cursor-pointer rounded-lg border border-gray-300 bg-white p-4 shadow-sm focus:outline-none hover:border-blue-300">
                                    <input type="radio" name="base_matrix" value="heavy_user" class="sr-only">
                                    <span class="flex flex-1">
                                        <span class="flex flex-col">
                                            <span class="block text-sm font-medium text-gray-900">HEAVY USER</span>
                                            <span class="mt-1 flex items-center text-sm text-gray-500">Ceny dla stałych klientów</span>
                                        </span>
                                    </span>
                                </label>
                                
                                <label class="relative flex cursor-pointer rounded-lg border border-gray-300 bg-white p-4 shadow-sm focus:outline-none hover:border-blue-300">
                                    <input type="radio" name="base_matrix" value="client" class="sr-only">
                                    <span class="flex flex-1">
                                        <span class="flex flex-col">
                                            <span class="block text-sm font-medium text-gray-900">KLIENCJA</span>
                                            <span class="mt-1 flex items-center text-sm text-gray-500">Najlepsze ceny</span>
                                        </span>
                                    </span>
                                </label>
                            </div>
                        </div>

                        <!-- Discount/Markup -->
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <label for="price_adjustment" class="block text-sm font-medium text-gray-700">Korekta cenowa</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <select name="adjustment_type" class="absolute inset-y-0 left-0 h-full py-0 pl-3 pr-7 border-transparent bg-transparent text-gray-500 sm:text-sm rounded-l-md focus:ring-blue-500 focus:border-blue-500">
                                        <option value="discount">Zniżka</option>
                                        <option value="markup">Narzut</option>
                                    </select>
                                    <input type="number" name="price_adjustment" step="0.1" placeholder="0.0"
                                           class="block w-full pl-20 pr-12 border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">%</span>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label for="minimum_margin" class="block text-sm font-medium text-gray-700">Minimalna marża</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <input type="number" name="minimum_margin" step="0.1" value="5.0" min="0"
                                           class="block w-full pr-12 border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">%</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Service Selection -->
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700 mb-3">Usługi do negocjacji</label>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                                    <div class="flex items-center">
                                        <input type="checkbox" name="services[]" value="inpost_parcel_a" checked class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900">InPost Paczkomat A</div>
                                            <div class="text-xs text-gray-500">Standardowa cena: 12.99 zł</div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm font-medium text-gray-900" id="price_inpost_parcel_a">12.99 zł</div>
                                        <div class="text-xs text-gray-500">Marża: 15%</div>
                                    </div>
                                </div>
                                
                                <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                                    <div class="flex items-center">
                                        <input type="checkbox" name="services[]" value="inpost_parcel_b" checked class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900">InPost Paczkomat B</div>
                                            <div class="text-xs text-gray-500">Standardowa cena: 14.99 zł</div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm font-medium text-gray-900" id="price_inpost_parcel_b">14.99 zł</div>
                                        <div class="text-xs text-gray-500">Marża: 15%</div>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                                    <div class="flex items-center">
                                        <input type="checkbox" name="services[]" value="inpost_courier" checked class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900">InPost Kurier</div>
                                            <div class="text-xs text-gray-500">Standardowa cena: 18.99 zł</div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm font-medium text-gray-900" id="price_inpost_courier">18.99 zł</div>
                                        <div class="text-xs text-gray-500">Marża: 12%</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contract Terms -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Warunki umowy</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <label for="valid_from" class="block text-sm font-medium text-gray-700">Ważny od</label>
                                <input type="date" name="valid_from" id="valid_from" required
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label for="valid_to" class="block text-sm font-medium text-gray-700">Ważny do</label>
                                <input type="date" name="valid_to" id="valid_to" required
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label for="minimum_volume" class="block text-sm font-medium text-gray-700">Minimalny wolumen (paczki/miesiąc)</label>
                                <input type="number" name="minimum_volume" id="minimum_volume" min="0"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label for="payment_terms" class="block text-sm font-medium text-gray-700">Warunki płatności</label>
                                <select name="payment_terms" id="payment_terms" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="prepaid">Przedpłata</option>
                                    <option value="7_days">7 dni</option>
                                    <option value="14_days" selected>14 dni</option>
                                    <option value="30_days">30 dni</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mt-6">
                            <label for="notes" class="block text-sm font-medium text-gray-700">Notatki</label>
                            <textarea name="notes" id="notes" rows="3" 
                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                      placeholder="Dodatkowe warunki, ustalenia z klientem..."></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar - Cost Analysis -->
            <div class="lg:col-span-1">
                <div class="space-y-6">
                    <!-- Real-time Profitability -->
                    <div class="bg-white shadow rounded-lg p-6 sticky top-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Analiza rentowności</h3>
                        
                        <!-- Profitability Meter -->
                        <div class="mb-6">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm text-gray-500">Przewidywana marża</span>
                                <span class="text-lg font-bold text-green-600" id="predicted-margin">12.4%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div class="bg-green-500 h-3 rounded-full transition-all duration-300" style="width: 62%" id="margin-bar"></div>
                            </div>
                            <div class="flex justify-between text-xs text-gray-500 mt-1">
                                <span>0%</span>
                                <span>20%</span>
                            </div>
                        </div>

                        <!-- Financial Summary -->
                        <div class="space-y-3">
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <span class="text-sm text-gray-600">Przewidywany przychód</span>
                                <span class="font-medium text-gray-900" id="predicted-revenue">24,500 zł/mies.</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <span class="text-sm text-gray-600">Koszt dostawy</span>
                                <span class="font-medium text-gray-900" id="delivery-cost">21,460 zł/mies.</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <span class="text-sm text-gray-600">Zysk brutto</span>
                                <span class="font-bold text-green-600" id="gross-profit">3,040 zł/mies.</span>
                            </div>
                        </div>

                        <!-- Risk Assessment -->
                        <div class="mt-6 p-3 bg-yellow-50 rounded-lg">
                            <div class="flex items-center mb-2">
                                <svg class="h-4 w-4 text-yellow-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                <span class="text-sm font-medium text-yellow-800">Analiza ryzyka</span>
                            </div>
                            <ul class="text-xs text-yellow-700 space-y-1">
                                <li id="risk-margin">• Marża powyżej minimum (5%)</li>
                                <li id="risk-volume">• Wolumen do weryfikacji</li>
                                <li id="risk-payment">• Warunki płatności: średnie ryzyko</li>
                            </ul>
                        </div>

                        <!-- Cost Breakdown -->
                        <div class="mt-6">
                            <h4 class="text-sm font-medium text-gray-900 mb-3">Struktura kosztów</h4>
                            <div class="space-y-2">
                                <div class="flex justify-between text-xs">
                                    <span class="text-gray-600">Koszt kuriera</span>
                                    <span class="text-gray-900">85.2%</span>
                                </div>
                                <div class="flex justify-between text-xs">
                                    <span class="text-gray-600">Obsługa klienta</span>
                                    <span class="text-gray-900">3.8%</span>
                                </div>
                                <div class="flex justify-between text-xs">
                                    <span class="text-gray-600">System/IT</span>
                                    <span class="text-gray-900">2.1%</span>
                                </div>
                                <div class="flex justify-between text-xs">
                                    <span class="text-gray-600">Administracja</span>
                                    <span class="text-gray-900">1.5%</span>
                                </div>
                                <div class="flex justify-between text-xs font-medium border-t border-gray-200 pt-2">
                                    <span class="text-gray-900">Zysk</span>
                                    <span class="text-green-600">7.4%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex justify-end space-x-3">
            <a href="{{ route('admin.settings.pricing.negotiated') }}" 
               class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Anuluj
            </a>
            <button type="submit" name="action" value="draft"
                    class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Zapisz jako szkic
            </button>
            <button type="submit" name="action" value="create"
                    class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                Utwórz cennik
            </button>
        </div>
    </form>
</div>

<script>
// Real-time profitability calculation
function updateProfitability() {
    const baseMatrix = document.querySelector('input[name="base_matrix"]:checked').value;
    const adjustment = parseFloat(document.querySelector('input[name="price_adjustment"]').value) || 0;
    const adjustmentType = document.querySelector('select[name="adjustment_type"]').value;
    
    // Mock calculation - in real implementation, fetch from backend
    let baseMargin = 15; // Base margin percentage
    let newMargin = baseMargin;
    
    if (adjustmentType === 'discount') {
        newMargin = baseMargin - adjustment;
    } else {
        newMargin = baseMargin + adjustment;
    }
    
    // Update UI
    document.getElementById('predicted-margin').textContent = newMargin.toFixed(1) + '%';
    document.getElementById('margin-bar').style.width = (newMargin / 20 * 100) + '%';
    
    // Update margin bar color
    const marginBar = document.getElementById('margin-bar');
    if (newMargin < 5) {
        marginBar.className = 'bg-red-500 h-3 rounded-full transition-all duration-300';
        document.getElementById('predicted-margin').className = 'text-lg font-bold text-red-600';
    } else if (newMargin < 10) {
        marginBar.className = 'bg-yellow-500 h-3 rounded-full transition-all duration-300';
        document.getElementById('predicted-margin').className = 'text-lg font-bold text-yellow-600';
    } else {
        marginBar.className = 'bg-green-500 h-3 rounded-full transition-all duration-300';
        document.getElementById('predicted-margin').className = 'text-lg font-bold text-green-600';
    }
    
    // Update risk assessment
    const riskMargin = document.getElementById('risk-margin');
    if (newMargin < 5) {
        riskMargin.textContent = '• Marża poniżej minimum! Wysokie ryzyko';
        riskMargin.className = 'text-red-700';
    } else if (newMargin < 8) {
        riskMargin.textContent = '• Niska marża, średnie ryzyko';
        riskMargin.className = 'text-yellow-700';
    } else {
        riskMargin.textContent = '• Marża powyżej minimum (5%)';
        riskMargin.className = 'text-green-700';
    }
}

// Listen for changes
document.querySelectorAll('input[name="base_matrix"], input[name="price_adjustment"], select[name="adjustment_type"]').forEach(input => {
    input.addEventListener('change', updateProfitability);
    input.addEventListener('input', updateProfitability);
});

// Customer search functionality
document.getElementById('customer_search').addEventListener('input', function() {
    const results = document.getElementById('customer-results');
    if (this.value.length > 2) {
        results.classList.remove('hidden');
    } else {
        results.classList.add('hidden');
    }
});

// Initialize
updateProfitability();
</script>
@endsection