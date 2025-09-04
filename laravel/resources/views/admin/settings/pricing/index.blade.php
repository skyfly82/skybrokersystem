@extends('layouts.admin')

@section('title', 'Macierze cennikowe')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="md:flex md:items-center md:justify-between mb-6">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                Macierze cennikowe
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Konfiguracja macierzy cenowych dla kurierów według standardów branżowych
            </p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3">
            <a href="{{ route('admin.settings.pricing.negotiated') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                Cenniki negocjowane
            </a>
            <button type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Import macierzy
            </button>
            <a href="{{ route('admin.settings.pricing.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Nowa macierz
            </a>
        </div>
    </div>

    <!-- Active Matrices -->
    <div class="mb-8">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Aktywne macierze cenowe</h3>
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <!-- InPost Paczkomat Matrix -->
            <div class="bg-white overflow-hidden shadow rounded-lg border-l-4 border-yellow-400">
                <div class="px-6 py-4">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="h-10 w-10 bg-yellow-500 rounded-lg flex items-center justify-center mr-3">
                                <span class="text-white font-bold text-sm">InP</span>
                            </div>
                            <div>
                                <h4 class="text-lg font-medium text-gray-900">InPost Paczkomat</h4>
                                <p class="text-sm text-gray-500">Macierz gabarytów A/B/C</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Aktywna
                        </span>
                    </div>
                    
                    <div class="grid grid-cols-4 gap-3 mb-4 text-xs">
                        <div class="text-center">
                            <div class="font-medium text-gray-500">Gabaryt</div>
                        </div>
                        <div class="text-center">
                            <div class="font-medium text-gray-500">BASE</div>
                        </div>
                        <div class="text-center">
                            <div class="font-medium text-gray-500">HEAVY USER</div>
                        </div>
                        <div class="text-center">
                            <div class="font-medium text-gray-500">KLIENCJA</div>
                        </div>
                        
                        <div class="text-center font-medium">A</div>
                        <div class="text-center">12.99 zł</div>
                        <div class="text-center">11.99 zł</div>
                        <div class="text-center">10.99 zł</div>
                        
                        <div class="text-center font-medium">B</div>
                        <div class="text-center">14.99 zł</div>
                        <div class="text-center">13.99 zł</div>
                        <div class="text-center">12.99 zł</div>
                        
                        <div class="text-center font-medium">C</div>
                        <div class="text-center">18.99 zł</div>
                        <div class="text-center">17.99 zł</div>
                        <div class="text-center">16.99 zł</div>
                    </div>
                    
                    <div class="border-t pt-3">
                        <div class="text-xs text-gray-500 mb-2">Dopłaty:</div>
                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <div>• COD: 3.99 zł</div>
                            <div>• Weekend: +2.00 zł</div>
                            <div>• Ubezpieczenie: 1%</div>
                            <div>• Paliwo: 0.5%</div>
                        </div>
                    </div>
                    
                    <div class="mt-4 flex justify-between items-center">
                        <div class="text-xs text-gray-500">
                            Ostatnia aktualizacja: 2024-08-28 14:30
                        </div>
                        <div class="flex space-x-2">
                            <a href="{{ route('admin.settings.pricing.edit', 'inpost-paczkomat') }}" class="text-sm text-blue-600 hover:text-blue-500 font-medium">
                                Konfiguruj →
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- InPost Kurier Matrix -->
            <div class="bg-white overflow-hidden shadow rounded-lg border-l-4 border-yellow-400">
                <div class="px-6 py-4">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="h-10 w-10 bg-yellow-500 rounded-lg flex items-center justify-center mr-3">
                                <span class="text-white font-bold text-sm">InK</span>
                            </div>
                            <div>
                                <h4 class="text-lg font-medium text-gray-900">InPost Kurier</h4>
                                <p class="text-sm text-gray-500">Macierz wagowo-gabarytowa</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Aktywna
                        </span>
                    </div>
                    
                    <div class="grid grid-cols-4 gap-3 mb-4 text-xs">
                        <div class="text-center">
                            <div class="font-medium text-gray-500">Waga/Typ</div>
                        </div>
                        <div class="text-center">
                            <div class="font-medium text-gray-500">BASE</div>
                        </div>
                        <div class="text-center">
                            <div class="font-medium text-gray-500">HEAVY USER</div>
                        </div>
                        <div class="text-center">
                            <div class="font-medium text-gray-500">KLIENCJA</div>
                        </div>
                        
                        <div class="text-center font-medium">do 1kg</div>
                        <div class="text-center">18.99 zł</div>
                        <div class="text-center">17.99 zł</div>
                        <div class="text-center">16.99 zł</div>
                        
                        <div class="text-center font-medium">do 5kg</div>
                        <div class="text-center">22.99 zł</div>
                        <div class="text-center">21.99 zł</div>
                        <div class="text-center">20.99 zł</div>
                        
                        <div class="text-center font-medium">do 10kg</div>
                        <div class="text-center">26.99 zł</div>
                        <div class="text-center">25.99 zł</div>
                        <div class="text-center">24.99 zł</div>
                    </div>
                    
                    <div class="border-t pt-3">
                        <div class="text-xs text-gray-500 mb-2">Dopłaty:</div>
                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <div>• COD: 4.99 zł</div>
                            <div>• Weekend: +3.00 zł</div>
                            <div>• Ubezpieczenie: 1%</div>
                            <div>• Paliwo: 0.8%</div>
                        </div>
                    </div>
                    
                    <div class="mt-4 flex justify-between items-center">
                        <div class="text-xs text-gray-500">
                            Ostatnia aktualizacja: 2024-08-28 14:30
                        </div>
                        <div class="flex space-x-2">
                            <a href="{{ route('admin.settings.pricing.edit', 'inpost-kurier') }}" class="text-sm text-blue-600 hover:text-blue-500 font-medium">
                                Konfiguruj →
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Geodis Freight Matrix -->
            <div class="bg-white overflow-hidden shadow rounded-lg border-l-4 border-red-400">
                <div class="px-6 py-4">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="h-10 w-10 bg-red-600 rounded-lg flex items-center justify-center mr-3">
                                <span class="text-white font-bold text-sm">GEO</span>
                            </div>
                            <div>
                                <h4 class="text-lg font-medium text-gray-900">Geodis Freight</h4>
                                <p class="text-sm text-gray-500">Macierz paletowa i eksportowa</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Aktywna
                        </span>
                    </div>
                    
                    <div class="grid grid-cols-4 gap-3 mb-4 text-xs">
                        <div class="text-center">
                            <div class="font-medium text-gray-500">Strefa</div>
                        </div>
                        <div class="text-center">
                            <div class="font-medium text-gray-500">do 300kg</div>
                        </div>
                        <div class="text-center">
                            <div class="font-medium text-gray-500">301-500kg</div>
                        </div>
                        <div class="text-center">
                            <div class="font-medium text-gray-500">501kg+</div>
                        </div>
                        
                        <div class="text-center font-medium">Krajowa</div>
                        <div class="text-center">120.00 zł</div>
                        <div class="text-center">165.00 zł</div>
                        <div class="text-center">285.00 zł</div>
                        
                        <div class="text-center font-medium">UE 2A</div>
                        <div class="text-center">185.00 zł</div>
                        <div class="text-center">245.00 zł</div>
                        <div class="text-center">385.00 zł</div>
                        
                        <div class="text-center font-medium">UE 2B</div>
                        <div class="text-center">205.00 zł</div>
                        <div class="text-center">275.00 zł</div>
                        <div class="text-center">425.00 zł</div>
                    </div>
                    
                    <div class="border-t pt-3">
                        <div class="text-xs text-gray-500 mb-2">Dopłaty:</div>
                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <div>• Paliwo: 18.5%</div>
                            <div>• Handling: 25.00 zł</div>
                            <div>• Odbiór/Dostawa: 45.00 zł</div>
                            <div>• Ponadgabaryt: +50%</div>
                        </div>
                    </div>
                    
                    <div class="mt-4 flex justify-between items-center">
                        <div class="text-xs text-gray-500">
                            Ostatnia aktualizacja: 2024-08-28 15:45
                        </div>
                        <div class="flex space-x-2">
                            <a href="{{ route('admin.settings.pricing.pallet.edit', 'geodis') }}" class="text-sm text-blue-600 hover:text-blue-500 font-medium">
                                Konfiguruj →
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- DHL Matrix -->
            <div class="bg-white overflow-hidden shadow rounded-lg border-l-4 border-red-500">
                <div class="px-6 py-4">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="h-10 w-10 bg-red-600 rounded-lg flex items-center justify-center mr-3">
                                <span class="text-white font-bold text-sm">DHL</span>
                            </div>
                            <div>
                                <h4 class="text-lg font-medium text-gray-900">DHL Express</h4>
                                <p class="text-sm text-gray-500">Macierz wagowo-strefowa międzynarodowa</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Aktywna
                        </span>
                    </div>
                    
                    <div class="grid grid-cols-4 gap-3 mb-4 text-xs">
                        <div class="text-center">
                            <div class="font-medium text-gray-500">Waga/Strefa</div>
                        </div>
                        <div class="text-center">
                            <div class="font-medium text-gray-500">UE-1</div>
                        </div>
                        <div class="text-center">
                            <div class="font-medium text-gray-500">UE-2</div>
                        </div>
                        <div class="text-center">
                            <div class="font-medium text-gray-500">Świat-1</div>
                        </div>
                        
                        <div class="text-center font-medium">0.5kg</div>
                        <div class="text-center">45.00 zł</div>
                        <div class="text-center">52.00 zł</div>
                        <div class="text-center">85.00 zł</div>
                        
                        <div class="text-center font-medium">1.0kg</div>
                        <div class="text-center">58.00 zł</div>
                        <div class="text-center">68.00 zł</div>
                        <div class="text-center">125.00 zł</div>
                        
                        <div class="text-center font-medium">2.0kg</div>
                        <div class="text-center">75.00 zł</div>
                        <div class="text-center">89.00 zł</div>
                        <div class="text-center">185.00 zł</div>
                    </div>
                    
                    <div class="border-t pt-3">
                        <div class="text-xs text-gray-500 mb-2">Dopłaty:</div>
                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <div>• Paliwo: 15.2%</div>
                            <div>• Bezpieczeństwo: 0.75%</div>
                            <div>• Remote area: +35.00 zł</div>
                            <div>• Dokumenty: -15%</div>
                        </div>
                    </div>
                    
                    <div class="mt-4 flex justify-between items-center">
                        <div class="text-xs text-gray-500">
                            Ostatnia aktualizacja: 2024-09-02 10:15
                        </div>
                        <div class="flex space-x-2">
                            <a href="{{ route('admin.settings.pricing.edit', 'dhl-express') }}" class="text-sm text-blue-600 hover:text-blue-500 font-medium">
                                Konfiguruj →
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scheduled Changes -->
    <div class="mb-8">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Zaplanowane zmiany cenowe</h3>
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h4 class="text-sm font-medium text-gray-900">Aktywne harmonogramy</h4>
                    <button class="text-sm text-blue-600 hover:text-blue-500">+ Nowy harmonogram</button>
                </div>
            </div>
            <div class="divide-y divide-gray-200">
                <div class="px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="h-8 w-8 bg-orange-500 rounded flex items-center justify-center mr-3">
                                <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-900">Podwyżka InPost Paczkomat +5%</div>
                                <div class="text-sm text-gray-500">Aktywacja: 01.09.2024 00:00 • Wszystkie gabaryty</div>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                Za 3 dni
                            </span>
                            <button class="text-sm text-blue-600 hover:text-blue-500">Edytuj</button>
                            <button class="text-sm text-red-600 hover:text-red-500">Anuluj</button>
                        </div>
                    </div>
                </div>
                
                <div class="px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="h-8 w-8 bg-blue-500 rounded flex items-center justify-center mr-3">
                                <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-900">Promocja weekend -10% InPost Kurier</div>
                                <div class="text-sm text-gray-500">Aktywacja: 31.08.2024 18:00 • Tylko KLIENCJA</div>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                Jutro
                            </span>
                            <button class="text-sm text-blue-600 hover:text-blue-500">Edytuj</button>
                            <button class="text-sm text-red-600 hover:text-red-500">Anuluj</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Inactive/Draft Matrices -->
    <div class="mb-8">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Nieaktywne macierze</h3>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div class="bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg p-6 hover:border-gray-400 cursor-pointer">
                <div class="text-center">
                    <div class="h-12 w-12 bg-red-500 rounded-lg flex items-center justify-center mx-auto mb-3">
                        <span class="text-white font-bold text-sm">DPD</span>
                    </div>
                    <h4 class="text-sm font-medium text-gray-900 mb-1">DPD Classic</h4>
                    <p class="text-xs text-gray-500 mb-3">Macierz nie skonfigurowana</p>
                    <button class="text-sm text-blue-600 hover:text-blue-500 font-medium">
                        Konfiguruj macierz →
                    </button>
                </div>
            </div>
            
            <div class="bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg p-6 hover:border-gray-400 cursor-pointer">
                <div class="text-center">
                    <div class="h-12 w-12 bg-yellow-600 rounded-lg flex items-center justify-center mx-auto mb-3">
                        <span class="text-white font-bold text-sm">UPS</span>
                    </div>
                    <h4 class="text-sm font-medium text-gray-900 mb-1">UPS Standard</h4>
                    <p class="text-xs text-gray-500 mb-3">W trybie testowym</p>
                    <button class="text-sm text-blue-600 hover:text-blue-500 font-medium">
                        Dokończ konfigurację →
                    </button>
                </div>
            </div>
            
            <div class="bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg p-6 hover:border-gray-400 cursor-pointer">
                <div class="text-center">
                    <div class="h-12 w-12 bg-gray-400 rounded-lg flex items-center justify-center mx-auto mb-3">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                    </div>
                    <h4 class="text-sm font-medium text-gray-900 mb-1">Nowy kurier</h4>
                    <p class="text-xs text-gray-500 mb-3">Dodaj macierz cenową</p>
                    <button class="text-sm text-blue-600 hover:text-blue-500 font-medium">
                        Utwórz macierz →
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- System Configuration -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Konfiguracja systemu cenowego</h3>
        </div>
        <div class="px-6 py-4">
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <div class="bg-blue-50 p-4 rounded-lg">
                    <div class="flex items-center mb-2">
                        <svg class="h-5 w-5 text-blue-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <span class="text-sm font-medium text-gray-900">Typy klientów</span>
                    </div>
                    <p class="text-xs text-gray-600 mb-3">Konfiguracja poziomów cenowych</p>
                    <button class="text-sm text-blue-600 hover:text-blue-500 font-medium">
                        BASE / HEAVY USER / KLIENCJA →
                    </button>
                </div>
                
                <div class="bg-green-50 p-4 rounded-lg">
                    <div class="flex items-center mb-2">
                        <svg class="h-5 w-5 text-green-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-sm font-medium text-gray-900">Dopłaty i opłaty</span>
                    </div>
                    <p class="text-xs text-gray-600 mb-3">COD, ubezpieczenia, paliwo</p>
                    <button class="text-sm text-green-600 hover:text-green-500 font-medium">
                        Konfiguruj dopłaty →
                    </button>
                </div>
                
                <div class="bg-purple-50 p-4 rounded-lg">
                    <div class="flex items-center mb-2">
                        <svg class="h-5 w-5 text-purple-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-sm font-medium text-gray-900">Harmonogramowanie</span>
                    </div>
                    <p class="text-xs text-gray-600 mb-3">Automatyczne zmiany cen</p>
                    <button class="text-sm text-purple-600 hover:text-purple-500 font-medium">
                        Zarządzaj harmonogramami →
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection