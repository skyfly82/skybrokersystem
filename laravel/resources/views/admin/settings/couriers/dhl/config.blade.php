@extends('layouts.admin')

@section('title', 'Konfiguracja DHL')

@section('content')
<div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="md:flex md:items-center md:justify-between mb-6">
        <div class="flex-1 min-w-0">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-4">
                    <li>
                        <a href="{{ route('admin.settings.index') }}" class="text-gray-400 hover:text-gray-500">
                            Ustawienia
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.settings.couriers') }}" class="text-gray-400 hover:text-gray-500">
                            <svg class="flex-shrink-0 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                            Kurierzy
                        </a>
                    </li>
                    <li>
                        <span class="text-gray-500">DHL Konfiguracja</span>
                    </li>
                </ol>
            </nav>
            
            <h2 class="mt-2 text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                Konfiguracja DHL
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Ustawienia integracji z DHL WebAPI 2.0 - zarządzanie przesyłkami i paletami
            </p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4">
            <a href="{{ route('admin.settings.couriers.dhl.test') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Test API
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-50 border-l-4 border-red-400 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-700">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Configuration Form -->
    <form method="POST" action="{{ route('admin.settings.couriers.dhl.config.update') }}" class="space-y-8">
        @csrf
        @method('PUT')

        <!-- Basic Settings -->
        <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
            <div class="md:grid md:grid-cols-3 md:gap-6">
                <div class="md:col-span-1">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Podstawowe ustawienia</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Główne ustawienia integracji DHL
                    </p>
                </div>
                <div class="mt-5 md:mt-0 md:col-span-2">
                    <div class="grid grid-cols-6 gap-6">
                        <!-- Enable DHL -->
                        <div class="col-span-6">
                            <div class="flex items-center">
                                <input type="hidden" name="enabled" value="0">
                                <input id="enabled" name="enabled" type="checkbox" value="1" 
                                       {{ $is_enabled ? 'checked' : '' }}
                                       class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                                <label for="enabled" class="ml-2 block text-sm text-gray-900">
                                    Włącz integrację DHL
                                </label>
                            </div>
                        </div>

                        <!-- Environment -->
                        <div class="col-span-6">
                            <div class="flex items-center">
                                <input type="hidden" name="sandbox" value="0">
                                <input id="sandbox" name="sandbox" type="checkbox" value="1" 
                                       {{ $config['sandbox'] ? 'checked' : '' }}
                                       class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                                <label for="sandbox" class="ml-2 block text-sm text-gray-900">
                                    Tryb testowy (Sandbox)
                                </label>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">
                                Używa środowiska testowego DHL. Wyłącz dla produkcji.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- API Credentials -->
        <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
            <div class="md:grid md:grid-cols-3 md:gap-6">
                <div class="md:col-span-1">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Dane dostępu API</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Dane autoryzacyjne do DHL WebAPI 2.0
                    </p>
                </div>
                <div class="mt-5 md:mt-0 md:col-span-2">
                    <div class="grid grid-cols-6 gap-6">
                        <!-- Username -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="username" class="block text-sm font-medium text-gray-700">
                                Nazwa użytkownika
                            </label>
                            <input type="text" name="username" id="username" 
                                   value="{{ old('username', $config['username'] ?? '') }}"
                                   class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            @error('username')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="password" class="block text-sm font-medium text-gray-700">
                                Hasło
                            </label>
                            <input type="password" name="password" id="password" 
                                   value="{{ old('password') }}"
                                   class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">
                                Zostaw puste, aby nie zmieniać hasła
                            </p>
                        </div>

                        <!-- Account Number -->
                        <div class="col-span-6">
                            <label for="account_number" class="block text-sm font-medium text-gray-700">
                                Numer klienta SAP
                            </label>
                            <input type="text" name="account_number" id="account_number" 
                                   value="{{ old('account_number', $config['account_number'] ?? '') }}"
                                   class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            @error('account_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Available Services -->
        <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
            <div class="md:grid md:grid-cols-3 md:gap-6">
                <div class="md:col-span-1">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Aktywne usługi</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Wybierz dostępne usługi DHL dla klientów
                    </p>
                </div>
                <div class="mt-5 md:mt-0 md:col-span-2">
                    <div class="space-y-4">
                        @foreach($config['services'] ?? [] as $service => $label)
                            <div class="flex items-center">
                                <input type="hidden" name="services[{{ $service }}]" value="0">
                                <input id="service_{{ $service }}" name="services[{{ $service }}]" 
                                       type="checkbox" value="1" checked
                                       class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                                <label for="service_{{ $service }}" class="ml-2 block text-sm text-gray-900">
                                    {{ $label }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Save Button -->
        <div class="flex justify-end">
            <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Zapisz konfigurację
            </button>
        </div>
    </form>
</div>
@endsection