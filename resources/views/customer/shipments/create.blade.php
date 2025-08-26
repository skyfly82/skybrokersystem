@extends('layouts.customer')

@section('title', 'Nowa przesyłka')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="shipmentForm">
    <!-- Header -->
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-black-coal">Nowa przesyłka</h2>
                <p class="mt-1 text-sm font-body text-gray-500">Utwórz nową przesyłkę kurierską</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('customer.shipments.address-book') }}" 
                   class="text-skywave hover:text-skywave/80 text-sm font-medium flex items-center">
                    <i class="fas fa-address-book mr-1"></i>
                    Książka adresowa
                </a>
                <div class="h-4 w-px bg-gray-300"></div>
                <a href="{{ route('customer.shipments.index') }}" 
                   class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-xl"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Form -->
    <form method="POST" action="{{ route('customer.shipments.store') }}" class="space-y-6">
        @csrf
        
        <!-- Step 1: Courier Selection -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-heading font-medium text-black-coal mb-4">
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-skywave text-white text-sm font-medium mr-3">1</span>
                Wybierz kuriera i usługę
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($couriers as $courier)
                <div class="relative">
                    <input type="radio" 
                           name="courier_code" 
                           value="{{ $courier->code }}"
                           id="courier_{{ $courier->id }}"
                           x-model="form.courier_code"
                           @change="loadCourierServices('{{ $courier->code }}')"
                           class="sr-only peer"
                           required>
                    <label for="courier_{{ $courier->id }}" 
                           class="block p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-skywave/30 peer-checked:border-skywave peer-checked:bg-skywave/5">
                        <div class="flex items-center">
                            @if($courier->logo_url)
                            <img src="{{ $courier->logo_url }}" 
                                 alt="{{ $courier->name }}" 
                                 class="w-12 h-12 object-contain mr-3">
                            @else
                            <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-truck text-gray-400"></i>
                            </div>
                            @endif
                            <div>
                                <h4 class="font-heading font-medium text-black-coal">{{ $courier->name }}</h4>
                                <p class="text-sm text-gray-500">{{ $courier->description }}</p>
                            </div>
                        </div>
                    </label>
                </div>
                @endforeach
            </div>
            @error('courier_code')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror

            <!-- Service Type Selection -->
            <div x-show="availableServices.length > 0" x-transition class="mt-6">
                <label class="block text-sm font-medium text-gray-700 mb-3">Typ usługi</label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <template x-for="service in availableServices" :key="service.code">
                        <div>
                            <input type="radio" 
                                   name="service_type" 
                                   :value="service.code"
                                   :id="'service_' + service.code"
                                   x-model="form.service_type"
                                   @change="calculatePrice()"
                                   class="sr-only peer"
                                   required>
                            <label :for="'service_' + service.code" 
                                   class="block p-3 border border-gray-200 rounded-lg cursor-pointer hover:border-skywave/30 peer-checked:border-skywave peer-checked:bg-skywave/5">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <h5 class="font-heading font-medium text-black-coal" x-text="service.name"></h5>
                                        <p class="text-sm text-gray-500" x-text="service.description"></p>
                                    </div>
                                    <span class="text-sm font-medium text-skywave" x-text="service.estimated_price"></span>
                                </div>
                            </label>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Step 2: Sender Data -->
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-heading font-medium text-black-coal">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-skywave text-white text-sm font-medium mr-3">2</span>
                    Dane nadawcy
                </h3>
                <button type="button" 
                        @click="showAddressBook('sender')"
                        class="text-sm text-skywave hover:text-skywave/80 font-medium flex items-center">
                    <i class="fas fa-address-book mr-1"></i>
                    Wybierz z książki adresowej
                </button>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="sender_name" class="block text-sm font-medium text-gray-700">Nazwa firmy/Imię i nazwisko</label>
                    <input type="text" 
                           name="sender[name]" 
                           id="sender_name"
                           value="{{ old('sender.name', auth()->user()->customer->company_name) }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-skywave focus:border-skywave"
                           :class="validateFormField('sender_name') ? 'border-green-300' : 'border-gray-300'"
                           @input="$nextTick(() => $el.dispatchEvent(new Event('validate')))"
                           required>
                    @error('sender.name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="sender_phone" class="block text-sm font-medium text-gray-700">Telefon</label>
                    <input type="tel" 
                           name="sender[phone]" 
                           id="sender_phone"
                           value="{{ old('sender.phone', auth()->user()->customer->phone) }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-skywave focus:border-skywave"
                           required>
                    @error('sender.phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="sender_email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" 
                           name="sender[email]" 
                           id="sender_email"
                           value="{{ old('sender.email', auth()->user()->customer->email) }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-skywave focus:border-skywave"
                           required>
                    @error('sender.email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="md:col-span-2">
                    <label for="sender_address" class="block text-sm font-medium text-gray-700">Adres</label>
                    <textarea name="sender[address]" 
                              id="sender_address"
                              rows="2"
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-skywave focus:border-skywave"
                              required>{{ old('sender.address', auth()->user()->customer->company_address) }}</textarea>
                    @error('sender.address')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="sender_city" class="block text-sm font-medium text-gray-700">Miasto</label>
                    <input type="text" 
                           name="sender[city]" 
                           id="sender_city"
                           value="{{ old('sender.city', auth()->user()->customer->city) }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-skywave focus:border-skywave"
                           required>
                    @error('sender.city')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="sender_postal_code" class="block text-sm font-medium text-gray-700">Kod pocztowy</label>
                    <input type="text" 
                           name="sender[postal_code]" 
                           id="sender_postal_code"
                           value="{{ old('sender.postal_code', auth()->user()->customer->postal_code) }}"
                           pattern="[0-9]{2}-[0-9]{3}"
                           placeholder="00-000"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-skywave focus:border-skywave"
                           required>
                    @error('sender.postal_code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Save to Address Book -->
                <div class="md:col-span-2 pt-4 border-t border-gray-100">
                    <div class="flex items-center justify-between">
                        <label class="flex items-center">
                            <input type="checkbox" 
                                   x-model="form.save_sender_to_book"
                                   class="h-4 w-4 text-skywave focus:ring-skywave border-gray-300 rounded">
                            <span class="ml-2 text-sm text-gray-600">Zapisz nadawcę w książce adresowej</span>
                        </label>
                        <div x-show="form.save_sender_to_book" x-transition>
                            <input type="text" 
                                   x-model="form.sender_book_name"
                                   placeholder="Nazwa w książce adresowej"
                                   class="text-sm border-gray-300 rounded-md focus:ring-skywave focus:border-skywave">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 2a: Financial Data Notice -->
        @if(!auth('customer_user')->user()->customer->cod_return_account || !auth('customer_user')->user()->customer->settlement_account)
        <div class="bg-white shadow rounded-lg p-6">
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex">
                    <i class="fas fa-exclamation-triangle text-yellow-400 mt-0.5 mr-2"></i>
                    <div class="text-sm">
                        <p class="text-yellow-800 font-medium">Brakują dane finansowe</p>
                        <div class="text-yellow-700 mt-1 space-y-1 text-xs">
                            <p>• Przed wysłaniem przesyłek ustaw konta bankowe w profilu firmy</p>
                            <p>• Dane finansowe możesz uzupełnić w <a href="{{ route('customer.profile.show') }}" class="underline font-medium">ustawieniach profilu</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Step 3: Recipient Data -->
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-heading font-medium text-black-coal">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-skywave text-white text-sm font-medium mr-3">3</span>
                    Dane odbiorcy
                </h3>
                <div class="flex space-x-3">
                    <button type="button" 
                            @click="copySenderToRecipient()"
                            class="text-xs text-purple-600 hover:text-purple-800 font-medium flex items-center">
                        <i class="fas fa-copy mr-1"></i>
                        Kopiuj z nadawcy
                    </button>
                    <button type="button" 
                            @click="showAddressBook('recipient')"
                            class="text-sm text-skywave hover:text-skywave/80 font-medium flex items-center">
                        <i class="fas fa-address-book mr-1"></i>
                        Wybierz z książki adresowej
                    </button>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="recipient_name" class="block text-sm font-medium text-gray-700">Imię i nazwisko</label>
                    <input type="text" 
                           name="recipient[name]" 
                           id="recipient_name"
                           value="{{ old('recipient.name') }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-skywave focus:border-skywave"
                           required>
                    @error('recipient.name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="recipient_phone" class="block text-sm font-medium text-gray-700">Telefon</label>
                    <input type="tel" 
                           name="recipient[phone]" 
                           id="recipient_phone"
                           value="{{ old('recipient.phone') }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-skywave focus:border-skywave"
                           required>
                    @error('recipient.phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="recipient_email" class="block text-sm font-medium text-gray-700">Email (opcjonalnie)</label>
                    <input type="email" 
                           name="recipient[email]" 
                           id="recipient_email"
                           value="{{ old('recipient.email') }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-skywave focus:border-skywave">
                    @error('recipient.email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Pickup Point Selection (for InPost lockers) -->
                <div x-show="needsPickupPoint" x-transition>
                    <label for="pickup_point" class="block text-sm font-medium text-gray-700">Paczkomat</label>
                    <div class="mt-1 relative">
                        <input type="text" 
                               name="pickup_point" 
                               id="pickup_point"
                               x-model="form.pickup_point"
                               placeholder="Wpisz miasto aby wyszukać paczkomaty"
                               @input="searchPickupPoints()"
                               class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-skywave focus:border-skywave">
                        
                        <!-- Pickup Points Dropdown -->
                        <div x-show="pickupPoints.length > 0" 
                             class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto">
                            <template x-for="point in pickupPoints" :key="point.id">
                                <div @click="selectPickupPoint(point)" 
                                     class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-skywave/5">
                                    <div>
                                        <span class="font-medium" x-text="point.name"></span>
                                        <span class="text-gray-500 ml-2" x-text="point.address"></span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
                
                <!-- Address for courier delivery -->
                <div x-show="!needsPickupPoint" x-transition class="md:col-span-2">
                    <label for="recipient_address" class="block text-sm font-medium text-gray-700">Adres dostawy</label>
                    <textarea name="recipient[address]" 
                              id="recipient_address"
                              rows="2"
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-skywave focus:border-skywave"
                              :required="!needsPickupPoint">{{ old('recipient.address') }}</textarea>
                    @error('recipient.address')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div x-show="!needsPickupPoint" x-transition>
                    <label for="recipient_city" class="block text-sm font-medium text-gray-700">Miasto</label>
                    <input type="text" 
                           name="recipient[city]" 
                           id="recipient_city"
                           value="{{ old('recipient.city') }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-skywave focus:border-skywave"
                           :required="!needsPickupPoint">
                    @error('recipient.city')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div x-show="!needsPickupPoint" x-transition>
                    <label for="recipient_postal_code" class="block text-sm font-medium text-gray-700">Kod pocztowy</label>
                    <input type="text" 
                           name="recipient[postal_code]" 
                           id="recipient_postal_code"
                           value="{{ old('recipient.postal_code') }}"
                           pattern="[0-9]{2}-[0-9]{3}"
                           placeholder="00-000"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-skywave focus:border-skywave"
                           :required="!needsPickupPoint">
                    @error('recipient.postal_code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Save to Address Book -->
                <div class="md:col-span-2 pt-4 border-t border-gray-100">
                    <div class="flex items-center justify-between">
                        <label class="flex items-center">
                            <input type="checkbox" 
                                   x-model="form.save_recipient_to_book"
                                   class="h-4 w-4 text-skywave focus:ring-skywave border-gray-300 rounded">
                            <span class="ml-2 text-sm text-gray-600">Zapisz odbiorcę w książce adresowej</span>
                        </label>
                        <div x-show="form.save_recipient_to_book" x-transition>
                            <input type="text" 
                                   x-model="form.recipient_book_name"
                                   placeholder="Nazwa w książce adresowej"
                                   class="text-sm border-gray-300 rounded-md focus:ring-skywave focus:border-skywave">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 4: Package Data -->
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-heading font-medium text-black-coal">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-skywave text-white text-sm font-medium mr-3">4</span>
                    Dane paczki
                </h3>
                <div class="flex space-x-2">
                    <button type="button" @click="setPackagePreset('small')" 
                            class="text-xs bg-gray-100 hover:bg-gray-200 px-2 py-1 rounded text-gray-700">
                        Mała (20×15×10cm, 1kg)
                    </button>
                    <button type="button" @click="setPackagePreset('medium')" 
                            class="text-xs bg-gray-100 hover:bg-gray-200 px-2 py-1 rounded text-gray-700">
                        Średnia (30×20×15cm, 2kg)
                    </button>
                    <button type="button" @click="setPackagePreset('large')" 
                            class="text-xs bg-gray-100 hover:bg-gray-200 px-2 py-1 rounded text-gray-700">
                        Duża (40×30×20cm, 5kg)
                    </button>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div>
                    <label for="package_weight" class="block text-sm font-medium text-gray-700">Waga (kg)</label>
                    <input type="number" 
                           name="package[weight]" 
                           id="package_weight"
                           value="{{ old('package.weight', '1') }}"
                           step="0.1"
                           min="0.1"
                           max="30"
                           x-model="form.package.weight"
                           @input="calculatePrice()"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-skywave focus:border-skywave"
                           required>
                    @error('package.weight')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="package_length" class="block text-sm font-medium text-gray-700">Długość (cm)</label>
                    <input type="number" 
                           name="package[length]" 
                           id="package_length"
                           value="{{ old('package.length', '20') }}"
                           min="1"
                           max="100"
                           x-model="form.package.length"
                           @input="calculatePrice()"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-skywave focus:border-skywave"
                           required>
                    @error('package.length')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="package_width" class="block text-sm font-medium text-gray-700">Szerokość (cm)</label>
                    <input type="number" 
                           name="package[width]" 
                           id="package_width"
                           value="{{ old('package.width', '15') }}"
                           min="1"
                           max="100"
                           x-model="form.package.width"
                           @input="calculatePrice()"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-skywave focus:border-skywave"
                           required>
                    @error('package.width')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="package_height" class="block text-sm font-medium text-gray-700">Wysokość (cm)</label>
                    <input type="number" 
                           name="package[height]" 
                           id="package_height"
                           value="{{ old('package.height', '10') }}"
                           min="1"
                           max="100"
                           x-model="form.package.height"
                           @input="calculatePrice()"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-skywave focus:border-skywave"
                           required>
                    @error('package.height')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Step 5: Additional Services -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-heading font-medium text-black-coal mb-4">
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-skywave text-white text-sm font-medium mr-3">5</span>
                Usługi dodatkowe
            </h3>
            
            <div class="space-y-4">
                <!-- COD -->
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input type="checkbox" 
                               name="cod_enabled" 
                               id="cod_enabled"
                               x-model="form.cod_enabled"
                               class="focus:ring-skywave h-4 w-4 text-skywave border-gray-300 rounded">
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="cod_enabled" class="font-medium text-gray-700">Pobranie (COD)</label>
                        <p class="text-gray-500">Pobierz płatność od odbiorcy</p>
                    </div>
                </div>
                
                <div x-show="form.cod_enabled" x-transition class="ml-7">
                    <label for="cod_amount" class="block text-sm font-medium text-gray-700">Kwota pobrania (PLN)</label>
                    <input type="number" 
                           name="cod_amount" 
                           id="cod_amount"
                           step="0.01"
                           min="0"
                           x-model="form.cod_amount"
                           class="mt-1 block w-full max-w-xs border-gray-300 rounded-md shadow-sm focus:ring-skywave focus:border-skywave">
                </div>

                <!-- Insurance -->
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input type="checkbox" 
                               name="insurance_enabled" 
                               id="insurance_enabled"
                               x-model="form.insurance_enabled"
                               class="focus:ring-skywave h-4 w-4 text-skywave border-gray-300 rounded">
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="insurance_enabled" class="font-medium text-gray-700">Ubezpieczenie</label>
                        <p class="text-gray-500">Ubezpiecz przesyłkę na wyższą kwotę</p>
                    </div>
                </div>
                
                <div x-show="form.insurance_enabled" x-transition class="ml-7">
                    <label for="insurance_amount" class="block text-sm font-medium text-gray-700">Wartość ubezpieczenia (PLN)</label>
                    <input type="number" 
                           name="insurance_amount" 
                           id="insurance_amount"
                           step="0.01"
                           min="0"
                           x-model="form.insurance_amount"
                           class="mt-1 block w-full max-w-xs border-gray-300 rounded-md shadow-sm focus:ring-skywave focus:border-skywave">
                </div>
            </div>

            <!-- Reference Number -->
            <div class="mt-6">
                <label for="reference_number" class="block text-sm font-medium text-gray-700">Numer referencyjny (opcjonalnie)</label>
                <input type="text" 
                       name="reference_number" 
                       id="reference_number"
                       value="{{ old('reference_number') }}"
                       placeholder="Twój wewnętrzny numer przesyłki"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-skywave focus:border-skywave">
                @error('reference_number')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Notes -->
            <div class="mt-6">
                <label for="notes" class="block text-sm font-medium text-gray-700">Uwagi (opcjonalnie)</label>
                <textarea name="notes" 
                          id="notes"
                          rows="3"
                          placeholder="Dodatkowe informacje o przesyłce..."
                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-skywave focus:border-skywave">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Price Summary -->
        <div x-show="estimatedPrice" x-transition class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-heading font-medium text-black-coal mb-4">Podsumowanie kosztów</h3>
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Koszt przesyłki:</span>
                    <span class="font-medium" x-text="estimatedPrice"></span>
                </div>
                <div x-show="form.cod_enabled && form.cod_amount" class="flex justify-between items-center mt-2">
                    <span class="text-sm text-gray-600">Opłata za pobranie:</span>
                    <span class="font-medium">3.00 PLN</span>
                </div>
                <div x-show="form.insurance_enabled && form.insurance_amount" class="flex justify-between items-center mt-2">
                    <span class="text-sm text-gray-600">Opłata za ubezpieczenie:</span>
                    <span class="font-medium" x-text="insuranceFee"></span>
                </div>
                <hr class="my-3">
                <div class="flex justify-between items-center text-lg font-semibold">
                    <span>Razem:</span>
                    <span x-text="totalPrice"></span>
                </div>
            </div>
        </div>

        <!-- Progress & Validation Status -->
        <div class="bg-white shadow rounded-lg p-4 mb-6" x-show="form.courier_code">
            <div class="flex items-center justify-between mb-4">
                <h4 class="font-heading font-medium text-black-coal">Postęp wypełniania formularza</h4>
                <div class="flex items-center space-x-2">
                    <span class="text-sm font-medium text-skywave" x-text="getFormProgress() + '%'"></span>
                    <div class="relative">
                        <i class="fas fa-info-circle text-gray-400 hover:text-gray-600 cursor-help" 
                           @mouseenter="showProgressHelp = true" 
                           @mouseleave="showProgressHelp = false"></i>
                        <div x-show="showProgressHelp" 
                             x-transition
                             class="absolute right-0 top-6 w-64 p-2 bg-gray-800 text-white text-xs rounded shadow-lg z-10">
                            Postęp kalkulowany na podstawie wypełnionych sekcji: kurier (30%), dane (50%), wymiary (10%), wycena (10%)
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Enhanced Progress Bar -->
            <div class="relative w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-r from-skywave via-blue-400 to-green-400 h-full rounded-full transition-all duration-500 ease-out" 
                     :style="'width: ' + getFormProgress() + '%'"></div>
                <div class="absolute inset-0 bg-white bg-opacity-20 h-full rounded-full animate-pulse"
                     x-show="getFormProgress() > 0 && getFormProgress() < 100"></div>
            </div>
            
            <!-- Enhanced Step Indicators -->
            <div class="flex justify-between mt-4 text-xs">
                <div class="flex flex-col items-center space-y-1" :class="form.courier_code ? 'text-green-600' : 'text-gray-400'">
                    <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center" 
                         :class="form.courier_code ? 'border-green-600 bg-green-100' : 'border-gray-300'">
                        <i class="fas fa-check text-xs" x-show="form.courier_code"></i>
                        <span class="text-xs font-bold" x-show="!form.courier_code">1</span>
                    </div>
                    <span>Kurier</span>
                </div>
                <div class="flex flex-col items-center space-y-1" :class="isFormValid ? 'text-green-600' : 'text-gray-400'">
                    <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center" 
                         :class="isFormValid ? 'border-green-600 bg-green-100' : 'border-gray-300'">
                        <i class="fas fa-check text-xs" x-show="isFormValid"></i>
                        <span class="text-xs font-bold" x-show="!isFormValid">2</span>
                    </div>
                    <span>Dane</span>
                </div>
                <div class="flex flex-col items-center space-y-1" :class="form.package.weight && form.package.length ? 'text-green-600' : 'text-gray-400'">
                    <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center" 
                         :class="form.package.weight && form.package.length ? 'border-green-600 bg-green-100' : 'border-gray-300'">
                        <i class="fas fa-check text-xs" x-show="form.package.weight && form.package.length"></i>
                        <span class="text-xs font-bold" x-show="!form.package.weight || !form.package.length">3</span>
                    </div>
                    <span>Wymiary</span>
                </div>
                <div class="flex flex-col items-center space-y-1" :class="estimatedPrice ? 'text-green-600' : 'text-gray-400'">
                    <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center" 
                         :class="estimatedPrice ? 'border-green-600 bg-green-100' : 'border-gray-300'">
                        <i class="fas fa-check text-xs" x-show="estimatedPrice"></i>
                        <span class="text-xs font-bold" x-show="!estimatedPrice">4</span>
                    </div>
                    <span>Wycena</span>
                </div>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center justify-between">
                <a href="{{ route('customer.shipments.index') }}" 
                   class="bg-white border border-gray-300 rounded-md px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Anuluj
                </a>
                
                <div class="flex space-x-3">
                    <button type="button"
                            @click="showBulkRecipients()"
                            :disabled="!form.courier_code || !form.service_type"
                            class="bg-purple-100 hover:bg-purple-200 disabled:bg-gray-300 text-purple-700 px-4 py-2 rounded-md text-sm font-body font-medium border border-purple-200 relative group"
                            :class="{ 'opacity-50 cursor-not-allowed': !form.courier_code || !form.service_type }"
                            :title="!form.courier_code || !form.service_type ? 'Najpierw wybierz kuriera i usługę' : 'Dodaj wielu odbiorców dla tej samej przesyłki'">
                        <i class="fas fa-users mr-2"></i>
                        Wielu odbiorców
                        
                        <!-- Tooltip -->
                        <div x-show="!form.courier_code || !form.service_type"
                             class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 bg-gray-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">
                            Najpierw wybierz kuriera i usługę
                        </div>
                    </button>
                    <button type="button"
                            @click="addToCart()"
                            :disabled="!canSubmit"
                            class="bg-gray-100 hover:bg-gray-200 disabled:bg-gray-300 text-gray-700 px-4 py-2 rounded-md text-sm font-body font-medium border border-gray-300"
                            :class="{ 'opacity-50 cursor-not-allowed': !canSubmit }">
                        <i class="fas fa-cart-plus mr-2"></i>
                        Dodaj do koszyka
                    </button>
                    <button type="submit" 
                            name="save_draft"
                            value="1"
                            class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-body font-medium">
                        Zapisz szkic
                    </button>
                    <button type="submit" 
                            :disabled="!canSubmit"
                            class="bg-skywave hover:bg-skywave/90 disabled:bg-gray-300 text-white px-4 py-2 rounded-md text-sm font-body font-medium"
                            :class="{ 'opacity-50 cursor-not-allowed': !canSubmit }">
                        Utwórz i opłać teraz
                    </button>
                </div>
            </div>
        </div>
    </form>

    <!-- Bulk Recipients Modal -->
    <div x-show="showBulkModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-500 bg-opacity-50 flex items-center justify-center z-50"
         @click.outside="closeBulkModal()">
        
        <div class="bg-white rounded-lg shadow-lg max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-heading font-medium text-black-coal">Dodaj wielu odbiorców</h3>
                        <p class="text-sm text-gray-500 mt-1">Użyj tych samych parametrów przesyłki dla różnych odbiorców</p>
                    </div>
                    <button @click="closeBulkModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            
            <div class="p-6">
                <!-- Current Settings Summary -->
                <div class="bg-skywave/5 rounded-lg p-4 mb-6 border border-skywave/20">
                    <h4 class="font-medium text-black-coal mb-2">Ustawienia przesyłki:</h4>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-600">Kurier:</span>
                            <span class="font-medium ml-2" x-text="getSelectedCourierName()"></span>
                        </div>
                        <div>
                            <span class="text-gray-600">Usługa:</span>
                            <span class="font-medium ml-2" x-text="getSelectedServiceName()"></span>
                        </div>
                        <div>
                            <span class="text-gray-600">Wymiary:</span>
                            <span class="font-medium ml-2" x-text="`${form.package.length}×${form.package.width}×${form.package.height}cm`"></span>
                        </div>
                        <div>
                            <span class="text-gray-600">Waga:</span>
                            <span class="font-medium ml-2" x-text="`${form.package.weight}kg`"></span>
                        </div>
                    </div>
                </div>

                <!-- Recipients List -->
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h4 class="font-medium text-black-coal">Lista odbiorców:</h4>
                        <button @click="addBulkRecipient()" 
                                class="bg-skywave hover:bg-skywave/90 text-white px-3 py-1 rounded text-sm">
                            <i class="fas fa-plus mr-1"></i>
                            Dodaj odbiorcę
                        </button>
                    </div>
                    
                    <template x-for="(recipient, index) in bulkRecipients" :key="'recipient-' + index">
                        <div class="border border-gray-200 rounded-lg p-4 space-y-4">
                            <div class="flex items-center justify-between">
                                <h5 class="font-medium text-gray-700" x-text="`Odbiorca ${index + 1}`"></h5>
                                <button @click="removeBulkRecipient(index)" 
                                        class="text-red-600 hover:text-red-800 text-sm">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Imię i nazwisko</label>
                                    <input type="text" 
                                           x-model="recipient.name"
                                           class="w-full text-sm border-gray-300 rounded-md focus:ring-skywave focus:border-skywave"
                                           required>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Telefon</label>
                                    <input type="tel" 
                                           x-model="recipient.phone"
                                           class="w-full text-sm border-gray-300 rounded-md focus:ring-skywave focus:border-skywave"
                                           required>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Email</label>
                                    <input type="email" 
                                           x-model="recipient.email"
                                           class="w-full text-sm border-gray-300 rounded-md focus:ring-skywave focus:border-skywave">
                                </div>
                                
                                <!-- Address fields (conditional based on service type) -->
                                <template x-if="!needsPickupPoint">
                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Adres</label>
                                        <input type="text" 
                                               x-model="recipient.address"
                                               class="w-full text-sm border-gray-300 rounded-md focus:ring-skywave focus:border-skywave"
                                               required>
                                    </div>
                                </template>
                                <template x-if="!needsPickupPoint">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Miasto</label>
                                        <input type="text" 
                                               x-model="recipient.city"
                                               class="w-full text-sm border-gray-300 rounded-md focus:ring-skywave focus:border-skywave"
                                               required>
                                    </div>
                                </template>
                                <template x-if="!needsPickupPoint">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Kod pocztowy</label>
                                        <input type="text" 
                                               x-model="recipient.postal_code"
                                               pattern="[0-9]{2}-[0-9]{3}"
                                               placeholder="00-000"
                                               class="w-full text-sm border-gray-300 rounded-md focus:ring-skywave focus:border-skywave"
                                               required>
                                    </div>
                                </template>
                                
                                <!-- Pickup point for locker services -->
                                <template x-if="needsPickupPoint">
                                    <div class="md:col-span-3">
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Paczkomat</label>
                                        <input type="text" 
                                               x-model="recipient.pickup_point"
                                               placeholder="Nazwa paczkomatu"
                                               class="w-full text-sm border-gray-300 rounded-md focus:ring-skywave focus:border-skywave"
                                               required>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                    
                    <!-- Add more recipients button -->
                    <div x-show="bulkRecipients.length === 0" class="text-center py-8">
                        <div class="text-gray-400">
                            <i class="fas fa-users text-3xl mb-2"></i>
                            <p class="text-sm">Brak odbiorców. Kliknij "Dodaj odbiorcę" aby rozpocząć.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
                <div class="text-sm text-gray-600">
                    <span x-text="bulkRecipients.length"></span> odbiorców 
                    • Szacowany koszt: <span class="font-medium" x-text="getBulkTotalPrice()"></span>
                </div>
                <div class="flex space-x-3">
                    <button @click="closeBulkModal()" 
                            class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-md text-sm font-body font-medium">
                        Anuluj
                    </button>
                    <button @click="addBulkToCart()" 
                            :disabled="bulkRecipients.length === 0"
                            class="bg-skywave hover:bg-skywave/90 disabled:bg-gray-300 text-white px-4 py-2 rounded-md text-sm font-body font-medium">
                        <i class="fas fa-cart-plus mr-2"></i>
                        Dodaj wszystkie do koszyka
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Address Book Modal -->
    <div x-show="showAddressBookModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-500 bg-opacity-50 flex items-center justify-center z-50"
         @click.outside="closeAddressBook()">
        
        <div class="bg-white rounded-lg shadow-lg max-w-5xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-heading font-medium text-black-coal">
                            Książka adresowa - <span x-text="addressBookType === 'sender' ? 'Nadawcy' : 'Odbiorcy'"></span>
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">Wybierz adres z zapisanych lub dodaj nowy</p>
                    </div>
                    <button @click="closeAddressBook()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            
            <div class="p-6">
                <!-- Search -->
                <div class="mb-6">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                        <input type="text" 
                               x-model="addressBookSearch"
                               placeholder="Szukaj w książce adresowej..."
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-skywave focus:border-skywave">
                    </div>
                </div>

                <!-- Address List -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <template x-for="address in filteredAddresses" :key="address.id">
                        <div class="border border-gray-200 rounded-lg p-4 hover:border-skywave cursor-pointer transition-colors"
                             @click="selectAddress(address)">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h4 class="font-medium text-black-coal" x-text="address.name"></h4>
                                    <p class="text-sm text-gray-600 mt-1" x-text="address.company"></p>
                                    <p class="text-sm text-gray-500 mt-1">
                                        <span x-text="address.address"></span><br>
                                        <span x-text="address.postal_code + ' ' + address.city"></span>
                                    </p>
                                    <p class="text-xs text-gray-400 mt-2">
                                        Tel: <span x-text="address.phone"></span>
                                        <span x-show="address.email"> | Email: <span x-text="address.email"></span></span>
                                    </p>
                                </div>
                                <div class="flex space-x-2">
                                    <button @click.stop="editAddress(address)" 
                                            class="text-gray-400 hover:text-gray-600 text-sm">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button @click.stop="deleteAddress(address.id)" 
                                            class="text-red-400 hover:text-red-600 text-sm">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Empty State -->
                <div x-show="filteredAddresses.length === 0" class="text-center py-12">
                    <div class="text-gray-400">
                        <i class="fas fa-address-book text-4xl mb-4"></i>
                        <h4 class="text-lg font-medium text-gray-600 mb-2">Brak adresów</h4>
                        <p class="text-sm">Dodaj pierwszy adres do książki adresowej</p>
                    </div>
                </div>

                <!-- Add New Address Form -->
                <div x-show="showNewAddressForm" x-transition class="border-t border-gray-200 pt-6 mt-6">
                    <h4 class="font-medium text-black-coal mb-4">Dodaj nowy adres</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nazwa</label>
                            <input type="text" x-model="newAddress.name" 
                                   class="w-full border-gray-300 rounded-md focus:ring-skywave focus:border-skywave">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Firma</label>
                            <input type="text" x-model="newAddress.company" 
                                   class="w-full border-gray-300 rounded-md focus:ring-skywave focus:border-skywave">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Telefon</label>
                            <input type="tel" x-model="newAddress.phone" 
                                   class="w-full border-gray-300 rounded-md focus:ring-skywave focus:border-skywave">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" x-model="newAddress.email" 
                                   class="w-full border-gray-300 rounded-md focus:ring-skywave focus:border-skywave">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Adres</label>
                            <input type="text" x-model="newAddress.address" 
                                   class="w-full border-gray-300 rounded-md focus:ring-skywave focus:border-skywave">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Miasto</label>
                            <input type="text" x-model="newAddress.city" 
                                   class="w-full border-gray-300 rounded-md focus:ring-skywave focus:border-skywave">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kod pocztowy</label>
                            <input type="text" x-model="newAddress.postal_code" 
                                   pattern="[0-9]{2}-[0-9]{3}" placeholder="00-000"
                                   class="w-full border-gray-300 rounded-md focus:ring-skywave focus:border-skywave">
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
                <div class="flex space-x-3">
                    <button @click="toggleNewAddressForm()" 
                            class="text-sm text-skywave hover:text-skywave/80 font-medium">
                        <i class="fas fa-plus mr-1"></i>
                        <span x-show="!showNewAddressForm">Dodaj nowy adres</span>
                        <span x-show="showNewAddressForm">Anuluj dodawanie</span>
                    </button>
                </div>
                <div class="flex space-x-3">
                    <button @click="closeAddressBook()" 
                            class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-md text-sm font-body font-medium">
                        Anuluj
                    </button>
                    <button x-show="showNewAddressForm" @click="saveNewAddress()" 
                            class="bg-skywave hover:bg-skywave/90 text-white px-4 py-2 rounded-md text-sm font-body font-medium">
                        <i class="fas fa-save mr-2"></i>
                        Zapisz adres
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Floating Cart Indicator -->
    <div x-show="getCartItemCount() > 0" 
         x-transition
         class="fixed bottom-6 right-6 z-40">
        <a href="{{ route('customer.shipments.cart') }}" 
           class="bg-skywave hover:bg-skywave/90 text-white rounded-full px-4 py-3 shadow-lg flex items-center space-x-2 transition-all duration-300">
            <i class="fas fa-shopping-cart"></i>
            <span class="font-medium" x-text="getCartItemCount()"></span>
            <span class="text-sm">w koszyku</span>
        </a>
    </div>
</div>

@push('scripts')
<script>
// Debug info
console.log('Shipment form initializing...');
document.addEventListener('alpine:init', () => {
    console.log('Alpine.js initializing shipmentForm...');
    Alpine.data('shipmentForm', () => ({
        form: {
            courier_code: '',
            service_type: '',
            pickup_point: '',
            package: {
                weight: 1,
                length: 20,
                width: 15,
                height: 10
            },
            cod_enabled: false,
            cod_amount: 0,
            insurance_enabled: false,
            insurance_amount: 0,
            save_sender_to_book: false,
            sender_book_name: '',
            save_recipient_to_book: false,
            recipient_book_name: ''
        },
        availableServices: [],
        pickupPoints: [],
        estimatedPrice: '',
        totalPrice: '',
        insuranceFee: '0.00 PLN',
        showBulkModal: false,
        bulkRecipients: [],
        showAddressBookModal: false,
        showProgressHelp: false,
        addressBookType: 'sender',
        addressBookSearch: '',
        showNewAddressForm: false,
        addresses: [],
        newAddress: {
            name: '',
            company: '',
            phone: '',
            email: '',
            address: '',
            city: '',
            postal_code: ''
        },
        
        get needsPickupPoint() {
            return this.form.service_type && this.form.service_type.includes('locker');
        },

        get filteredAddresses() {
            if (!this.addressBookSearch) return this.addresses;
            
            const search = this.addressBookSearch.toLowerCase();
            return this.addresses.filter(address => 
                address.name.toLowerCase().includes(search) ||
                address.company.toLowerCase().includes(search) ||
                address.city.toLowerCase().includes(search) ||
                address.address.toLowerCase().includes(search)
            );
        },
        
        get canSubmit() {
            return this.form.courier_code && this.form.service_type && 
                   (!this.needsPickupPoint || this.form.pickup_point) &&
                   this.isFormValid;
        },

        get isFormValid() {
            // Check required sender fields
            const senderValid = this.validateFormField('sender_name') &&
                               this.validateFormField('sender_phone') &&
                               this.validateFormField('sender_email') &&
                               this.validateFormField('sender_address') &&
                               this.validateFormField('sender_city') &&
                               this.validateFormField('sender_postal_code');

            // Check required recipient fields
            const recipientValid = this.validateFormField('recipient_name') &&
                                 this.validateFormField('recipient_phone') &&
                                 (!this.needsPickupPoint ? (
                                     this.validateFormField('recipient_address') &&
                                     this.validateFormField('recipient_city') &&
                                     this.validateFormField('recipient_postal_code')
                                 ) : this.form.pickup_point);

            return senderValid && recipientValid;
        },

        validateFormField(fieldName) {
            const field = document.querySelector(`[name="${fieldName}"], [name="sender[${fieldName.replace('sender_', '')}]"], [name="recipient[${fieldName.replace('recipient_', '')}]"]`);
            return field && field.value.trim().length > 0;
        },

        getFormProgress() {
            let progress = 0;
            
            // Step 1: Courier & Service (30%)
            if (this.form.courier_code) progress += 15;
            if (this.form.service_type) progress += 15;
            
            // Step 2: Form Data (50%)
            if (this.isFormValid) progress += 50;
            
            // Step 3: Package dimensions (10%)
            if (this.form.package.weight && this.form.package.length && 
                this.form.package.width && this.form.package.height) progress += 10;
            
            // Step 4: Price calculated (10%)
            if (this.estimatedPrice) progress += 10;
            
            return Math.min(100, progress);
        },

        getCartItemCount() {
            try {
                const cart = JSON.parse(localStorage.getItem('shipment_cart') || '[]');
                return cart.length;
            } catch (error) {
                return 0;
            }
        },

        // Address Book Methods
        init() {
            // Load addresses from localStorage
            this.loadAddresses();
            
            // Check if we have a selected address from URL
            this.checkSelectedAddress();
        },

        checkSelectedAddress() {
            try {
                const selectedAddress = localStorage.getItem('selected_address');
                const urlParams = new URLSearchParams(window.location.search);
                const addressType = urlParams.get('address_type');
                
                if (selectedAddress && addressType) {
                    const address = JSON.parse(selectedAddress);
                    this.selectAddressFromStorage(address, addressType);
                    
                    // Clear the stored address
                    localStorage.removeItem('selected_address');
                    
                    // Remove URL parameter
                    const newUrl = window.location.pathname;
                    window.history.replaceState({}, document.title, newUrl);
                }
            } catch (error) {
                console.error('Error checking selected address:', error);
            }
        },

        selectAddressFromStorage(address, type) {
            if (type === 'sender') {
                document.getElementById('sender_name').value = address.name;
                document.getElementById('sender_phone').value = address.phone;
                document.getElementById('sender_email').value = address.email;
                document.getElementById('sender_address').value = address.address;
                document.getElementById('sender_city').value = address.city;
                document.getElementById('sender_postal_code').value = address.postal_code;
            } else if (type === 'recipient') {
                document.getElementById('recipient_name').value = address.name;
                document.getElementById('recipient_phone').value = address.phone;
                document.getElementById('recipient_email').value = address.email || '';
                if (!this.needsPickupPoint) {
                    document.getElementById('recipient_address').value = address.address;
                    document.getElementById('recipient_city').value = address.city;
                    document.getElementById('recipient_postal_code').value = address.postal_code;
                }
            }
        },

        loadAddresses() {
            try {
                const savedAddresses = localStorage.getItem('address_book');
                if (savedAddresses) {
                    this.addresses = JSON.parse(savedAddresses);
                }
            } catch (error) {
                console.error('Error loading address book:', error);
                this.addresses = [];
            }
        },

        saveAddresses() {
            localStorage.setItem('address_book', JSON.stringify(this.addresses));
        },

        showAddressBook(type) {
            this.addressBookType = type;
            this.showAddressBookModal = true;
            this.addressBookSearch = '';
            this.showNewAddressForm = false;
        },

        closeAddressBook() {
            this.showAddressBookModal = false;
            this.resetNewAddress();
        },

        selectAddress(address) {
            if (this.addressBookType === 'sender') {
                // Fill sender fields
                document.getElementById('sender_name').value = address.name;
                document.getElementById('sender_phone').value = address.phone;
                document.getElementById('sender_email').value = address.email;
                document.getElementById('sender_address').value = address.address;
                document.getElementById('sender_city').value = address.city;
                document.getElementById('sender_postal_code').value = address.postal_code;
            } else {
                // Fill recipient fields
                document.getElementById('recipient_name').value = address.name;
                document.getElementById('recipient_phone').value = address.phone;
                document.getElementById('recipient_email').value = address.email || '';
                if (!this.needsPickupPoint) {
                    document.getElementById('recipient_address').value = address.address;
                    document.getElementById('recipient_city').value = address.city;
                    document.getElementById('recipient_postal_code').value = address.postal_code;
                }
            }
            this.closeAddressBook();
        },

        toggleNewAddressForm() {
            this.showNewAddressForm = !this.showNewAddressForm;
            if (!this.showNewAddressForm) {
                this.resetNewAddress();
            }
        },

        resetNewAddress() {
            this.newAddress = {
                name: '',
                company: '',
                phone: '',
                email: '',
                address: '',
                city: '',
                postal_code: ''
            };
        },

        saveNewAddress() {
            if (!this.newAddress.name || !this.newAddress.phone || !this.newAddress.address) {
                alert('Proszę wypełnić wymagane pola: Nazwa, Telefon, Adres');
                return;
            }

            const newAddr = {
                id: Date.now(),
                type: this.addressBookType,
                ...this.newAddress
            };

            this.addresses.push(newAddr);
            this.saveAddresses();
            
            // Auto-select the new address
            this.selectAddress(newAddr);
            
            this.resetNewAddress();
            this.showNewAddressForm = false;
        },

        editAddress(address) {
            this.newAddress = { ...address };
            this.showNewAddressForm = true;
        },

        deleteAddress(addressId) {
            if (confirm('Czy na pewno chcesz usunąć ten adres?')) {
                this.addresses = this.addresses.filter(addr => addr.id !== addressId);
                this.saveAddresses();
            }
        },

        // Package Preset Methods
        setPackagePreset(size) {
            const presets = {
                small: { weight: 1, length: 20, width: 15, height: 10 },
                medium: { weight: 2, length: 30, width: 20, height: 15 },
                large: { weight: 5, length: 40, width: 30, height: 20 }
            };
            
            const preset = presets[size];
            if (preset) {
                this.form.package = { ...preset };
                
                // Update form inputs
                document.getElementById('package_weight').value = preset.weight;
                document.getElementById('package_length').value = preset.length;
                document.getElementById('package_width').value = preset.width;
                document.getElementById('package_height').value = preset.height;
                
                // Trigger price calculation
                this.calculatePrice();
            }
        },

        // Smart Copy Methods
        copySenderToRecipient() {
            const senderName = document.getElementById('sender_name').value;
            const senderPhone = document.getElementById('sender_phone').value;
            const senderEmail = document.getElementById('sender_email').value;
            const senderAddress = document.getElementById('sender_address').value;
            const senderCity = document.getElementById('sender_city').value;
            const senderPostalCode = document.getElementById('sender_postal_code').value;

            if (!senderName) {
                alert('Najpierw wypełnij dane nadawcy');
                return;
            }

            // Fill recipient fields
            document.getElementById('recipient_name').value = senderName;
            document.getElementById('recipient_phone').value = senderPhone;
            document.getElementById('recipient_email').value = senderEmail;
            
            if (!this.needsPickupPoint) {
                document.getElementById('recipient_address').value = senderAddress;
                document.getElementById('recipient_city').value = senderCity;
                document.getElementById('recipient_postal_code').value = senderPostalCode;
            }
        },
        
        addToCart() {
            if (!this.canSubmit) {
                alert('Proszę wypełnić wszystkie wymagane pola.');
                return;
            }
            
            // Collect form data
            const cartItem = {
                id: Date.now(),
                courier_code: this.form.courier_code,
                service_type: this.form.service_type,
                sender: this.getSenderData(),
                recipient: this.getRecipientData(),
                package: this.form.package,
                options: this.getOptionsData(),
                estimated_price: this.totalPrice,
                created_at: new Date().toISOString()
            };
            
            // Save to localStorage
            let cart = JSON.parse(localStorage.getItem('shipment_cart') || '[]');
            cart.push(cartItem);
            localStorage.setItem('shipment_cart', JSON.stringify(cart));
            
            // Show success message
            this.$dispatch('show-toast', {
                message: 'Przesyłka została dodana do koszyka!',
                type: 'success'
            });
            
            // Ask user what to do next
            if (confirm('Przesyłka dodana do koszyka! Czy chcesz:\n\n✓ Dodać kolejną przesyłkę (OK)\n✓ Przejść do koszyka (Anuluj)')) {
                // Reset form for next shipment
                this.resetForm();
            } else {
                // Go to cart
                window.location.href = '{{ route("customer.shipments.cart") }}';
            }
        },
        
        getSenderData() {
            const form = document.querySelector('form');
            const formData = new FormData(form);
            return {
                name: formData.get('sender[name]'),
                phone: formData.get('sender[phone]'),
                email: formData.get('sender[email]'),
                address: formData.get('sender[address]'),
                city: formData.get('sender[city]'),
                postal_code: formData.get('sender[postal_code]')
            };
        },
        
        getRecipientData() {
            const form = document.querySelector('form');
            const formData = new FormData(form);
            return {
                name: formData.get('recipient[name]'),
                phone: formData.get('recipient[phone]'),
                email: formData.get('recipient[email]'),
                address: formData.get('recipient[address]'),
                city: formData.get('recipient[city]'),
                postal_code: formData.get('recipient[postal_code]')
            };
        },
        
        getOptionsData() {
            return {
                cod_enabled: this.form.cod_enabled,
                cod_amount: this.form.cod_amount,
                insurance_enabled: this.form.insurance_enabled,
                insurance_amount: this.form.insurance_amount,
                reference_number: document.querySelector('[name="reference_number"]')?.value || '',
                notes: document.querySelector('[name="notes"]')?.value || ''
            };
        },
        
        resetForm() {
            this.form = {
                courier_code: '',
                service_type: '',
                pickup_point: '',
                package: {
                    weight: 1,
                    length: 20,
                    width: 15,
                    height: 10
                },
                cod_enabled: false,
                cod_amount: 0,
                insurance_enabled: false,
                insurance_amount: 0
            };
            this.availableServices = [];
            this.pickupPoints = [];
            this.estimatedPrice = '';
            this.totalPrice = '';
            
            // Reset form fields
            document.querySelector('form').reset();
            
            // Scroll to top
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },

        // Bulk Recipients Methods
        showBulkRecipients() {
            if (!this.form.courier_code || !this.form.service_type) {
                alert('Proszę najpierw wybrać kuriera i usługę.');
                return;
            }
            this.showBulkModal = true;
            if (this.bulkRecipients.length === 0) {
                this.addBulkRecipient();
            }
        },

        closeBulkModal() {
            this.showBulkModal = false;
        },

        addBulkRecipient() {
            this.bulkRecipients.push({
                name: '',
                phone: '',
                email: '',
                address: '',
                city: '',
                postal_code: '',
                pickup_point: ''
            });
        },

        removeBulkRecipient(index) {
            if (confirm('Czy na pewno chcesz usunąć tego odbiorcę?')) {
                this.bulkRecipients.splice(index, 1);
            }
        },

        getSelectedCourierName() {
            const courierElement = document.querySelector(`input[name="courier_code"]:checked`);
            return courierElement ? courierElement.closest('label').querySelector('h4').textContent : '';
        },

        getSelectedServiceName() {
            const service = this.availableServices.find(s => s.code === this.form.service_type);
            return service ? service.name : '';
        },

        getBulkTotalPrice() {
            if (!this.estimatedPrice || this.bulkRecipients.length === 0) {
                return '0.00 PLN';
            }
            const basePrice = parseFloat(this.estimatedPrice.replace(' PLN', ''));
            const total = basePrice * this.bulkRecipients.length;
            return `${total.toFixed(2)} PLN`;
        },

        addBulkToCart() {
            if (this.bulkRecipients.length === 0) {
                alert('Dodaj co najmniej jednego odbiorcę.');
                return;
            }

            const invalidRecipients = this.bulkRecipients.filter(recipient => {
                if (!recipient.name || !recipient.phone) return true;
                if (this.needsPickupPoint && !recipient.pickup_point) return true;
                if (!this.needsPickupPoint && (!recipient.address || !recipient.city || !recipient.postal_code)) return true;
                return false;
            });

            if (invalidRecipients.length > 0) {
                alert('Proszę wypełnić wszystkie wymagane pola dla wszystkich odbiorców.');
                return;
            }

            const senderData = this.getSenderData();
            const optionsData = this.getOptionsData();
            let addedCount = 0;

            this.bulkRecipients.forEach((recipient, index) => {
                const cartItem = {
                    id: Date.now() + index,
                    courier_code: this.form.courier_code,
                    service_type: this.form.service_type,
                    sender: senderData,
                    recipient: {
                        name: recipient.name,
                        phone: recipient.phone,
                        email: recipient.email,
                        address: this.needsPickupPoint ? recipient.pickup_point : recipient.address,
                        city: recipient.city,
                        postal_code: recipient.postal_code,
                        pickup_point: this.needsPickupPoint ? recipient.pickup_point : null
                    },
                    package: { ...this.form.package },
                    options: optionsData,
                    estimated_price: this.totalPrice,
                    created_at: new Date().toISOString()
                };

                let cart = JSON.parse(localStorage.getItem('shipment_cart') || '[]');
                cart.push(cartItem);
                localStorage.setItem('shipment_cart', JSON.stringify(cart));
                addedCount++;
            });

            this.$dispatch('show-toast', {
                message: `${addedCount} przesyłek zostało dodanych do koszyka!`,
                type: 'success'
            });

            this.closeBulkModal();
            this.bulkRecipients = [];

            if (confirm(`Dodano ${addedCount} przesyłek do koszyka!\n\nCzy chcesz przejść do koszyka?`)) {
                window.location.href = '{{ route("customer.shipments.cart") }}';
            }
        },
        
        async loadCourierServices(courierCode) {
            try {
                const response = await fetch(`/customer/couriers/${courierCode}/services`);
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                const data = await response.json();
                this.availableServices = data.data || data.services || [];
                console.log('Loaded services:', this.availableServices);
            } catch (error) {
                console.error('Failed to load services:', error);
                this.availableServices = [];
            }
        },
        
        async searchPickupPoints() {
            if (!this.form.pickup_point || this.form.pickup_point.length < 3) {
                this.pickupPoints = [];
                return;
            }
            
            try {
                const response = await fetch(`/api/couriers/${this.form.courier_code}/pickup-points?city=${encodeURIComponent(this.form.pickup_point)}`);
                const data = await response.json();
                this.pickupPoints = data.data || [];
            } catch (error) {
                console.error('Failed to search pickup points:', error);
            }
        },
        
        selectPickupPoint(point) {
            this.form.pickup_point = point.name;
            this.pickupPoints = [];
        },
        
        async calculatePrice() {
            if (!this.form.courier_code || !this.form.service_type) return;
            
            try {
                const response = await fetch(`/api/couriers/${this.form.courier_code}/calculate-price`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        service_type: this.form.service_type,
                        package: this.form.package
                    })
                });
                
                const data = await response.json();
                if (data.success) {
                    const price = data.data.find(p => p.service_type === this.form.service_type);
                    if (price) {
                        this.estimatedPrice = `${price.price_gross} PLN`;
                        this.calculateTotal(price.price_gross);
                    }
                }
            } catch (error) {
                console.error('Failed to calculate price:', error);
            }
        },
        
        calculateTotal(basePrice) {
            let total = parseFloat(basePrice);
            
            if (this.form.cod_enabled && this.form.cod_amount > 0) {
                total += 3.00; // COD fee
            }
            
            if (this.form.insurance_enabled && this.form.insurance_amount > 0) {
                const insuranceCost = Math.max(2.00, this.form.insurance_amount * 0.01);
                this.insuranceFee = `${insuranceCost.toFixed(2)} PLN`;
                total += insuranceCost;
            }
            
            this.totalPrice = `${total.toFixed(2)} PLN`;
        }
    }));
});
</script>
@endpush
@endsection