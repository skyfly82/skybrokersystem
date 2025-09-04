@extends('layouts.admin')

@section('title', 'Konfiguracja macierzy DHL Express')

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
                            <span class="ml-1 text-gray-700 md:ml-2">DHL Express</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate flex items-center">
                <div class="h-10 w-10 bg-red-600 rounded-lg flex items-center justify-center mr-3">
                    <span class="text-white font-bold text-sm">DHL</span>
                </div>
                DHL Express - Macierz cenowa
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Konfiguracja cen dla stref geograficznych UE-1, UE-2, Świat-1 oraz różnych przedziałów wagowych
            </p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3">
            <button type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Zaplanuj zmianę
            </button>
            <button type="submit" form="dhl-pricing-form" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Zapisz macierz
            </button>
        </div>
    </div>

    <form id="dhl-pricing-form" method="POST" action="{{ route('admin.settings.pricing.update', 'dhl-express') }}">
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
                                <label class="block text-sm font-medium text-gray-700">Waluta</label>
                                <select name="currency" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">
                                    <option value="PLN" selected>PLN</option>
                                    <option value="EUR">EUR</option>
                                    <option value="USD">USD</option>
                                </select>
                            </div>
                            
                            <div class="flex items-center">
                                <input type="checkbox" name="vat_included" id="vat_included" checked class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="vat_included" class="ml-2 text-sm text-gray-900">Ceny zawierają VAT</label>
                            </div>
                        </div>
                    </div>

                    <!-- Zone Configuration -->
                    <div class="bg-white shadow rounded-lg p-6">
                        <h4 class="text-sm font-medium text-gray-900 mb-3">Strefy geograficzne</h4>
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <div class="h-3 w-3 bg-green-500 rounded-full mr-2"></div>
                                <span class="text-sm text-gray-700">UE-1: Niemcy, Francja, Czechy</span>
                            </div>
                            <div class="flex items-center">
                                <div class="h-3 w-3 bg-blue-500 rounded-full mr-2"></div>
                                <span class="text-sm text-gray-700">UE-2: Reszta UE</span>
                            </div>
                            <div class="flex items-center">
                                <div class="h-3 w-3 bg-purple-500 rounded-full mr-2"></div>
                                <span class="text-sm text-gray-700">Świat-1: USA, UK, Azja</span>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-white shadow rounded-lg p-6">
                        <h4 class="text-sm font-medium text-gray-900 mb-3">Szybkie akcje</h4>
                        <div class="space-y-2">
                            <button type="button" class="w-full text-left px-3 py-2 text-sm rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100">
                                Zastosuj inflację +3%
                            </button>
                            <button type="button" class="w-full text-left px-3 py-2 text-sm rounded-lg hover:bg-gray-50">
                                Kopiuj ceny UE-1 do UE-2 +15%
                            </button>
                            <button type="button" class="w-full text-left px-3 py-2 text-sm rounded-lg hover:bg-gray-50">
                                Reset do cen fabrycznych DHL
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
                        <h3 class="text-lg font-medium text-gray-900">Macierz cenowa DHL Express</h3>
                        <p class="mt-1 text-sm text-gray-500">Ceny według przedziałów wagowych i stref geograficznych</p>
                    </div>
                    <div class="p-6">
                        <!-- Pricing Matrix Table -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead>
                                    <tr class="border-b border-gray-200">
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                                            Waga
                                        </th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            UE-1<br><span class="text-xs normal-case text-gray-400">(DE, FR, CZ)</span>
                                        </th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            UE-2<br><span class="text-xs normal-case text-gray-400">(Reszta UE)</span>
                                        </th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Świat-1<br><span class="text-xs normal-case text-gray-400">(USA, UK, Azja)</span>
                                        </th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Różnica %
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <!-- 0.5kg -->
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-4">
                                            <div class="flex items-center">
                                                <div class="h-8 w-8 bg-green-100 rounded flex items-center justify-center mr-2">
                                                    <span class="text-green-700 font-bold text-xs">0.5</span>
                                                </div>
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900">0.5 kg</div>
                                                    <div class="text-xs text-gray-500">dokumenty</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <div class="relative">
                                                <input type="number" name="prices[0.5][zone1]" value="45.00" step="0.01" 
                                                       class="w-20 text-center border-gray-300 rounded text-sm focus:ring-blue-500 focus:border-blue-500">
                                                <span class="text-xs text-gray-500 ml-1">zł</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <div class="relative">
                                                <input type="number" name="prices[0.5][zone2]" value="52.00" step="0.01" 
                                                       class="w-20 text-center border-gray-300 rounded text-sm focus:ring-blue-500 focus:border-blue-500">
                                                <span class="text-xs text-gray-500 ml-1">zł</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <div class="relative">
                                                <input type="number" name="prices[0.5][world1]" value="85.00" step="0.01" 
                                                       class="w-20 text-center border-gray-300 rounded text-sm focus:ring-blue-500 focus:border-blue-500">
                                                <span class="text-xs text-gray-500 ml-1">zł</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <span class="text-sm text-red-600 font-medium">+88.9%</span>
                                        </td>
                                    </tr>
                                    
                                    <!-- 1.0kg -->
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-4">
                                            <div class="flex items-center">
                                                <div class="h-8 w-8 bg-blue-100 rounded flex items-center justify-center mr-2">
                                                    <span class="text-blue-700 font-bold text-xs">1.0</span>
                                                </div>
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900">1.0 kg</div>
                                                    <div class="text-xs text-gray-500">standard</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <div class="relative">
                                                <input type="number" name="prices[1.0][zone1]" value="58.00" step="0.01" 
                                                       class="w-20 text-center border-gray-300 rounded text-sm focus:ring-blue-500 focus:border-blue-500">
                                                <span class="text-xs text-gray-500 ml-1">zł</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <div class="relative">
                                                <input type="number" name="prices[1.0][zone2]" value="68.00" step="0.01" 
                                                       class="w-20 text-center border-gray-300 rounded text-sm focus:ring-blue-500 focus:border-blue-500">
                                                <span class="text-xs text-gray-500 ml-1">zł</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <div class="relative">
                                                <input type="number" name="prices[1.0][world1]" value="125.00" step="0.01" 
                                                       class="w-20 text-center border-gray-300 rounded text-sm focus:ring-blue-500 focus:border-blue-500">
                                                <span class="text-xs text-gray-500 ml-1">zł</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <span class="text-sm text-red-600 font-medium">+115.5%</span>
                                        </td>
                                    </tr>
                                    
                                    <!-- 2.0kg -->
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-4">
                                            <div class="flex items-center">
                                                <div class="h-8 w-8 bg-yellow-100 rounded flex items-center justify-center mr-2">
                                                    <span class="text-yellow-700 font-bold text-xs">2.0</span>
                                                </div>
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900">2.0 kg</div>
                                                    <div class="text-xs text-gray-500">standard</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <div class="relative">
                                                <input type="number" name="prices[2.0][zone1]" value="75.00" step="0.01" 
                                                       class="w-20 text-center border-gray-300 rounded text-sm focus:ring-blue-500 focus:border-blue-500">
                                                <span class="text-xs text-gray-500 ml-1">zł</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <div class="relative">
                                                <input type="number" name="prices[2.0][zone2]" value="89.00" step="0.01" 
                                                       class="w-20 text-center border-gray-300 rounded text-sm focus:ring-blue-500 focus:border-blue-500">
                                                <span class="text-xs text-gray-500 ml-1">zł</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <div class="relative">
                                                <input type="number" name="prices[2.0][world1]" value="185.00" step="0.01" 
                                                       class="w-20 text-center border-gray-300 rounded text-sm focus:ring-blue-500 focus:border-blue-500">
                                                <span class="text-xs text-gray-500 ml-1">zł</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <span class="text-sm text-red-600 font-medium">+146.7%</span>
                                        </td>
                                    </tr>
                                    
                                    <!-- 5.0kg -->
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-4">
                                            <div class="flex items-center">
                                                <div class="h-8 w-8 bg-orange-100 rounded flex items-center justify-center mr-2">
                                                    <span class="text-orange-700 font-bold text-xs">5.0</span>
                                                </div>
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900">5.0 kg</div>
                                                    <div class="text-xs text-gray-500">ciężka</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <div class="relative">
                                                <input type="number" name="prices[5.0][zone1]" value="125.00" step="0.01" 
                                                       class="w-20 text-center border-gray-300 rounded text-sm focus:ring-blue-500 focus:border-blue-500">
                                                <span class="text-xs text-gray-500 ml-1">zł</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <div class="relative">
                                                <input type="number" name="prices[5.0][zone2]" value="145.00" step="0.01" 
                                                       class="w-20 text-center border-gray-300 rounded text-sm focus:ring-blue-500 focus:border-blue-500">
                                                <span class="text-xs text-gray-500 ml-1">zł</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <div class="relative">
                                                <input type="number" name="prices[5.0][world1]" value="285.00" step="0.01" 
                                                       class="w-20 text-center border-gray-300 rounded text-sm focus:ring-blue-500 focus:border-blue-500">
                                                <span class="text-xs text-gray-500 ml-1">zł</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <span class="text-sm text-red-600 font-medium">+128.0%</span>
                                        </td>
                                    </tr>
                                    
                                    <!-- 10.0kg -->
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-4">
                                            <div class="flex items-center">
                                                <div class="h-8 w-8 bg-red-100 rounded flex items-center justify-center mr-2">
                                                    <span class="text-red-700 font-bold text-xs">10</span>
                                                </div>
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900">10.0 kg</div>
                                                    <div class="text-xs text-gray-500">max standard</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <div class="relative">
                                                <input type="number" name="prices[10.0][zone1]" value="185.00" step="0.01" 
                                                       class="w-20 text-center border-gray-300 rounded text-sm focus:ring-blue-500 focus:border-blue-500">
                                                <span class="text-xs text-gray-500 ml-1">zł</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <div class="relative">
                                                <input type="number" name="prices[10.0][zone2]" value="225.00" step="0.01" 
                                                       class="w-20 text-center border-gray-300 rounded text-sm focus:ring-blue-500 focus:border-blue-500">
                                                <span class="text-xs text-gray-500 ml-1">zł</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <div class="relative">
                                                <input type="number" name="prices[10.0][world1]" value="485.00" step="0.01" 
                                                       class="w-20 text-center border-gray-300 rounded text-sm focus:ring-blue-500 focus:border-blue-500">
                                                <span class="text-xs text-gray-500 ml-1">zł</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <span class="text-sm text-red-600 font-medium">+162.2%</span>
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
                        <h3 class="text-lg font-medium text-gray-900">Usługi dodatkowe DHL</h3>
                        <p class="mt-1 text-sm text-gray-500">Dopłaty za dodatkowe usługi ekspresowe</p>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
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
                                            <input type="number" name="services[fuel][rate]" value="15.2" step="0.1" 
                                                   class="w-20 text-sm border-gray-300 rounded-l-md focus:ring-blue-500 focus:border-blue-500">
                                            <span class="px-3 py-2 bg-gray-50 border border-l-0 border-gray-300 rounded-r-md text-sm text-gray-500">%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Security Surcharge -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="text-sm font-medium text-gray-900">Opłata bezpieczeństwa</h4>
                                    <input type="checkbox" name="services[security][enabled]" checked class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                </div>
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-1">Stawka</label>
                                        <div class="flex items-center">
                                            <input type="number" name="services[security][rate]" value="0.75" step="0.01" 
                                                   class="w-20 text-sm border-gray-300 rounded-l-md focus:ring-blue-500 focus:border-blue-500">
                                            <span class="px-3 py-2 bg-gray-50 border border-l-0 border-gray-300 rounded-r-md text-sm text-gray-500">%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Remote Area -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="text-sm font-medium text-gray-900">Obszar odległy</h4>
                                    <input type="checkbox" name="services[remote_area][enabled]" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                </div>
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-1">Dopłata</label>
                                        <div class="flex items-center">
                                            <input type="number" name="services[remote_area][fee]" value="35.00" step="0.01" 
                                                   class="w-20 text-sm border-gray-300 rounded-l-md focus:ring-blue-500 focus:border-blue-500">
                                            <span class="px-3 py-2 bg-gray-50 border border-l-0 border-gray-300 rounded-r-md text-sm text-gray-500">zł</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Document Discount -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="text-sm font-medium text-gray-900">Zniżka dokumenty</h4>
                                    <input type="checkbox" name="services[document_discount][enabled]" checked class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                </div>
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-1">Zniżka</label>
                                        <div class="flex items-center">
                                            <input type="number" name="services[document_discount][discount]" value="15" step="1" 
                                                   class="w-20 text-sm border-gray-300 rounded-l-md focus:ring-blue-500 focus:border-blue-500">
                                            <span class="px-3 py-2 bg-gray-50 border border-l-0 border-gray-300 rounded-r-md text-sm text-gray-500">%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Saturday Delivery -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="text-sm font-medium text-gray-900">Dostawa sobota</h4>
                                    <input type="checkbox" name="services[saturday][enabled]" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                </div>
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-1">Dopłata</label>
                                        <div class="flex items-center">
                                            <input type="number" name="services[saturday][fee]" value="45.00" step="0.01" 
                                                   class="w-20 text-sm border-gray-300 rounded-l-md focus:ring-blue-500 focus:border-blue-500">
                                            <span class="px-3 py-2 bg-gray-50 border border-l-0 border-gray-300 rounded-r-md text-sm text-gray-500">zł</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Express 9:00 -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="text-sm font-medium text-gray-900">Express 9:00</h4>
                                    <input type="checkbox" name="services[express_9][enabled]" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                </div>
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-1">Dopłata</label>
                                        <div class="flex items-center">
                                            <input type="number" name="services[express_9][multiplier]" value="2.5" step="0.1" 
                                                   class="w-20 text-sm border-gray-300 rounded-l-md focus:ring-blue-500 focus:border-blue-500">
                                            <span class="px-3 py-2 bg-gray-50 border border-l-0 border-gray-300 rounded-r-md text-sm text-gray-500">x cena</span>
                                        </div>
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
    const weights = ['0.5', '1.0', '2.0', '5.0', '10.0'];
    weights.forEach(weight => {
        const zone1Price = parseFloat(document.querySelector(`input[name="prices[${weight}][zone1]"]`).value) || 0;
        const world1Price = parseFloat(document.querySelector(`input[name="prices[${weight}][world1]"]`).value) || 0;
        
        if (zone1Price > 0) {
            const difference = ((world1Price - zone1Price) / zone1Price * 100).toFixed(1);
            const cell = document.querySelector(`tr:has(input[name="prices[${weight}][zone1]"]) td:last-child span`);
            if (cell) {
                cell.textContent = (difference >= 0 ? '+' : '') + difference + '%';
                cell.className = difference > 0 ? 'text-sm text-red-600 font-medium' : 'text-sm text-green-600 font-medium';
            }
        }
    });
}

// Listen for price changes
document.querySelectorAll('input[name*="prices"]').forEach(input => {
    input.addEventListener('input', calculateDifferences);
});

// Quick actions for DHL
document.querySelector('button:contains("Zastosuj inflację +3%")')?.addEventListener('click', function() {
    const inputs = document.querySelectorAll('input[name*="prices"]');
    inputs.forEach(input => {
        const currentValue = parseFloat(input.value) || 0;
        input.value = (currentValue * 1.03).toFixed(2);
    });
    calculateDifferences();
});
</script>
@endsection