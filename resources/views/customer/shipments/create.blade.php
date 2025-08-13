@extends('layouts.customer')

@section('title', 'Nowa przesyłka')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="shipmentForm">
    <!-- Header -->
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Nowa przesyłka</h2>
                <p class="mt-1 text-sm text-gray-500">Utwórz nową przesyłkę kurierską</p>
            </div>
            <a href="{{ route('customer.shipments.index') }}" 
               class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </a>
        </div>
    </div>

    <!-- Form -->
    <form method="POST" action="{{ route('customer.shipments.store') }}" class="space-y-6">
        @csrf
        
        <!-- Step 1: Courier Selection -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-600 text-white text-sm font-medium mr-3">1</span>
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
                           class="block p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-300 peer-checked:border-blue-600 peer-checked:bg-blue-50">
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
                                <h4 class="font-medium text-gray-900">{{ $courier->name }}</h4>
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
                                   class="block p-3 border border-gray-200 rounded-lg cursor-pointer hover:border-blue-300 peer-checked:border-blue-600 peer-checked:bg-blue-50">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <h5 class="font-medium text-gray-900" x-text="service.name"></h5>
                                        <p class="text-sm text-gray-500" x-text="service.description"></p>
                                    </div>
                                    <span class="text-sm font-medium text-blue-600" x-text="service.estimated_price"></span>
                                </div>
                            </label>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Step 2: Sender Data -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-600 text-white text-sm font-medium mr-3">2</span>
                Dane nadawcy
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="sender_name" class="block text-sm font-medium text-gray-700">Nazwa firmy/Imię i nazwisko</label>
                    <input type="text" 
                           name="sender[name]" 
                           id="sender_name"
                           value="{{ old('sender.name', auth()->user()->customer->company_name) }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
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
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
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
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
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
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
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
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
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
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                           required>
                    @error('sender.postal_code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Step 3: Recipient Data -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-600 text-white text-sm font-medium mr-3">3</span>
                Dane odbiorcy
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="recipient_name" class="block text-sm font-medium text-gray-700">Imię i nazwisko</label>
                    <input type="text" 
                           name="recipient[name]" 
                           id="recipient_name"
                           value="{{ old('recipient.name') }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
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
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
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
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
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
                               class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        
                        <!-- Pickup Points Dropdown -->
                        <div x-show="pickupPoints.length > 0" 
                             class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto">
                            <template x-for="point in pickupPoints" :key="point.id">
                                <div @click="selectPickupPoint(point)" 
                                     class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-50">
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
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
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
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
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
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                           :required="!needsPickupPoint">
                    @error('recipient.postal_code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Step 4: Package Data -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-600 text-white text-sm font-medium mr-3">4</span>
                Dane paczki
            </h3>
            
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
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
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
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
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
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
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
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                           required>
                    @error('package.height')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Step 5: Additional Services -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-600 text-white text-sm font-medium mr-3">5</span>
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
                               class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
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
                           class="mt-1 block w-full max-w-xs border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Insurance -->
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input type="checkbox" 
                               name="insurance_enabled" 
                               id="insurance_enabled"
                               x-model="form.insurance_enabled"
                               class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
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
                           class="mt-1 block w-full max-w-xs border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
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
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
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
                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Price Summary -->
        <div x-show="estimatedPrice" x-transition class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Podsumowanie kosztów</h3>
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

        <!-- Submit Buttons -->
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center justify-between">
                <a href="{{ route('customer.shipments.index') }}" 
                   class="bg-white border border-gray-300 rounded-md px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Anuluj
                </a>
                
                <div class="flex space-x-3">
                    <button type="submit" 
                            name="save_draft"
                            value="1"
                            class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                        Zapisz jako szkic
                    </button>
                    <button type="submit" 
                            :disabled="!canSubmit"
                            class="bg-blue-600 hover:bg-blue-700 disabled:bg-gray-300 text-white px-4 py-2 rounded-md text-sm font-medium"
                            :class="{ 'opacity-50 cursor-not-allowed': !canSubmit }">
                        Utwórz przesyłkę
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
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
            insurance_amount: 0
        },
        availableServices: [],
        pickupPoints: [],
        estimatedPrice: '',
        totalPrice: '',
        insuranceFee: '0.00 PLN',
        
        get needsPickupPoint() {
            return this.form.service_type && this.form.service_type.includes('locker');
        },
        
        get canSubmit() {
            return this.form.courier_code && this.form.service_type && 
                   (!this.needsPickupPoint || this.form.pickup_point);
        },
        
        async loadCourierServices(courierCode) {
            try {
                const response = await fetch(`/api/couriers/${courierCode}/services`);
                const data = await response.json();
                this.availableServices = data.data || [];
            } catch (error) {
                console.error('Failed to load services:', error);
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