@extends('layouts.admin')

@section('title', 'Konfiguracja kurierów')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="md:flex md:items-center md:justify-between mb-6">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                Konfiguracja kurierów
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Zarządzanie kurierami, ich API i usługami w systemie
            </p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4">
            <button type="button" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Dodaj kuriera
            </button>
        </div>
    </div>

    <!-- Aktywni kurierze -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 mb-8">
        <!-- InPost -->
        <div class="bg-white overflow-hidden shadow rounded-lg border-l-4 border-green-400">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 bg-yellow-500 rounded-lg flex items-center justify-center">
                                <span class="text-white font-bold text-sm">InP</span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">InPost</h3>
                            <p class="text-sm text-gray-500">Paczkomaty i Kurier</p>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Aktywny
                        </span>
                    </div>
                </div>
                
                <div class="mt-4 grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="font-medium text-gray-500">API Status</dt>
                        <dd class="mt-1 text-green-600">Połączony</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Usługi</dt>
                        <dd class="mt-1 text-gray-900">12 aktywnych</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Ostatnia sync</dt>
                        <dd class="mt-1 text-gray-900">2 min temu</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Przesyłki</dt>
                        <dd class="mt-1 text-gray-900">1,234 / miesiąc</dd>
                    </div>
                </div>
                
                <div class="mt-6 flex space-x-3">
                    <a href="#" class="text-sm text-blue-600 hover:text-blue-500 font-medium">
                        Konfiguruj →
                    </a>
                    <a href="#" class="text-sm text-gray-600 hover:text-gray-500">
                        Statystyki →
                    </a>
                    <a href="#" class="text-sm text-gray-600 hover:text-gray-500">
                        Testy API →
                    </a>
                </div>
            </div>
        </div>

        <!-- DPD -->
        <div class="bg-white overflow-hidden shadow rounded-lg border-l-4 border-gray-300">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 bg-red-500 rounded-lg flex items-center justify-center">
                                <span class="text-white font-bold text-sm">DPD</span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">DPD</h3>
                            <p class="text-sm text-gray-500">Przesyłki kurierskie</p>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                            Nieaktywny
                        </span>
                    </div>
                </div>
                
                <div class="mt-4 grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="font-medium text-gray-500">API Status</dt>
                        <dd class="mt-1 text-gray-600">Nie skonfigurowany</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Usługi</dt>
                        <dd class="mt-1 text-gray-900">0 aktywnych</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Ostatnia sync</dt>
                        <dd class="mt-1 text-gray-900">Nigdy</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Przesyłki</dt>
                        <dd class="mt-1 text-gray-900">0 / miesiąc</dd>
                    </div>
                </div>
                
                <div class="mt-6 flex space-x-3">
                    <a href="#" class="text-sm text-blue-600 hover:text-blue-500 font-medium">
                        Konfiguruj →
                    </a>
                    <span class="text-sm text-gray-400">Wymaga konfiguracji</span>
                </div>
            </div>
        </div>

        <!-- UPS -->
        <div class="bg-white overflow-hidden shadow rounded-lg border-l-4 border-gray-300">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 bg-yellow-600 rounded-lg flex items-center justify-center">
                                <span class="text-white font-bold text-sm">UPS</span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">UPS</h3>
                            <p class="text-sm text-gray-500">Międzynarodowe i krajowe</p>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            W przygotowaniu
                        </span>
                    </div>
                </div>
                
                <div class="mt-4 grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="font-medium text-gray-500">API Status</dt>
                        <dd class="mt-1 text-yellow-600">Testowy</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Usługi</dt>
                        <dd class="mt-1 text-gray-900">5 w testach</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Ostatnia sync</dt>
                        <dd class="mt-1 text-gray-900">1 godz. temu</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Przesyłki</dt>
                        <dd class="mt-1 text-gray-900">0 / miesiąc</dd>
                    </div>
                </div>
                
                <div class="mt-6 flex space-x-3">
                    <a href="#" class="text-sm text-blue-600 hover:text-blue-500 font-medium">
                        Konfiguruj →
                    </a>
                    <a href="#" class="text-sm text-gray-600 hover:text-gray-500">
                        Testy API →
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Podsumowanie systemowe -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2 mb-8">
        <!-- API Health Monitor -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                    Monitor API kurierów
                </h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="h-2 w-2 bg-green-400 rounded-full"></div>
                            <span class="ml-3 text-sm font-medium text-gray-900">InPost API</span>
                        </div>
                        <span class="text-sm text-gray-500">99.9% uptime</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="h-2 w-2 bg-gray-400 rounded-full"></div>
                            <span class="ml-3 text-sm font-medium text-gray-900">DPD API</span>
                        </div>
                        <span class="text-sm text-gray-500">Nie skonfigurowany</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="h-2 w-2 bg-yellow-400 rounded-full"></div>
                            <span class="ml-3 text-sm font-medium text-gray-900">UPS API</span>
                        </div>
                        <span class="text-sm text-gray-500">Tryb testowy</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statystyki globalarne -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                    Statystyki globalne
                </h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="font-medium text-gray-500">Aktywni kurierze</dt>
                        <dd class="mt-1 text-2xl font-semibold text-green-600">1</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Dostępne usługi</dt>
                        <dd class="mt-1 text-2xl font-semibold text-blue-600">17</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Przesyłki/miesiąc</dt>
                        <dd class="mt-1 text-2xl font-semibold text-gray-900">1,234</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Błędy API/dzień</dt>
                        <dd class="mt-1 text-2xl font-semibold text-red-600">0.2</dd>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Dostępni kurierze do dodania -->
    <div class="bg-gray-50 overflow-hidden shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                Dostępni kurierze do dodania
            </h3>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div class="bg-white p-4 rounded-lg border border-gray-200 hover:border-blue-300 cursor-pointer transition-colors">
                    <div class="flex items-center">
                        <div class="h-8 w-8 bg-green-500 rounded flex items-center justify-center">
                            <span class="text-white font-bold text-xs">FedEx</span>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900">FedEx</p>
                            <p class="text-xs text-gray-500">Międzynarodowy</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white p-4 rounded-lg border border-gray-200 hover:border-blue-300 cursor-pointer transition-colors">
                    <div class="flex items-center">
                        <div class="h-8 w-8 bg-blue-600 rounded flex items-center justify-center">
                            <span class="text-white font-bold text-xs">GLS</span>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900">GLS</p>
                            <p class="text-xs text-gray-500">Europejski</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white p-4 rounded-lg border border-gray-200 hover:border-blue-300 cursor-pointer transition-colors">
                    <div class="flex items-center">
                        <div class="h-8 w-8 bg-red-600 rounded flex items-center justify-center">
                            <span class="text-white font-bold text-xs">PP</span>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900">Poczta Polska</p>
                            <p class="text-xs text-gray-500">Krajowy</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white p-4 rounded-lg border border-gray-200 hover:border-blue-300 cursor-pointer transition-colors">
                    <div class="flex items-center">
                        <div class="h-8 w-8 bg-purple-600 rounded flex items-center justify-center">
                            <span class="text-white font-bold text-xs">+</span>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900">Własny kurier</p>
                            <p class="text-xs text-gray-500">Niestandardowy</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection