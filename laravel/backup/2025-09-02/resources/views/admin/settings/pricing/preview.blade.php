@extends('layouts.admin')

@section('title', 'Podgląd cennika')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="md:flex md:items-center md:justify-between mb-6">
        <div class="flex-1 min-w-0">
            <nav class="flex mb-4" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('admin.settings.index') }}" class="text-gray-500 hover:text-gray-700">
                            Ustawienia
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <a href="{{ route('admin.settings.pricing') }}" class="ml-1 text-gray-500 hover:text-gray-700 md:ml-2">
                                Cenniki
                            </a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="ml-1 text-gray-700 md:ml-2">Podgląd</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                Podgląd cennika: Cennik bazowy
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Przegląd wszystkich cen i marż w aktualnym cenniku
            </p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3">
            <a href="{{ route('admin.settings.pricing.edit', $pricing_id) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edytuj
            </a>
            <button onclick="window.print()" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Drukuj
            </button>
            <button class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Eksportuj
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-4">
        <!-- Summary Stats -->
        <div class="lg:col-span-1">
            <div class="bg-white shadow rounded-lg p-6 mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Podsumowanie cennika</h3>
                
                <div class="space-y-4">
                    <div class="bg-green-50 p-3 rounded-lg">
                        <div class="flex items-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Aktywny
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 mt-2">Status cennika</p>
                    </div>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Usługi aktywne:</span>
                            <span class="text-sm font-medium text-gray-900">152</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Kurierzy:</span>
                            <span class="text-sm font-medium text-gray-900">3</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Średnia marża:</span>
                            <span class="text-sm font-medium text-green-600">15.2%</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Najniższa cena:</span>
                            <span class="text-sm font-medium text-gray-900">12.99 PLN</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Najwyższa cena:</span>
                            <span class="text-sm font-medium text-gray-900">29.99 PLN</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick filters -->
            <div class="bg-white shadow rounded-lg p-6">
                <h4 class="text-sm font-medium text-gray-900 mb-3">Filtry szybkie</h4>
                <div class="space-y-2">
                    <button class="w-full text-left px-3 py-2 text-sm rounded-lg bg-blue-50 text-blue-700 font-medium">
                        Wszystkie (152)
                    </button>
                    <button class="w-full text-left px-3 py-2 text-sm rounded-lg hover:bg-gray-50">
                        InPost (89)
                    </button>
                    <button class="w-full text-left px-3 py-2 text-sm rounded-lg hover:bg-gray-50">
                        DPD (41)
                    </button>
                    <button class="w-full text-left px-3 py-2 text-sm rounded-lg hover:bg-gray-50">
                        UPS (22)
                    </button>
                    <hr class="my-2">
                    <button class="w-full text-left px-3 py-2 text-sm rounded-lg hover:bg-gray-50">
                        Marża > 20% (18)
                    </button>
                    <button class="w-full text-left px-3 py-2 text-sm rounded-lg hover:bg-gray-50">
                        Cena > 25 PLN (34)
                    </button>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="lg:col-span-3">
            <!-- Search and filters -->
            <div class="bg-white shadow rounded-lg p-6 mb-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="relative">
                            <input type="text" placeholder="Szukaj usługi..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm">
                            <svg class="absolute left-3 top-2.5 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <select class="border-gray-300 rounded-lg shadow-sm text-sm">
                            <option value="">Wszystkie kurierzy</option>
                            <option value="inpost">InPost</option>
                            <option value="dpd">DPD</option>
                            <option value="ups">UPS</option>
                        </select>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-gray-500">Sortuj:</span>
                        <select class="border-gray-300 rounded-lg shadow-sm text-sm">
                            <option value="name">Nazwa A-Z</option>
                            <option value="price_asc">Cena rosnąco</option>
                            <option value="price_desc">Cena malejąco</option>
                            <option value="margin">Marża</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Services grid -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach([
                    ['name' => 'InPost Paczkomat', 'base_price' => 12.99, 'final_price' => 14.94, 'margin' => 15, 'courier' => 'InPost', 'type' => 'Paczkomat', 'color' => 'bg-yellow-500'],
                    ['name' => 'InPost Kurier', 'base_price' => 18.99, 'final_price' => 21.27, 'margin' => 12, 'courier' => 'InPost', 'type' => 'Kurier', 'color' => 'bg-yellow-500'],
                    ['name' => 'InPost Kurier pobranie', 'base_price' => 21.99, 'final_price' => 24.19, 'margin' => 10, 'courier' => 'InPost', 'type' => 'Pobranie', 'color' => 'bg-yellow-500'],
                    ['name' => 'DPD Classic', 'base_price' => 16.50, 'final_price' => 19.47, 'margin' => 18, 'courier' => 'DPD', 'type' => 'Standard', 'color' => 'bg-red-500'],
                    ['name' => 'UPS Standard', 'base_price' => 24.99, 'final_price' => 29.99, 'margin' => 20, 'courier' => 'UPS', 'type' => 'Standard', 'color' => 'bg-yellow-600'],
                    ['name' => 'InPost Paczkomat XL', 'base_price' => 15.99, 'final_price' => 18.39, 'margin' => 15, 'courier' => 'InPost', 'type' => 'Paczkomat XL', 'color' => 'bg-yellow-500'],
                ] as $service)
                <div class="bg-white rounded-lg shadow border border-gray-200 hover:shadow-md transition-shadow">
                    <div class="p-4">
                        <div class="flex items-center mb-3">
                            <div class="flex-shrink-0 h-10 w-10 {{ $service['color'] }} rounded-lg flex items-center justify-center">
                                <span class="text-white text-xs font-bold">
                                    @if($service['courier'] == 'InPost')InP
                                    @elseif($service['courier'] == 'DPD')DPD
                                    @else UPS
                                    @endif
                                </span>
                            </div>
                            <div class="ml-3 flex-1">
                                <h4 class="text-sm font-medium text-gray-900">{{ $service['name'] }}</h4>
                                <p class="text-xs text-gray-500">{{ $service['type'] }}</p>
                            </div>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium 
                                @if($service['margin'] >= 20) bg-red-100 text-red-800
                                @elseif($service['margin'] >= 15) bg-yellow-100 text-yellow-800
                                @else bg-green-100 text-green-800 @endif">
                                {{ $service['margin'] }}%
                            </span>
                        </div>
                        
                        <div class="space-y-2">
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-gray-500">Cena bazowa:</span>
                                <span class="text-sm text-gray-600">{{ number_format($service['base_price'], 2) }} PLN</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-gray-500">Marża:</span>
                                <span class="text-sm text-gray-600">+{{ number_format($service['final_price'] - $service['base_price'], 2) }} PLN</span>
                            </div>
                            <div class="flex justify-between items-center border-t pt-2">
                                <span class="text-sm font-medium text-gray-900">Cena finalna:</span>
                                <span class="text-lg font-bold text-blue-600">{{ number_format($service['final_price'], 2) }} PLN</span>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6 mt-6 rounded-lg shadow">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        Wyświetlane <span class="font-medium">1</span> do <span class="font-medium">6</span> z <span class="font-medium">152</span> usług
                    </div>
                    <div class="flex space-x-2">
                        <button class="px-3 py-1 text-sm text-gray-500 border border-gray-300 rounded hover:bg-gray-50">Poprzednia</button>
                        <button class="px-3 py-1 text-sm text-white bg-blue-600 border border-blue-600 rounded">1</button>
                        <button class="px-3 py-1 text-sm text-gray-500 border border-gray-300 rounded hover:bg-gray-50">2</button>
                        <button class="px-3 py-1 text-sm text-gray-500 border border-gray-300 rounded hover:bg-gray-50">3</button>
                        <span class="px-3 py-1 text-sm text-gray-500">...</span>
                        <button class="px-3 py-1 text-sm text-gray-500 border border-gray-300 rounded hover:bg-gray-50">26</button>
                        <button class="px-3 py-1 text-sm text-gray-500 border border-gray-300 rounded hover:bg-gray-50">Następna</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .no-print { display: none; }
    body { print-color-adjust: exact; }
}
</style>
@endsection