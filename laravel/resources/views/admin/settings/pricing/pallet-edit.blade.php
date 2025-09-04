@extends('layouts.admin')

@section('title', 'Geodis - Konfiguracja cenowa')

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
                            <span class="ml-1 text-gray-700 md:ml-2">Geodis Freight</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate flex items-center">
                <div class="h-10 w-10 bg-red-600 rounded-lg flex items-center justify-center mr-3">
                    <span class="text-white font-bold text-sm">GEO</span>
                </div>
                Geodis Freight - Macierz paletowa
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Konfiguracja cen paletowych według stref geograficznych i parametrów przesyłek
            </p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3">
            <button type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-1.447-.894L15 4m0 13V4m-6 3l6-3" />
                </svg>
                Zarządzaj strefami
            </button>
            <button type="submit" form="pallet-pricing-form" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Zapisz macierz
            </button>
        </div>
    </div>

    <form id="pallet-pricing-form" method="POST" action="{{ route('admin.settings.pricing.pallet.update', 'geodis') }}">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-5">
            <!-- Configuration Sidebar -->
            <div class="lg:col-span-1">
                <div class="space-y-6">
                    <!-- Calculator -->
                    <div class="bg-white shadow rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Kalkulator wymiarowy</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Długość (cm)</label>
                                <input type="number" id="calc_length" class="mt-1 block w-full border-gray-300 rounded-md text-sm" placeholder="120">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Szerokość (cm)</label>
                                <input type="number" id="calc_width" class="mt-1 block w-full border-gray-300 rounded-md text-sm" placeholder="80">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Wysokość (cm)</label>
                                <input type="number" id="calc_height" class="mt-1 block w-full border-gray-300 rounded-md text-sm" placeholder="100">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Waga (kg)</label>
                                <input type="number" id="calc_weight" class="mt-1 block w-full border-gray-300 rounded-md text-sm" placeholder="25">
                            </div>
                            
                            <hr class="my-4">
                            
                            <div class="bg-gray-50 p-3 rounded-lg">
                                <div class="text-sm space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Objętość:</span>
                                        <span class="font-medium" id="calc_volume">0.96 m³</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Waga obj.:</span>
                                        <span class="font-medium" id="calc_dim_weight">192 kg</span>
                                    </div>
                                    <div class="flex justify-between font-medium border-t pt-2">
                                        <span class="text-gray-900">Waga taryfowa:</span>
                                        <span class="text-red-600" id="calc_chargeable">192 kg</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Zone Finder -->
                    <div class="bg-white shadow rounded-lg p-6">
                        <h4 class="text-sm font-medium text-gray-900 mb-3">Wyszukiwarka stref</h4>
                        <div class="space-y-3">
                            <input type="text" placeholder="Kod pocztowy lub miasto" class="block w-full border-gray-300 rounded-md text-sm" id="zone-search">
                            <div class="text-xs text-gray-600">
                                <div>Warszawa: Strefa 1</div>
                                <div>Berlin: Strefa 2A</div>
                                <div>Paryż: Strefa 2B</div>
                                <div>Londyn: Strefa 3</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="lg:col-span-4">
                <!-- Service Type Tabs -->
                <div class="bg-white shadow rounded-lg mb-6">
                    <div class="border-b border-gray-200">
                        <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                            <button type="button" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm tab-button active" data-tab="domestic">
                                Krajowy
                            </button>
                            <button type="button" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm tab-button" data-tab="export">
                                Eksport UE
                            </button>
                            <button type="button" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm tab-button" data-tab="international">
                                Międzynarodowy
                            </button>
                        </nav>
                    </div>
                </div>

                <!-- Domestic Tab -->
                <div id="tab-domestic" class="tab-content">
                    <div class="bg-white shadow rounded-lg mb-6">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Transport krajowy - Strefy</h3>
                            <p class="mt-1 text-sm text-gray-500">Ceny według stref geograficznych i wagi taryfowej</p>
                        </div>
                        <div class="p-6">
                            <div class="overflow-x-auto">
                                <table class="min-w-full">
                                    <thead>
                                        <tr class="border-b border-gray-200">
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waga</th>
                                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Strefa 1<br><span class="text-xs normal-case">(Warszawa)</span></th>
                                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Strefa 2<br><span class="text-xs normal-case">(Główne miasta)</span></th>
                                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Strefa 3<br><span class="text-xs normal-case">(Pozostałe)</span></th>
                                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Strefa 4<br><span class="text-xs normal-case">(Trudna)</span></th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach([
                                            ['weight' => 'do 50 kg', 'z1' => 45.00, 'z2' => 55.00, 'z3' => 65.00, 'z4' => 85.00],
                                            ['weight' => '51-100 kg', 'z1' => 65.00, 'z2' => 75.00, 'z3' => 95.00, 'z4' => 125.00],
                                            ['weight' => '101-200 kg', 'z1' => 85.00, 'z2' => 105.00, 'z3' => 135.00, 'z4' => 175.00],
                                            ['weight' => '201-300 kg', 'z1' => 120.00, 'z2' => 145.00, 'z3' => 185.00, 'z4' => 235.00],
                                            ['weight' => '301-500 kg', 'z1' => 165.00, 'z2' => 195.00, 'z3' => 245.00, 'z4' => 315.00],
                                            ['weight' => '501-750 kg', 'z1' => 225.00, 'z2' => 265.00, 'z3' => 325.00, 'z4' => 415.00],
                                            ['weight' => '751-1000 kg', 'z1' => 285.00, 'z2' => 335.00, 'z3' => 405.00, 'z4' => 515.00],
                                        ] as $index => $row)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-4 text-sm font-medium text-gray-900">{{ $row['weight'] }}</td>
                                            <td class="px-4 py-4 text-center">
                                                <input type="number" name="domestic[{{ $index }}][z1]" value="{{ $row['z1'] }}" step="0.01" 
                                                       class="w-20 text-center border-gray-300 rounded text-sm">
                                            </td>
                                            <td class="px-4 py-4 text-center">
                                                <input type="number" name="domestic[{{ $index }}][z2]" value="{{ $row['z2'] }}" step="0.01" 
                                                       class="w-20 text-center border-gray-300 rounded text-sm">
                                            </td>
                                            <td class="px-4 py-4 text-center">
                                                <input type="number" name="domestic[{{ $index }}][z3]" value="{{ $row['z3'] }}" step="0.01" 
                                                       class="w-20 text-center border-gray-300 rounded text-sm">
                                            </td>
                                            <td class="px-4 py-4 text-center">
                                                <input type="number" name="domestic[{{ $index }}][z4]" value="{{ $row['z4'] }}" step="0.01" 
                                                       class="w-20 text-center border-gray-300 rounded text-sm">
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Export Tab -->
                <div id="tab-export" class="tab-content hidden">
                    <div class="bg-white shadow rounded-lg mb-6">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Eksport UE - Strefy kodowe</h3>
                            <p class="mt-1 text-sm text-gray-500">Ceny według krajów i kodów pocztowych UE</p>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                                <!-- Zone 2A (Germany, Czech, Slovakia) -->
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <h4 class="text-sm font-medium text-gray-900 mb-3 flex items-center">
                                        <div class="h-6 w-6 bg-blue-500 rounded flex items-center justify-center mr-2">
                                            <span class="text-white text-xs font-bold">2A</span>
                                        </div>
                                        Niemcy, Czechy, Słowacja
                                    </h4>
                                    
                                    <div class="space-y-3">
                                        <div class="grid grid-cols-3 gap-2 text-xs">
                                            <div class="font-medium text-gray-500">Waga</div>
                                            <div class="font-medium text-gray-500">Standard</div>
                                            <div class="font-medium text-gray-500">Express</div>
                                        </div>
                                        
                                        @foreach([
                                            ['weight' => 'do 100kg', 'std' => 125.00, 'exp' => 175.00],
                                            ['weight' => '101-300kg', 'std' => 185.00, 'exp' => 235.00],
                                            ['weight' => '301-500kg', 'std' => 245.00, 'exp' => 315.00],
                                            ['weight' => '501-1000kg', 'std' => 385.00, 'exp' => 485.00]
                                        ] as $i => $row)
                                        <div class="grid grid-cols-3 gap-2">
                                            <div class="text-sm text-gray-900">{{ $row['weight'] }}</div>
                                            <input type="number" name="export[2a][{{ $i }}][std]" value="{{ $row['std'] }}" step="0.01" 
                                                   class="w-full text-center border-gray-300 rounded text-sm">
                                            <input type="number" name="export[2a][{{ $i }}][exp]" value="{{ $row['exp'] }}" step="0.01" 
                                                   class="w-full text-center border-gray-300 rounded text-sm">
                                        </div>
                                        @endforeach
                                    </div>
                                    
                                    <div class="mt-4 pt-3 border-t border-gray-200">
                                        <h5 class="text-xs font-medium text-gray-700 mb-2">Kody pocztowe specjalne:</h5>
                                        <div class="flex flex-wrap gap-1">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">DE: 18xxx +15%</span>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">CZ: 79xxx +10%</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Zone 2B (France, Benelux) -->
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <h4 class="text-sm font-medium text-gray-900 mb-3 flex items-center">
                                        <div class="h-6 w-6 bg-green-500 rounded flex items-center justify-center mr-2">
                                            <span class="text-white text-xs font-bold">2B</span>
                                        </div>
                                        Francja, Benelux, Austria
                                    </h4>
                                    
                                    <div class="space-y-3">
                                        <div class="grid grid-cols-3 gap-2 text-xs">
                                            <div class="font-medium text-gray-500">Waga</div>
                                            <div class="font-medium text-gray-500">Standard</div>
                                            <div class="font-medium text-gray-500">Express</div>
                                        </div>
                                        
                                        @foreach([
                                            ['weight' => 'do 100kg', 'std' => 145.00, 'exp' => 195.00],
                                            ['weight' => '101-300kg', 'std' => 205.00, 'exp' => 265.00],
                                            ['weight' => '301-500kg', 'std' => 275.00, 'exp' => 345.00],
                                            ['weight' => '501-1000kg', 'std' => 425.00, 'exp' => 525.00]
                                        ] as $i => $row)
                                        <div class="grid grid-cols-3 gap-2">
                                            <div class="text-sm text-gray-900">{{ $row['weight'] }}</div>
                                            <input type="number" name="export[2b][{{ $i }}][std]" value="{{ $row['std'] }}" step="0.01" 
                                                   class="w-full text-center border-gray-300 rounded text-sm">
                                            <input type="number" name="export[2b][{{ $i }}][exp]" value="{{ $row['exp'] }}" step="0.01" 
                                                   class="w-full text-center border-gray-300 rounded text-sm">
                                        </div>
                                        @endforeach
                                    </div>
                                    
                                    <div class="mt-4 pt-3 border-t border-gray-200">
                                        <h5 class="text-xs font-medium text-gray-700 mb-2">Kody pocztowe specjalne:</h5>
                                        <div class="flex flex-wrap gap-1">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">FR: 2xxxx +20%</span>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">AT: 9xxx +12%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- International Tab -->
                <div id="tab-international" class="tab-content hidden">
                    <div class="bg-white shadow rounded-lg mb-6">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Transport międzynarodowy</h3>
                            <p class="mt-1 text-sm text-gray-500">UK, Szwajcaria, kraje pozaunijne</p>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                                @foreach([
                                    ['zone' => 'UK', 'color' => 'purple', 'countries' => 'Wielka Brytania', 'rates' => ['100' => 285, '300' => 385, '500' => 485, '1000' => 685]],
                                    ['zone' => 'CH', 'color' => 'red', 'countries' => 'Szwajcaria', 'rates' => ['100' => 225, '300' => 325, '500' => 425, '1000' => 585]],
                                    ['zone' => 'RoW', 'color' => 'gray', 'countries' => 'Reszta świata', 'rates' => ['100' => 485, '300' => 685, '500' => 885, '1000' => 1285]]
                                ] as $zone)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <h4 class="text-sm font-medium text-gray-900 mb-3 flex items-center">
                                        <div class="h-6 w-6 bg-{{ $zone['color'] }}-500 rounded flex items-center justify-center mr-2">
                                            <span class="text-white text-xs font-bold">{{ $zone['zone'] }}</span>
                                        </div>
                                        {{ $zone['countries'] }}
                                    </h4>
                                    
                                    <div class="space-y-3">
                                        @foreach($zone['rates'] as $weight => $rate)
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm text-gray-600">do {{ $weight }}kg</span>
                                            <div class="flex items-center">
                                                <input type="number" name="international[{{ strtolower($zone['zone']) }}][{{ $weight }}]" 
                                                       value="{{ $rate }}" step="0.01" 
                                                       class="w-20 text-center border-gray-300 rounded text-sm mr-2">
                                                <span class="text-xs text-gray-500">PLN</span>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Surcharges Panel -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Dopłaty i opłaty dodatkowe</h3>
                        <p class="mt-1 text-sm text-gray-500">Paliwo, handling, celne, niestandardowe</p>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                            <!-- Fuel Surcharge -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="text-sm font-medium text-gray-900">Opłata paliwowa</h4>
                                    <input type="checkbox" name="surcharges[fuel][enabled]" checked class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                                </div>
                                <div class="space-y-2">
                                    <div class="flex items-center">
                                        <input type="number" name="surcharges[fuel][rate]" value="18.5" step="0.1" 
                                               class="w-16 text-sm border-gray-300 rounded-l-md">
                                        <span class="px-2 py-1 bg-gray-50 border border-l-0 border-gray-300 rounded-r-md text-sm text-gray-500">%</span>
                                    </div>
                                    <p class="text-xs text-gray-500">Aktualizowana co tydzień</p>
                                </div>
                            </div>

                            <!-- Handling Fee -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="text-sm font-medium text-gray-900">Opłata handling</h4>
                                    <input type="checkbox" name="surcharges[handling][enabled]" checked class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                                </div>
                                <div class="space-y-2">
                                    <div class="flex items-center">
                                        <input type="number" name="surcharges[handling][fee]" value="25.00" step="0.01" 
                                               class="w-16 text-sm border-gray-300 rounded-l-md">
                                        <span class="px-2 py-1 bg-gray-50 border border-l-0 border-gray-300 rounded-r-md text-sm text-gray-500">zł</span>
                                    </div>
                                    <p class="text-xs text-gray-500">Za przesyłkę</p>
                                </div>
                            </div>

                            <!-- Customs -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="text-sm font-medium text-gray-900">Obsługa celna</h4>
                                    <input type="checkbox" name="surcharges[customs][enabled]" class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                                </div>
                                <div class="space-y-2">
                                    <div class="flex items-center">
                                        <input type="number" name="surcharges[customs][fee]" value="75.00" step="0.01" 
                                               class="w-16 text-sm border-gray-300 rounded-l-md">
                                        <span class="px-2 py-1 bg-gray-50 border border-l-0 border-gray-300 rounded-r-md text-sm text-gray-500">zł</span>
                                    </div>
                                    <p class="text-xs text-gray-500">Kraje pozaunijne</p>
                                </div>
                            </div>

                            <!-- Oversize -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="text-sm font-medium text-gray-900">Ponadgabaryt</h4>
                                    <input type="checkbox" name="surcharges[oversize][enabled]" checked class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                                </div>
                                <div class="space-y-2">
                                    <div class="text-xs text-gray-600">
                                        <div>L>240cm: +50%</div>
                                        <div>L>300cm: +100%</div>
                                        <div>H>200cm: +25%</div>
                                    </div>
                                </div>
                            </div>

                            <!-- ADR -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="text-sm font-medium text-gray-900">Przesyłki ADR</h4>
                                    <input type="checkbox" name="surcharges[adr][enabled]" class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                                </div>
                                <div class="space-y-2">
                                    <div class="flex items-center">
                                        <input type="number" name="surcharges[adr][fee]" value="150.00" step="0.01" 
                                               class="w-16 text-sm border-gray-300 rounded-l-md">
                                        <span class="px-2 py-1 bg-gray-50 border border-l-0 border-gray-300 rounded-r-md text-sm text-gray-500">zł</span>
                                    </div>
                                    <p class="text-xs text-gray-500">Materiały niebezpieczne</p>
                                </div>
                            </div>

                            <!-- Pickup -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="text-sm font-medium text-gray-900">Odbiór</h4>
                                    <input type="checkbox" name="surcharges[pickup][enabled]" checked class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                                </div>
                                <div class="space-y-2">
                                    <div class="flex items-center">
                                        <input type="number" name="surcharges[pickup][fee]" value="45.00" step="0.01" 
                                               class="w-16 text-sm border-gray-300 rounded-l-md">
                                        <span class="px-2 py-1 bg-gray-50 border border-l-0 border-gray-300 rounded-r-md text-sm text-gray-500">zł</span>
                                    </div>
                                    <p class="text-xs text-gray-500">Za odbiór</p>
                                </div>
                            </div>

                            <!-- Delivery -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="text-sm font-medium text-gray-900">Dostawa</h4>
                                    <input type="checkbox" name="surcharges[delivery][enabled]" checked class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                                </div>
                                <div class="space-y-2">
                                    <div class="flex items-center">
                                        <input type="number" name="surcharges[delivery][fee]" value="45.00" step="0.01" 
                                               class="w-16 text-sm border-gray-300 rounded-l-md">
                                        <span class="px-2 py-1 bg-gray-50 border border-l-0 border-gray-300 rounded-r-md text-sm text-gray-500">zł</span>
                                    </div>
                                    <p class="text-xs text-gray-500">Za dostawę</p>
                                </div>
                            </div>

                            <!-- Insurance -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="text-sm font-medium text-gray-900">Ubezpieczenie</h4>
                                    <input type="checkbox" name="surcharges[insurance][enabled]" checked class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                                </div>
                                <div class="space-y-2">
                                    <div class="flex items-center">
                                        <input type="number" name="surcharges[insurance][rate]" value="0.8" step="0.1" 
                                               class="w-16 text-sm border-gray-300 rounded-l-md">
                                        <span class="px-2 py-1 bg-gray-50 border border-l-0 border-gray-300 rounded-r-md text-sm text-gray-500">%</span>
                                    </div>
                                    <p class="text-xs text-gray-500">Min. 15 zł</p>
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
// Tabs functionality
document.querySelectorAll('.tab-button').forEach(button => {
    button.addEventListener('click', function() {
        const tab = this.getAttribute('data-tab');
        
        // Remove active class from all buttons and contents
        document.querySelectorAll('.tab-button').forEach(btn => {
            btn.classList.remove('active', 'border-red-500', 'text-red-600');
            btn.classList.add('border-transparent', 'text-gray-500');
        });
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });
        
        // Add active class to clicked button and show content
        this.classList.remove('border-transparent', 'text-gray-500');
        this.classList.add('active', 'border-red-500', 'text-red-600');
        document.getElementById('tab-' + tab).classList.remove('hidden');
    });
});

// Dimensional weight calculator
function calculateDimensionalWeight() {
    const length = parseFloat(document.getElementById('calc_length').value) || 0;
    const width = parseFloat(document.getElementById('calc_width').value) || 0;
    const height = parseFloat(document.getElementById('calc_height').value) || 0;
    const weight = parseFloat(document.getElementById('calc_weight').value) || 0;
    
    const volume = (length * width * height) / 1000000; // m³
    const dimWeight = volume * 200; // Geodis uses 200 kg/m³ factor
    const chargeableWeight = Math.max(weight, dimWeight);
    
    document.getElementById('calc_volume').textContent = volume.toFixed(2) + ' m³';
    document.getElementById('calc_dim_weight').textContent = dimWeight.toFixed(0) + ' kg';
    document.getElementById('calc_chargeable').textContent = chargeableWeight.toFixed(0) + ' kg';
}

// Listen for calculator changes
document.querySelectorAll('#calc_length, #calc_width, #calc_height, #calc_weight').forEach(input => {
    input.addEventListener('input', calculateDimensionalWeight);
});

// Initialize calculator
calculateDimensionalWeight();
</script>
@endsection