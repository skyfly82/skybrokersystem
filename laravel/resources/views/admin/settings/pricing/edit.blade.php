@extends('layouts.admin')

@section('title', 'Konfiguracja macierzy cenowej')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
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
                            <a href="{{ route('admin.settings.pricing') }}" class="ml-1 text-gray-500 hover:text-gray-700 md:ml-2">Macierze cenowe</a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="ml-1 text-gray-700 md:ml-2">InPost Paczkomat</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate flex items-center">
                <div class="h-10 w-10 bg-yellow-500 rounded-lg flex items-center justify-center mr-3">
                    <span class="text-white font-bold text-sm">InP</span>
                </div>
                InPost Paczkomat - Macierz cenowa
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Konfiguracja cen dla gabarytów A, B, C oraz poziomów klientów BASE, HEAVY USER, KLIENCJA
            </p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3">
            <button type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Zaplanuj zmianę
            </button>
            <button type="submit" form="pricing-matrix-form" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Zapisz macierz
            </button>
        </div>
    </div>

    <form id="pricing-matrix-form" method="POST" action="{{ route('admin.settings.pricing.update', $pricing_id) }}">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-4">
            <!-- Configuration Sidebar -->
            <div class="lg:col-span-1">
                <div class="space-y-6">
                    <!-- General Settings -->
                    <div class="bg-white shadow rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Ustawienia ogólne</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Status macierzy</label>
                                <select name="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">
                                    <option value="active" selected>Aktywna</option>
                                    <option value="inactive">Nieaktywna</option>
                                    <option value="scheduled">Zaplanowana</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Typ klienta domyślny</label>
                                <select name="default_customer_type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">
                                    <option value="base" selected>BASE</option>
                                    <option value="heavy_user">HEAVY USER</option>
                                    <option value="client">KLIENCJA</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Waluta</label>
                                <select name="currency" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">
                                    <option value="PLN" selected>PLN</option>
                                    <option value="EUR">EUR</option>
                                </select>
                            </div>
                            
                            <div class="flex items-center">
                                <input type="checkbox" name="vat_included" id="vat_included" checked class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="vat_included" class="ml-2 text-sm text-gray-900">Ceny zawierają VAT</label>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-white shadow rounded-lg p-6">
                        <h4 class="text-sm font-medium text-gray-900 mb-3">Szybkie akcje</h4>
                        <div class="space-y-2">
                            <button type="button" class="w-full text-left px-3 py-2 text-sm rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100">
                                Kopiuj ceny z BASE do HEAVY USER
                            </button>
                            <button type="button" class="w-full text-left px-3 py-2 text-sm rounded-lg hover:bg-gray-50">
                                Zastosuj zniżkę -5% dla KLIENCJA
                            </button>
                            <button type="button" class="w-full text-left px-3 py-2 text-sm rounded-lg hover:bg-gray-50">
                                Reset do cen fabrycznych
                            </button>
                            <hr class="my-2">
                            <button type="button" class="w-full text-left px-3 py-2 text-sm rounded-lg hover:bg-gray-50">
                                Import z Excel
                            </button>
                            <button type="button" class="w-full text-left px-3 py-2 text-sm rounded-lg hover:bg-gray-50">
                                Eksport do Excel
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="lg:col-span-3">
                <!-- Base Prices Matrix -->
                <div class="bg-white shadow rounded-lg mb-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Macierz cenowa podstawowa</h3>
                        <p class="mt-1 text-sm text-gray-500">Ceny według gabarytów i poziomów klientów</p>
                    </div>
                    <div class="p-6">
                        <!-- Individual vs Business Toggle -->
                        <div class="mb-6">
                            <div class="flex items-center space-x-6">
                                <div class="flex items-center">
                                    <input type="radio" name="pricing_type" value="individual" id="individual" checked class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                    <label for="individual" class="ml-2 text-sm font-medium text-gray-900">Klienci indywidualni</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="radio" name="pricing_type" value="business" id="business" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                    <label for="business" class="ml-2 text-sm font-medium text-gray-900">Klienci biznesowi</label>
                                </div>
                            </div>
                        </div>

                        <!-- Pricing Matrix Table -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead>
                                    <tr class="border-b border-gray-200">
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">
                                            Gabaryt
                                        </th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            BASE<br><span class="text-xs normal-case text-gray-400">(0-50 paczek/mies.)</span>
                                        </th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            HEAVY USER<br><span class="text-xs normal-case text-gray-400">(51-200 paczek/mies.)</span>
                                        </th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            KLIENCJA<br><span class="text-xs normal-case text-gray-400">(200+ paczek/mies.)</span>
                                        </th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Różnica %
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <!-- Gabaryt A -->
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-4">
                                            <div class="flex items-center">
                                                <div class="h-8 w-8 bg-green-100 rounded flex items-center justify-center mr-2">
                                                    <span class="text-green-700 font-bold text-sm">A</span>
                                                </div>
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900">Gabaryt A</div>
                                                    <div class="text-xs text-gray-500">8×38×64 cm</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <div class="relative">
                                                <input type="number" name="prices[A][base]" value="12.99" step="0.01" 
                                                       class="w-20 text-center border-gray-300 rounded text-sm focus:ring-blue-500 focus:border-blue-500">
                                                <span class="text-xs text-gray-500 ml-1">zł</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <div class="relative">
                                                <input type="number" name="prices[A][heavy_user]" value="11.99" step="0.01" 
                                                       class="w-20 text-center border-gray-300 rounded text-sm focus:ring-blue-500 focus:border-blue-500">
                                                <span class="text-xs text-gray-500 ml-1">zł</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <div class="relative">
                                                <input type="number" name="prices[A][client]" value="10.99" step="0.01" 
                                                       class="w-20 text-center border-gray-300 rounded text-sm focus:ring-blue-500 focus:border-blue-500">
                                                <span class="text-xs text-gray-500 ml-1">zł</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <span class="text-sm text-green-600 font-medium">-15.4%</span>
                                        </td>
                                    </tr>
                                    
                                    <!-- Gabaryt B -->
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-4">
                                            <div class="flex items-center">
                                                <div class="h-8 w-8 bg-yellow-100 rounded flex items-center justify-center mr-2">
                                                    <span class="text-yellow-700 font-bold text-sm">B</span>
                                                </div>
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900">Gabaryt B</div>
                                                    <div class="text-xs text-gray-500">19×38×64 cm</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <div class="relative">
                                                <input type="number" name="prices[B][base]" value="14.99" step="0.01" 
                                                       class="w-20 text-center border-gray-300 rounded text-sm focus:ring-blue-500 focus:border-blue-500">
                                                <span class="text-xs text-gray-500 ml-1">zł</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <div class="relative">
                                                <input type="number" name="prices[B][heavy_user]" value="13.99" step="0.01" 
                                                       class="w-20 text-center border-gray-300 rounded text-sm focus:ring-blue-500 focus:border-blue-500">
                                                <span class="text-xs text-gray-500 ml-1">zł</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <div class="relative">
                                                <input type="number" name="prices[B][client]" value="12.99" step="0.01" 
                                                       class="w-20 text-center border-gray-300 rounded text-sm focus:ring-blue-500 focus:border-blue-500">
                                                <span class="text-xs text-gray-500 ml-1">zł</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <span class="text-sm text-green-600 font-medium">-13.3%</span>
                                        </td>
                                    </tr>
                                    
                                    <!-- Gabaryt C -->
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-4">
                                            <div class="flex items-center">
                                                <div class="h-8 w-8 bg-red-100 rounded flex items-center justify-center mr-2">
                                                    <span class="text-red-700 font-bold text-sm">C</span>
                                                </div>
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900">Gabaryt C</div>
                                                    <div class="text-xs text-gray-500">41×38×64 cm</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <div class="relative">
                                                <input type="number" name="prices[C][base]" value="18.99" step="0.01" 
                                                       class="w-20 text-center border-gray-300 rounded text-sm focus:ring-blue-500 focus:border-blue-500">
                                                <span class="text-xs text-gray-500 ml-1">zł</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <div class="relative">
                                                <input type="number" name="prices[C][heavy_user]" value="17.99" step="0.01" 
                                                       class="w-20 text-center border-gray-300 rounded text-sm focus:ring-blue-500 focus:border-blue-500">
                                                <span class="text-xs text-gray-500 ml-1">zł</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <div class="relative">
                                                <input type="number" name="prices[C][client]" value="16.99" step="0.01" 
                                                       class="w-20 text-center border-gray-300 rounded text-sm focus:ring-blue-500 focus:border-blue-500">
                                                <span class="text-xs text-gray-500 ml-1">zł</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <span class="text-sm text-green-600 font-medium">-10.5%</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Additional Services -->
                <div class="bg-white shadow rounded-lg mb-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Usługi dodatkowe</h3>
                        <p class="mt-1 text-sm text-gray-500">Dopłaty za dodatkowe usługi</p>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                            <!-- COD -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="text-sm font-medium text-gray-900">Pobranie (COD)</h4>
                                    <input type="checkbox" name="services[cod][enabled]" checked class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                </div>
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-1">Opłata stała</label>
                                        <div class="flex items-center">
                                            <input type="number" name="services[cod][fixed_fee]" value="3.99" step="0.01" 
                                                   class="w-20 text-sm border-gray-300 rounded-l-md focus:ring-blue-500 focus:border-blue-500">
                                            <span class="px-3 py-2 bg-gray-50 border border-l-0 border-gray-300 rounded-r-md text-sm text-gray-500">zł</span>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-1">Opłata procentowa</label>
                                        <div class="flex items-center">
                                            <input type="number" name="services[cod][percentage_fee]" value="0.5" step="0.1" 
                                                   class="w-20 text-sm border-gray-300 rounded-l-md focus:ring-blue-500 focus:border-blue-500">
                                            <span class="px-3 py-2 bg-gray-50 border border-l-0 border-gray-300 rounded-r-md text-sm text-gray-500">%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Insurance -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="text-sm font-medium text-gray-900">Ubezpieczenie</h4>
                                    <input type="checkbox" name="services[insurance][enabled]" checked class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                </div>
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-1">Stawka</label>
                                        <div class="flex items-center">
                                            <input type="number" name="services[insurance][rate]" value="1.0" step="0.1" 
                                                   class="w-20 text-sm border-gray-300 rounded-l-md focus:ring-blue-500 focus:border-blue-500">
                                            <span class="px-3 py-2 bg-gray-50 border border-l-0 border-gray-300 rounded-r-md text-sm text-gray-500">% wartości</span>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-1">Min. opłata</label>
                                        <div class="flex items-center">
                                            <input type="number" name="services[insurance][min_fee]" value="2.00" step="0.01" 
                                                   class="w-20 text-sm border-gray-300 rounded-l-md focus:ring-blue-500 focus:border-blue-500">
                                            <span class="px-3 py-2 bg-gray-50 border border-l-0 border-gray-300 rounded-r-md text-sm text-gray-500">zł</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Weekend Delivery -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="text-sm font-medium text-gray-900">Paczka w weekend</h4>
                                    <input type="checkbox" name="services[weekend][enabled]" checked class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                </div>
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-1">Dopłata</label>
                                        <div class="flex items-center">
                                            <input type="number" name="services[weekend][fee]" value="2.00" step="0.01" 
                                                   class="w-20 text-sm border-gray-300 rounded-l-md focus:ring-blue-500 focus:border-blue-500">
                                            <span class="px-3 py-2 bg-gray-50 border border-l-0 border-gray-300 rounded-r-md text-sm text-gray-500">zł</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Fuel Surcharge -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="text-sm font-medium text-gray-900">Opłata paliwowa</h4>
                                    <input type="checkbox" name="services[fuel][enabled]" checked class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                </div>
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-1">Stawka</label>
                                        <div class="flex items-center">
                                            <input type="number" name="services[fuel][rate]" value="0.5" step="0.1" 
                                                   class="w-20 text-sm border-gray-300 rounded-l-md focus:ring-blue-500 focus:border-blue-500">
                                            <span class="px-3 py-2 bg-gray-50 border border-l-0 border-gray-300 rounded-r-md text-sm text-gray-500">%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- SMS Notification -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="text-sm font-medium text-gray-900">Powiadomienie SMS</h4>
                                    <input type="checkbox" name="services[sms][enabled]" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                </div>
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-1">Opłata za SMS</label>
                                        <div class="flex items-center">
                                            <input type="number" name="services[sms][fee]" value="0.50" step="0.01" 
                                                   class="w-20 text-sm border-gray-300 rounded-l-md focus:ring-blue-500 focus:border-blue-500">
                                            <span class="px-3 py-2 bg-gray-50 border border-l-0 border-gray-300 rounded-r-md text-sm text-gray-500">zł</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Return Service -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="text-sm font-medium text-gray-900">Zwrot</h4>
                                    <input type="checkbox" name="services[return][enabled]" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                </div>
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-1">Opłata za zwrot</label>
                                        <div class="flex items-center">
                                            <input type="number" name="services[return][fee]" value="5.00" step="0.01" 
                                                   class="w-20 text-sm border-gray-300 rounded-l-md focus:ring-blue-500 focus:border-blue-500">
                                            <span class="px-3 py-2 bg-gray-50 border border-l-0 border-gray-300 rounded-r-md text-sm text-gray-500">zł</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Price Validation -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Walidacja cenowa</h3>
                        <p class="mt-1 text-sm text-gray-500">Automatyczne sprawdzenia i ograniczenia</p>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 mb-3">Ograniczenia cenowe</h4>
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-1">Minimalna cena</label>
                                        <div class="flex items-center">
                                            <input type="number" name="validation[min_price]" value="5.00" step="0.01" 
                                                   class="w-24 text-sm border-gray-300 rounded-l-md focus:ring-blue-500 focus:border-blue-500">
                                            <span class="px-3 py-2 bg-gray-50 border border-l-0 border-gray-300 rounded-r-md text-sm text-gray-500">zł</span>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-1">Maksymalna cena</label>
                                        <div class="flex items-center">
                                            <input type="number" name="validation[max_price]" value="50.00" step="0.01" 
                                                   class="w-24 text-sm border-gray-300 rounded-l-md focus:ring-blue-500 focus:border-blue-500">
                                            <span class="px-3 py-2 bg-gray-50 border border-l-0 border-gray-300 rounded-r-md text-sm text-gray-500">zł</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 mb-3">Zaokrąglanie</h4>
                                <div class="space-y-3">
                                    <div class="flex items-center">
                                        <input type="checkbox" name="rounding[enabled]" id="rounding_enabled" checked class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                        <label for="rounding_enabled" class="ml-2 text-sm text-gray-900">Włącz zaokrąglanie</label>
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-1">Do najbliższych</label>
                                        <select name="rounding[precision]" class="w-32 text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                            <option value="0.01" selected>1 grosz</option>
                                            <option value="0.05">5 groszy</option>
                                            <option value="0.10">10 groszy</option>
                                            <option value="0.50">50 groszy</option>
                                            <option value="1.00">1 złoty</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
// Auto-calculate percentage differences
function calculateDifferences() {
    const rows = ['A', 'B', 'C'];
    rows.forEach(row => {
        const basePrice = parseFloat(document.querySelector(`input[name="prices[${row}][base]"]`).value) || 0;
        const clientPrice = parseFloat(document.querySelector(`input[name="prices[${row}][client]"]`).value) || 0;
        
        if (basePrice > 0) {
            const difference = ((clientPrice - basePrice) / basePrice * 100).toFixed(1);
            const cell = document.querySelector(`tr:has(input[name="prices[${row}][base]"]) td:last-child span`);
            if (cell) {
                cell.textContent = difference + '%';
                cell.className = difference < 0 ? 'text-sm text-green-600 font-medium' : 'text-sm text-red-600 font-medium';
            }
        }
    });
}

// Listen for price changes
document.querySelectorAll('input[name*="prices"]').forEach(input => {
    input.addEventListener('input', calculateDifferences);
});

// Quick actions
document.querySelector('button:contains("Kopiuj ceny z BASE do HEAVY USER")').addEventListener('click', function() {
    const rows = ['A', 'B', 'C'];
    rows.forEach(row => {
        const basePrice = document.querySelector(`input[name="prices[${row}][base]"]`).value;
        document.querySelector(`input[name="prices[${row}][heavy_user]"]`).value = basePrice;
    });
    calculateDifferences();
});
</script>
@endsection