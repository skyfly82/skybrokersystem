@extends('layouts.customer')

@section('title', 'Edycja przesyłki')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Breadcrumb -->
    <div class="mb-6">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-2 text-sm text-gray-500">
                <li><a href="{{ route('customer.dashboard') }}" class="hover:text-gray-700">Dashboard</a></li>
                <li><i class="fas fa-chevron-right text-xs"></i></li>
                <li><a href="{{ route('customer.shipments.index') }}" class="hover:text-gray-700">Przesyłki</a></li>
                <li><i class="fas fa-chevron-right text-xs"></i></li>
                <li><a href="{{ route('customer.shipments.show', $shipment) }}" class="hover:text-gray-700">{{ $shipment->tracking_number ?? 'Przesyłka #'.$shipment->id }}</a></li>
                <li><i class="fas fa-chevron-right text-xs"></i></li>
                <li class="text-gray-900">Edycja</li>
            </ol>
        </nav>
    </div>

    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm border p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Edycja przesyłki</h1>
                <p class="mt-1 text-sm text-gray-600">
                    {{ $shipment->tracking_number ?? 'Przesyłka #'.$shipment->id }} • Status: 
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                        {{ $shipment->status_label }}
                    </span>
                </p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('customer.shipments.show', $shipment) }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <i class="fas fa-times mr-2"></i>
                    Anuluj
                </a>
            </div>
        </div>
    </div>

    <!-- Edit Form -->
    <form method="POST" action="{{ route('customer.shipments.update', $shipment) }}">
        @csrf
        @method('PUT')

        <div class="space-y-6">
            <!-- Sender Information -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-paper-plane text-green-600 mr-3"></i>
                    Dane nadawcy
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Imię i nazwisko *</label>
                        <input type="text" name="sender[name]" 
                               value="{{ old('sender.name', $shipment->sender_data['name'] ?? '') }}"
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               required>
                        @error('sender.name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nazwa firmy</label>
                        <input type="text" name="sender[company]" 
                               value="{{ old('sender.company', $shipment->sender_data['company'] ?? '') }}"
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Adres *</label>
                        <input type="text" name="sender[address]" 
                               value="{{ old('sender.address', $shipment->sender_data['address'] ?? '') }}"
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               required>
                        @error('sender.address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kod pocztowy *</label>
                        <input type="text" name="sender[postal_code]" 
                               value="{{ old('sender.postal_code', $shipment->sender_data['postal_code'] ?? '') }}"
                               pattern="[0-9]{2}-[0-9]{3}"
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               required>
                        @error('sender.postal_code')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Miasto *</label>
                        <input type="text" name="sender[city]" 
                               value="{{ old('sender.city', $shipment->sender_data['city'] ?? '') }}"
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               required>
                        @error('sender.city')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Telefon *</label>
                        <input type="tel" name="sender[phone]" 
                               value="{{ old('sender.phone', $shipment->sender_data['phone'] ?? '') }}"
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               required>
                        @error('sender.phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                        <input type="email" name="sender[email]" 
                               value="{{ old('sender.email', $shipment->sender_data['email'] ?? '') }}"
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               required>
                        @error('sender.email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Recipient Information -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-map-marker-alt text-blue-600 mr-3"></i>
                    Dane odbiorcy
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Imię i nazwisko *</label>
                        <input type="text" name="recipient[name]" 
                               value="{{ old('recipient.name', $shipment->recipient_data['name'] ?? '') }}"
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               required>
                        @error('recipient.name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nazwa firmy</label>
                        <input type="text" name="recipient[company]" 
                               value="{{ old('recipient.company', $shipment->recipient_data['company'] ?? '') }}"
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Adres *</label>
                        <input type="text" name="recipient[address]" 
                               value="{{ old('recipient.address', $shipment->recipient_data['address'] ?? '') }}"
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               required>
                        @error('recipient.address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kod pocztowy *</label>
                        <input type="text" name="recipient[postal_code]" 
                               value="{{ old('recipient.postal_code', $shipment->recipient_data['postal_code'] ?? '') }}"
                               pattern="[0-9]{2}-[0-9]{3}"
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               required>
                        @error('recipient.postal_code')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Miasto *</label>
                        <input type="text" name="recipient[city]" 
                               value="{{ old('recipient.city', $shipment->recipient_data['city'] ?? '') }}"
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               required>
                        @error('recipient.city')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Telefon *</label>
                        <input type="tel" name="recipient[phone]" 
                               value="{{ old('recipient.phone', $shipment->recipient_data['phone'] ?? '') }}"
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               required>
                        @error('recipient.phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                        <input type="email" name="recipient[email]" 
                               value="{{ old('recipient.email', $shipment->recipient_data['email'] ?? '') }}"
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               required>
                        @error('recipient.email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Package Information -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-box text-purple-600 mr-3"></i>
                    Informacje o paczce
                </h2>
                
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Długość (cm) *</label>
                        <input type="number" name="package[length]" 
                               value="{{ old('package.length', $shipment->package_data['length'] ?? '') }}"
                               min="1" max="300"
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               required>
                        @error('package.length')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Szerokość (cm) *</label>
                        <input type="number" name="package[width]" 
                               value="{{ old('package.width', $shipment->package_data['width'] ?? '') }}"
                               min="1" max="300"
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               required>
                        @error('package.width')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Wysokość (cm) *</label>
                        <input type="number" name="package[height]" 
                               value="{{ old('package.height', $shipment->package_data['height'] ?? '') }}"
                               min="1" max="300"
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               required>
                        @error('package.height')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Waga (kg) *</label>
                        <input type="number" name="package[weight]" 
                               value="{{ old('package.weight', $shipment->package_data['weight'] ?? '') }}"
                               min="0.1" max="1000" step="0.1"
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               required>
                        @error('package.weight')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Opis zawartości</label>
                    <input type="text" name="package[description]" 
                           value="{{ old('package.description', $shipment->package_data['description'] ?? '') }}"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="np. Książki, odzież, elektronika">
                </div>
            </div>

            <!-- Additional Services -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-plus-circle text-orange-600 mr-3"></i>
                    Usługi dodatkowe
                </h2>
                
                <div class="space-y-4">
                    <div class="flex items-start space-x-3">
                        <div class="flex items-center h-5">
                            <input type="checkbox" name="cod_enabled" id="cod_enabled" 
                                   {{ ($shipment->cod_amount ?? 0) > 0 ? 'checked' : '' }}
                                   class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded"
                                   onchange="toggleCodAmount()">
                        </div>
                        <div class="flex-1">
                            <label for="cod_enabled" class="text-sm font-medium text-gray-700">
                                Pobranie (COD)
                            </label>
                            <p class="text-sm text-gray-500">Odbierz płatność przy dostawie</p>
                            <div id="cod_amount_field" class="mt-2 {{ ($shipment->cod_amount ?? 0) > 0 ? '' : 'hidden' }}">
                                <input type="number" name="cod_amount" 
                                       value="{{ old('cod_amount', $shipment->cod_amount ?? '') }}"
                                       min="1" max="10000" step="0.01"
                                       class="w-32 border border-gray-300 rounded-md px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       placeholder="Kwota PLN">
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-3">
                        <div class="flex items-center h-5">
                            <input type="checkbox" name="insurance_enabled" id="insurance_enabled"
                                   {{ ($shipment->insurance_amount ?? 0) > 0 ? 'checked' : '' }}
                                   class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded"
                                   onchange="toggleInsuranceAmount()">
                        </div>
                        <div class="flex-1">
                            <label for="insurance_enabled" class="text-sm font-medium text-gray-700">
                                Ubezpieczenie
                            </label>
                            <p class="text-sm text-gray-500">Dodatkowa ochrona przesyłki</p>
                            <div id="insurance_amount_field" class="mt-2 {{ ($shipment->insurance_amount ?? 0) > 0 ? '' : 'hidden' }}">
                                <input type="number" name="insurance_amount" 
                                       value="{{ old('insurance_amount', $shipment->insurance_amount ?? '') }}"
                                       min="100" max="50000" step="0.01"
                                       class="w-32 border border-gray-300 rounded-md px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       placeholder="Wartość PLN">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-sticky-note text-yellow-600 mr-3"></i>
                    Uwagi dodatkowe
                </h2>
                
                <textarea name="notes" rows="3"
                          class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                          placeholder="Dodatkowe informacje dla kuriera...">{{ old('notes', $shipment->notes ?? '') }}</textarea>
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-between bg-white rounded-lg shadow-sm border p-6">
                <a href="{{ route('customer.shipments.show', $shipment) }}" 
                   class="text-gray-500 hover:text-gray-700 font-medium">
                    ← Anuluj i wróć
                </a>
                
                <button type="submit" 
                        class="inline-flex items-center px-6 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-save mr-2"></i>
                    Zapisz zmiany
                </button>
            </div>
        </div>
    </form>
</div>

<script>
function toggleCodAmount() {
    const checkbox = document.getElementById('cod_enabled');
    const field = document.getElementById('cod_amount_field');
    const input = field.querySelector('input');
    
    if (checkbox.checked) {
        field.classList.remove('hidden');
        input.required = true;
    } else {
        field.classList.add('hidden');
        input.required = false;
        input.value = '';
    }
}

function toggleInsuranceAmount() {
    const checkbox = document.getElementById('insurance_enabled');
    const field = document.getElementById('insurance_amount_field');
    const input = field.querySelector('input');
    
    if (checkbox.checked) {
        field.classList.remove('hidden');
        input.required = true;
    } else {
        field.classList.add('hidden');
        input.required = false;
        input.value = '';
    }
}
</script>
@endsection