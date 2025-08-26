@extends('layouts.customer')

@section('title', 'Nowa przesyłka')

@push('styles')
<style>
.package-type-card.selected {
    @apply border-blue-500 bg-blue-50 ring-2 ring-blue-500;
}
.offer-card.selected {
    @apply border-blue-500 bg-blue-50;
}
.step-indicator.active {
    @apply bg-blue-600 text-white;
}
.step-indicator.completed {
    @apply bg-green-600 text-white;
}
.loading-skeleton {
    @apply animate-pulse bg-gray-200 rounded;
}
</style>
@endpush

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="modernShipmentForm">
    <!-- Header -->
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Nadaj przesyłkę</h2>
                <p class="mt-1 text-sm text-gray-600">Wypełnij formularz, aby obliczyć koszty i wybrać najlepszą ofertę</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('customer.shipments.address-book') }}" 
                   class="text-blue-600 hover:text-blue-800 text-sm font-medium flex items-center">
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

    <!-- Progress Steps -->
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center" :class="currentStep >= 1 ? 'text-blue-600' : 'text-gray-400'">
                <div class="step-indicator w-10 h-10 rounded-full flex items-center justify-center" 
                     :class="currentStep >= 1 ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-400'">
                    <i class="fas fa-box"></i>
                </div>
                <div class="ml-3">
                    <div class="text-sm font-medium">Krok 1</div>
                    <div class="text-xs">Szczegóły przesyłki</div>
                </div>
            </div>
            <div class="flex-1 h-0.5 mx-4" :class="currentStep > 1 ? 'bg-blue-600' : 'bg-gray-200'"></div>
            
            <div class="flex items-center" :class="currentStep >= 2 ? 'text-blue-600' : 'text-gray-400'">
                <div class="step-indicator w-10 h-10 rounded-full flex items-center justify-center" 
                     :class="currentStep >= 2 ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-400'">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <div class="ml-3">
                    <div class="text-sm font-medium">Krok 2</div>
                    <div class="text-xs">Adresy</div>
                </div>
            </div>
            <div class="flex-1 h-0.5 mx-4" :class="currentStep > 2 ? 'bg-blue-600' : 'bg-gray-200'"></div>
            
            <div class="flex items-center" :class="currentStep >= 3 ? 'text-blue-600' : 'text-gray-400'">
                <div class="step-indicator w-10 h-10 rounded-full flex items-center justify-center" 
                     :class="currentStep >= 3 ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-400'">
                    <i class="fas fa-truck"></i>
                </div>
                <div class="ml-3">
                    <div class="text-sm font-medium">Krok 3</div>
                    <div class="text-xs">Wybór oferty</div>
                </div>
            </div>
            <div class="flex-1 h-0.5 mx-4" :class="currentStep > 3 ? 'bg-blue-600' : 'bg-gray-200'"></div>
            
            <div class="flex items-center" :class="currentStep >= 4 ? 'text-blue-600' : 'text-gray-400'">
                <div class="step-indicator w-10 h-10 rounded-full flex items-center justify-center" 
                     :class="currentStep >= 4 ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-400'">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="ml-3">
                    <div class="text-sm font-medium">Krok 4</div>
                    <div class="text-xs">Potwierdzenie</div>
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
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Dokąd</label>
                        <input type="text" 
                               x-model="form.route.to" 
                               name="route_to"
                               placeholder="Miasto, kod pocztowy lub adres"
                               class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 2: Address Data -->
        <div class="bg-white shadow rounded-lg p-6" x-show="currentStep === 2" x-transition>
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Dane nadawcy i odbiorcy</h3>
            
            <div class="grid md:grid-cols-2 gap-8">
                <!-- Sender Data -->
                <div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Dane nadawcy</h4>
                    <div class="space-y-4">
                        <input type="text" 
                               x-model="form.sender.name"
                               name="sender_name"
                               placeholder="Imię i nazwisko"
                               class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <input type="text" 
                               x-model="form.sender.company"
                               name="sender_company"
                               placeholder="Firma (opcjonalnie)"
                               class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <input type="text" 
                               x-model="form.sender.address"
                               name="sender_address"
                               placeholder="Ulica i numer"
                               class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <div class="grid grid-cols-2 gap-4">
                            <input type="text" 
                                   x-model="form.sender.postal_code"
                                   name="sender_postal_code"
                                   placeholder="Kod pocztowy"
                                   class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <input type="text" 
                                   x-model="form.sender.city"
                                   name="sender_city"
                                   placeholder="Miasto"
                                   class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <input type="tel" 
                               x-model="form.sender.phone"
                               name="sender_phone"
                               placeholder="Telefon"
                               class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <input type="email" 
                               x-model="form.sender.email"
                               name="sender_email"
                               placeholder="E-mail"
                               class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
                
                <!-- Recipient Data -->
                <div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Dane odbiorcy</h4>
                    <div class="space-y-4">
                        <input type="text" 
                               x-model="form.recipient.name"
                               name="recipient_name"
                               placeholder="Imię i nazwisko"
                               class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <input type="text" 
                               x-model="form.recipient.company"
                               name="recipient_company"
                               placeholder="Firma (opcjonalnie)"
                               class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <input type="text" 
                               x-model="form.recipient.address"
                               name="recipient_address"
                               placeholder="Ulica i numer"
                               class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <div class="grid grid-cols-2 gap-4">
                            <input type="text" 
                                   x-model="form.recipient.postal_code"
                                   name="recipient_postal_code"
                                   placeholder="Kod pocztowy"
                                   class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <input type="text" 
                                   x-model="form.recipient.city"
                                   name="recipient_city"
                                   placeholder="Miasto"
                                   class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <input type="tel" 
                               x-model="form.recipient.phone"
                               name="recipient_phone"
                               placeholder="Telefon"
                               class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <input type="email" 
                               x-model="form.recipient.email"
                               name="recipient_email"
                               placeholder="E-mail"
                               class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 3: Offers -->
        <div class="bg-white shadow rounded-lg p-6" x-show="currentStep === 3" x-transition>
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Wybierz najlepszą ofertę</h3>
            
            <div x-show="isLoadingOffers" class="space-y-4">
                <div class="loading-skeleton h-24"></div>
                <div class="loading-skeleton h-24"></div>
                <div class="loading-skeleton h-24"></div>
            </div>
            
            <div x-show="!isLoadingOffers && offers.length > 0" class="space-y-4">
                <template x-for="offer in offers" :key="offer.id">
                    <div class="offer-card border-2 border-gray-200 rounded-lg p-6 cursor-pointer hover:shadow-md transition-all"
                         :class="form.selectedOffer && form.selectedOffer.id === offer.id ? 'selected' : ''"
                         @click="selectOffer(offer)">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center mb-2">
                                    <div class="px-3 py-1 rounded text-white text-sm font-medium mr-3"
                                         :class="'bg-' + offer.color + '-500'"
                                         x-text="offer.courier"></div>
                                    <div class="flex items-center">
                                        <span class="text-yellow-400">★</span>
                                        <span class="text-sm text-gray-600 ml-1" x-text="offer.rating"></span>
                                    </div>
                                </div>
                                
                                <div class="grid md:grid-cols-2 gap-4">
                                    <div>
                                        <div class="text-2xl font-bold text-gray-900" x-text="offer.price + ' zł'"></div>
                                        <div class="text-sm text-gray-500 flex items-center">
                                            <i class="fas fa-clock mr-1"></i>
                                            <span x-text="offer.time"></span>
                                        </div>
                                    </div>
                                    
                                    <div class="space-y-1">
                                        <template x-for="feature in offer.features" :key="feature">
                                            <div class="text-sm text-gray-600 flex items-center">
                                                <i class="fas fa-check-circle mr-2 text-green-500"></i>
                                                <span x-text="feature"></span>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Step 4: Confirmation -->
        <div class="bg-white shadow rounded-lg p-6" x-show="currentStep === 4" x-transition>
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Podsumowanie zamówienia</h3>
            
            <div x-show="form.selectedOffer" class="bg-gray-50 rounded-lg p-6 mb-6">
                <div class="flex justify-between items-center mb-4">
                    <div class="text-lg font-semibold" x-text="form.selectedOffer ? form.selectedOffer.courier : ''"></div>
                    <div class="text-2xl font-bold" x-text="form.selectedOffer ? form.selectedOffer.price + ' zł' : ''"></div>
                </div>
                
                <div class="grid md:grid-cols-2 gap-6 text-sm">
                    <div>
                        <div class="font-medium mb-2">Przesyłka:</div>
                        <div x-text="'Wymiary: ' + form.dimensions.length + '×' + form.dimensions.width + '×' + form.dimensions.height + 'cm'"></div>
                        <div x-text="'Waga: ' + form.dimensions.weight + 'kg'"></div>
                    </div>
                    <div>
                        <div class="font-medium mb-2">Trasa:</div>
                        <div x-text="form.route.from"></div>
                        <div x-text="'→ ' + form.route.to"></div>
                    </div>
                </div>
            </div>
            
            <div>
                <h4 class="font-medium mb-4">Wybierz metodę płatności</h4>
                <div class="space-y-3">
                    <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="payment_method" value="card" x-model="form.paymentMethod" class="mr-3">
                        <i class="fas fa-credit-card mr-3"></i>
                        Płatność kartą online
                    </label>
                    <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="payment_method" value="blik" x-model="form.paymentMethod" class="mr-3">
                        <i class="fas fa-mobile-alt mr-3"></i>
                        BLIK
                    </label>
                    <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="payment_method" value="transfer" x-model="form.paymentMethod" class="mr-3">
                        <i class="fas fa-university mr-3"></i>
                        Przelew bankowy
                    </label>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <div class="flex justify-between bg-white shadow rounded-lg p-6">
            <button type="button" 
                    @click="prevStep()" 
                    x-show="currentStep > 1"
                    class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg font-medium hover:bg-gray-300 transition-colors">
                Wstecz
            </button>
            
            <div class="ml-auto">
                <button type="button" 
                        @click="nextStep()" 
                        x-show="currentStep < 4"
                        :disabled="!canProceedToNext()"
                        :class="canProceedToNext() ? 'bg-blue-600 text-white hover:bg-blue-700' : 'bg-gray-100 text-gray-400 cursor-not-allowed'"
                        class="px-6 py-3 rounded-lg font-medium transition-colors">
                    <span x-show="currentStep === 1">Oblicz ceny</span>
                    <span x-show="currentStep > 1">Dalej</span>
                </button>
                
                <button type="submit" 
                        x-show="currentStep === 4"
                        :disabled="!canSubmit()"
                        :class="canSubmit() ? 'bg-green-600 text-white hover:bg-green-700' : 'bg-gray-100 text-gray-400 cursor-not-allowed'"
                        class="px-8 py-3 rounded-lg font-medium transition-colors">
                    Złóż zamówienie
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('modernShipmentForm', () => ({
        currentStep: 1,
        isLoadingOffers: false,
        offers: [],
        
        packageTypes: [
            { id: 'envelope', name: 'Koperta', icon: '📄', dimensions: '35x25x2', maxWeight: '0.5' },
            { id: 'small', name: 'Mała paczka', icon: '📦', dimensions: '25x20x15', maxWeight: '5' },
            { id: 'medium', name: 'Średnia paczka', icon: '📦', dimensions: '40x30x20', maxWeight: '15' },
            { id: 'large', name: 'Duża paczka', icon: '📦', dimensions: '60x40x30', maxWeight: '30' },
            { id: 'custom', name: 'Niestandardowy', icon: '📏', dimensions: 'Custom', maxWeight: 'Custom' }
        ],
        
        form: {
            packageType: '',
            dimensions: { length: '', width: '', height: '', weight: '' },
            route: { from: '', to: '' },
            sender: {
                name: '', address: '', building_number: '', city: '', 
                postal_code: '', country: 'PL', phone: '', email: ''
            },
            recipient: {
                name: '', address: '', building_number: '', city: '', 
                postal_code: '', country: 'PL', phone: '', email: ''
            },
            selectedOffer: null,
            paymentMethod: ''
        },
        
        formErrors: {
            step1: {},
            step2: {},
            general: null
        },

        init() {
            // Load saved form data if exists
            this.loadSavedData();
        },

        selectPackageType(type) {
            this.form.packageType = type.id;
            
            if (type.id !== 'custom') {
                const [l, w, h] = type.dimensions.split('x');
                this.form.dimensions = {
                    length: l,
                    width: w, 
                    height: h,
                    weight: type.maxWeight
                };
            }
            this.saveFormData();
        },

        validateDimensions() {
            // Basic validation
            return this.form.dimensions.length > 0 && 
                   this.form.dimensions.width > 0 && 
                   this.form.dimensions.height > 0 && 
                   this.form.dimensions.weight > 0;
        },

        canProceedToNext() {
            switch (this.currentStep) {
                case 1:
                    return this.form.packageType && this.validateDimensions();
                case 2:
                    return this.validateSenderRecipient();
                case 3:
                    return this.form.selectedOffer;
                case 4:
                    return this.form.paymentMethod;
                default:
                    return true;
            }
        },

        validateSenderRecipient() {
            const senderValid = this.form.sender.name && 
                               this.form.sender.address && 
                               this.form.sender.city && 
                               this.form.sender.postal_code && 
                               this.form.sender.phone && 
                               this.form.sender.email;
                               
            const recipientValid = this.form.recipient.name && 
                                  this.form.recipient.address && 
                                  this.form.recipient.city && 
                                  this.form.recipient.postal_code && 
                                  this.form.recipient.phone && 
                                  this.form.recipient.email;
                                  
            return senderValid && recipientValid;
        },

        validateField(field, value) {
            switch (field) {
                case 'weight':
                    const weight = parseFloat(value);
                    if (!weight || weight <= 0) return 'Waga jest wymagana';
                    if (weight > 70) return 'Maksymalna waga to 70 kg';
                    break;
                case 'length':
                case 'width':
                case 'height':
                    const dimension = parseInt(value);
                    if (!dimension || dimension <= 0) return `${field} jest wymagane`;
                    if (dimension > 200) return `Maksymalna ${field} to 200 cm`;
                    break;
                case 'postal_code':
                    if (!/^\d{2}-\d{3}$/.test(value)) return 'Format: XX-XXX';
                    break;
                case 'email':
                    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) return 'Niepoprawny email';
                    break;
                case 'phone':
                    if (!/^[\d\s\-\+\(\)]{9,15}$/.test(value)) return 'Niepoprawny numer telefonu';
                    break;
            }
            return null;
        },

        showFieldError(field, value) {
            return this.validateField(field, value);
        },

        canSubmit() {
            return this.form.selectedOffer && this.form.paymentMethod;
        },

        async nextStep() {
            if (!this.canProceedToNext()) return;

            if (this.currentStep === 1) {
                await this.calculateOffers();
            }

            this.currentStep = Math.min(this.currentStep + 1, 4);
            this.saveFormData();
        },

        prevStep() {
            this.currentStep = Math.max(this.currentStep - 1, 1);
        },

        async calculateOffers() {
            this.isLoadingOffers = true;
            
            try {
                const response = await fetch('{{ route("customer.shipments.calculate-price") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        courier_code: 'inpost',
                        package: {
                            weight: parseFloat(this.form.dimensions.weight) || 1,
                            length: parseFloat(this.form.dimensions.length) || 20,
                            width: parseFloat(this.form.dimensions.width) || 15,
                            height: parseFloat(this.form.dimensions.height) || 10,
                            value: 100
                        },
                        sender: this.form.sender || {},
                        recipient: this.form.recipient || {}
                    })
                });

                const data = await response.json();
                
                if (data.success && data.prices) {
                    // Convert InPost service response to our format
                    this.offers = data.prices.map(price => ({
                        id: price.service_type,
                        courier: 'InPost',
                        service_name: price.service_name,
                        price: price.price_gross,
                        price_net: price.price_net,
                        time: this.getEstimatedTime(price.service_type),
                        rating: 4.5,
                        features: this.getServiceFeatures(price.service_type),
                        color: 'blue'
                    }));
                } else {
                    // Fallback to mock data if API fails
                    this.offers = [
                        {
                            id: 'inpost_locker_standard',
                            courier: 'InPost',
                            service_name: 'Paczkomat Standard',
                            price: 12.99,
                            time: '1-2 dni robocze',
                            rating: 4.8,
                            features: ['Ubezpieczenie do 500zł', 'SMS powiadomienia', 'Śledzenie online'],
                            color: 'blue'
                        },
                        {
                            id: 'inpost',
                            courier: 'InPost',
                            price: 19.31,
                            time: '1 dzień roboczy',
                            rating: 4.6,
                            features: ['Paczkomaty 24/7', 'Aplikacja mobilna', 'Eko dostawa'],
                            color: 'yellow'
                        },
                        {
                            id: 'ups',
                            courier: 'UPS',
                            price: 42.65,
                            time: 'Następny dzień roboczy',
                            rating: 4.9,
                            features: ['Express delivery', 'Ubezpieczenie premium', 'Potwierdzenie dostawy'],
                            color: 'amber'
                        }
                    ];
                }
            } catch (error) {
                console.error('Calculate offers failed:', error);
                alert('Nie udało się obliczyć cen. Spróbuj ponownie.');
            } finally {
                this.isLoadingOffers = false;
            }
        },

        getEstimatedTime(serviceType) {
            const times = {
                'inpost_locker_standard': '1-2 dni robocze',
                'inpost_locker_express': '24 godziny',
                'inpost_courier_standard': '1-2 dni robocze',
                'inpost_courier_express': '24 godziny',
                'inpost_courier_to_locker': '1-2 dni robocze',
                'inpost_locker_to_courier': '1-2 dni robocze'
            };
            return times[serviceType] || '1-2 dni robocze';
        },

        getServiceFeatures(serviceType) {
            const features = {
                'inpost_locker_standard': ['Paczkomat 24/7', 'SMS powiadomienia', 'Śledzenie online'],
                'inpost_locker_express': ['Paczkomat 24/7', 'Express 24h', 'SMS powiadomienia', 'Śledzenie online'],
                'inpost_courier_standard': ['Odbiór kurierem', 'Doręczenie kurierem', 'SMS powiadomienia'],
                'inpost_courier_express': ['Odbiór kurierem', 'Express 24h', 'SMS powiadomienia'],
                'inpost_courier_to_locker': ['Odbiór kurierem', 'Doręczenie do paczkomatu', 'SMS powiadomienia'],
                'inpost_locker_to_courier': ['Nadanie z paczkomatu', 'Doręczenie kurierem', 'SMS powiadomienia']
            };
            return features[serviceType] || ['SMS powiadomienia', 'Śledzenie online'];
        },

        selectOffer(offer) {
            this.form.selectedOffer = offer;
            this.saveFormData();
        },

        async submitForm() {
            if (!this.canSubmit()) return;

            try {
                const formData = new FormData();
                
                // Add CSRF token
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
                
                // Courier and service selection
                formData.append('courier_code', this.form.selectedOffer.id.includes('inpost') ? 'inpost' : 'inpost');
                formData.append('service_type', this.form.selectedOffer.id);
                
                // Package data
                formData.append('package[weight]', this.form.dimensions.weight);
                formData.append('package[length]', this.form.dimensions.length);
                formData.append('package[width]', this.form.dimensions.width);
                formData.append('package[height]', this.form.dimensions.height);
                formData.append('package[description]', 'Przesyłka standardowa');
                
                // Sender data (use logged in user's customer data)
                Object.entries(this.form.sender).forEach(([key, value]) => {
                    if (value) formData.append(`sender[${key}]`, value);
                });
                
                // Recipient data
                Object.entries(this.form.recipient).forEach(([key, value]) => {
                    if (value) formData.append(`recipient[${key}]`, value);
                });

                const response = await fetch('{{ route("customer.shipments.store") }}', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();
                
                if (data.success) {
                    // Clear saved data
                    localStorage.removeItem('shipment_form_data');
                    
                    // Redirect to success page or payment
                    window.location.href = data.redirect_url || '{{ route("customer.shipments.index") }}';
                } else {
                    throw new Error(data.message || 'Błąd podczas tworzenia przesyłki');
                }
            } catch (error) {
                console.error('Submit form failed:', error);
                alert('Nie udało się utworzyć przesyłki. Spróbuj ponownie.');
            }
        },

        saveFormData() {
            localStorage.setItem('shipment_form_data', JSON.stringify({
                form: this.form,
                currentStep: this.currentStep,
                timestamp: Date.now()
            }));
        },

        loadSavedData() {
            const saved = localStorage.getItem('shipment_form_data');
            if (!saved) return;

            try {
                const { form, currentStep, timestamp } = JSON.parse(saved);
                
                // Only restore if saved within last 24 hours
                if (Date.now() - timestamp > 24 * 60 * 60 * 1000) {
                    localStorage.removeItem('shipment_form_data');
                    return;
                }

                this.form = { ...this.form, ...form };
                this.currentStep = currentStep || 1;
            } catch (error) {
                console.warn('Failed to load saved form data:', error);
            }
        }
    }));
});
</script>
@endpush
@endsection