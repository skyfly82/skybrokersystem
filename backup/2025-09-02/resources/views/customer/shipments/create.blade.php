@extends('layouts.customer')

@section('title', 'Nowa przesyłka')

@push('styles')
<style>
/* Modern package card animations */
.package-card {
    @apply transition-all duration-300 ease-out transform hover:scale-105 cursor-pointer relative;
}

.package-card.selected {
    @apply border-blue-500 bg-gradient-to-br from-blue-50 to-blue-100 ring-4 ring-blue-200 ring-opacity-50 scale-105 shadow-lg;
    border-color: #2563eb !important;
    background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 50%, #93c5fd 100%) !important;
    box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.3), 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
    transform: scale(1.08) !important;
    border-width: 3px !important;
}

.package-card.selected::before {
    content: '✓';
    position: absolute;
    top: 10px;
    right: 10px;
    background: #2563eb;
    color: white;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    font-weight: bold;
    z-index: 10;
}

.package-card:hover {
    @apply shadow-xl border-gray-300;
}

/* Offer cards */
.offer-card {
    @apply transition-all duration-300 cursor-pointer hover:shadow-lg transform hover:-translate-y-1 relative;
}

.offer-card.selected {
    @apply border-blue-500 bg-blue-50 ring-2 ring-blue-400 transform scale-105 shadow-xl;
    border-color: #3b82f6 !important;
    background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%) !important;
    box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.5), 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important;
    transform: scale(1.05) !important;
    border-width: 3px !important;
}

.offer-card.selected::after {
    content: '✓';
    position: absolute;
    top: 15px;
    right: 15px;
    background: #2563eb;
    color: white;
    border-radius: 50%;
    width: 28px;
    height: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    font-weight: bold;
    z-index: 10;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

/* Service cards */
.service-card {
    @apply transition-all duration-200 cursor-pointer border-2 rounded-lg p-4;
}

.service-card.selected {
    @apply border-blue-500 bg-blue-50 ring-2 ring-blue-300;
    border-color: #2563eb !important;
    background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%) !important;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.4) !important;
    transform: scale(1.02) !important;
    border-width: 2px !important;
}

.service-card.selected .custom-checkbox {
    background-color: #2563eb !important;
    border-color: #2563eb !important;
}

/* Step indicators */
.step-circle {
    @apply w-12 h-12 rounded-full border-4 flex items-center justify-center font-bold text-sm transition-all duration-500;
}

.step-circle.active {
    @apply bg-blue-600 border-blue-600 text-white shadow-lg transform scale-110;
}

.step-circle.completed {
    @apply bg-green-600 border-green-600 text-white;
}

.step-circle.pending {
    @apply bg-gray-200 border-gray-300 text-gray-500;
}

/* Progress bar */
.progress-bar {
    @apply h-2 bg-gray-200 rounded-full overflow-hidden;
}

.progress-fill {
    @apply h-full bg-gradient-to-r from-blue-500 to-blue-600 transition-all duration-700 ease-out;
}

/* Cart animations */
.cart-item {
    @apply transform transition-all duration-300 hover:scale-102;
}

.cart-badge {
    @apply absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-6 h-6 flex items-center justify-center font-bold animate-pulse;
}

/* Loading skeleton */
.loading-skeleton {
    @apply animate-pulse bg-gradient-to-r from-gray-200 via-gray-300 to-gray-200 bg-[length:200%_100%];
    animation: shimmer 2s infinite;
}

@keyframes shimmer {
    0% { background-position: -200% 0; }
    100% { background-position: 200% 0; }
}

/* Floating action button */
.fab {
    @apply fixed bottom-6 right-6 bg-blue-600 hover:bg-blue-700 text-white p-4 rounded-full shadow-2xl transition-all duration-300 transform hover:scale-110 z-50;
}

/* Modern form inputs */
.modern-input {
    @apply w-full p-4 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all duration-200 hover:border-gray-300;
}

.modern-input:focus {
    @apply transform scale-105;
}

/* Selection animation */
.selected {
    animation: selectPulse 0.3s ease-out;
}

@keyframes selectPulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.02); }
    100% { transform: scale(1.05); }
}

/* Custom checkbox/radio */
.custom-checkbox {
    @apply w-5 h-5 text-blue-600 border-2 border-gray-300 rounded focus:ring-blue-500 focus:ring-2;
}

/* Notification toast */
.toast {
    @apply fixed top-4 right-4 bg-white shadow-xl border border-gray-200 rounded-lg p-4 z-50 transform transition-all duration-300;
}

/* Success animations */
@keyframes success-bounce {
    0%, 20%, 60%, 100% { transform: translateY(0); }
    40% { transform: translateY(-10px); }
    80% { transform: translateY(-5px); }
}

.success-animation {
    animation: success-bounce 0.6s ease-in-out;
}

/* Hide elements with x-cloak until Alpine.js loads */
[x-cloak] {
    display: none !important;
}
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100" x-data="modernShipmentManager">
    <!-- Fixed Header with Progress -->
    <div class="sticky top-0 z-40 bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center space-x-4">
                    <button @click="goBack()" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                        <i class="fas fa-arrow-left text-gray-600"></i>
                    </button>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">Nadawanie przesyłek</h1>
                        <p class="text-sm text-gray-500" x-text="`Krok ${currentStep} z 4`"></p>
                    </div>
                </div>
                
                <!-- Cart Badge -->
                <div class="relative">
                    <button @click="toggleCart()" 
                            class="relative p-3 bg-blue-50 hover:bg-blue-100 rounded-xl transition-colors"
                            :class="cart.length > 0 ? 'ring-2 ring-blue-200' : ''">
                        <i class="fas fa-shopping-cart text-blue-600 text-lg"></i>
                        <span x-show="cart.length > 0" 
                              class="cart-badge" 
                              x-text="cart.length"></span>
                    </button>
                </div>
            </div>
            
            <!-- Progress Bar -->
            <div class="progress-bar mb-4">
                <div class="progress-fill" :style="`width: ${(currentStep / 4) * 100}%`"></div>
            </div>
        </div>
    </div>


    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid lg:grid-cols-4 gap-8">
            <!-- Main Form Area -->
            <div class="lg:col-span-3 space-y-8">
                
                <!-- Financial Data Warning -->
                @if(!auth('customer_user')->user()->customer->cod_return_account || !auth('customer_user')->user()->customer->settlement_account)
                <div class="bg-gradient-to-r from-yellow-50 to-orange-50 border border-yellow-200 rounded-xl p-6">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-yellow-500 text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-yellow-800 mb-2">Uwaga: Brakują dane finansowe</h3>
                            <p class="text-yellow-700 text-sm mb-3">Przed wysłaniem przesyłek musisz uzupełnić konta bankowe w sekcji finansowej.</p>
                            <a href="{{ route('customer.finances.index') }}" 
                               class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors text-sm font-medium">
                                <i class="fas fa-cog mr-2"></i>
                                Przejdź do ustawień finansowych
                            </a>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Step 1: Package Type Selection -->
                <div x-show="currentStep === 1" x-transition class="space-y-6">
                    <div class="text-center">
                        <h2 class="text-3xl font-bold text-gray-900 mb-2">Wybierz typ przesyłki</h2>
                        <p class="text-gray-600">Wybierz najlepszy typ przesyłki dla Twoich potrzeb</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        <!-- Envelope -->
                        <div class="package-card bg-white rounded-xl p-6 relative"
                             :class="currentPackage.type === 'envelope' ? 'selected' : 'border-2 border-gray-200'"
                             :style="currentPackage.type === 'envelope' ? 'border: 3px solid #2563eb; background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 50%, #93c5fd 100%); transform: scale(1.05); box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.3);' : ''"
                             @click="selectPackageType('envelope')"
                             style="transition: all 0.3s ease;">
                            <!-- Checkmark -->
                            <div x-show="currentPackage.type === 'envelope'" 
                                 class="absolute top-2 right-2 bg-blue-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm font-bold"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-75"
                                 x-transition:enter-end="opacity-100 scale-100">
                                ✓
                            </div>
                            <div class="text-center">
                                <div class="text-5xl mb-4">📄</div>
                                <h3 class="font-bold text-gray-900 mb-2">Koperta</h3>
                                <div class="text-sm text-gray-600 space-y-1">
                                    <div>35 × 25 × 2 cm</div>
                                    <div>do 0.5 kg</div>
                                    <div class="font-semibold text-green-600">od 5.99 zł</div>
                                </div>
                            </div>
                        </div>

                        <!-- Small Package -->
                        <div class="package-card bg-white rounded-xl p-6 relative"
                             :class="currentPackage.type === 'small' ? 'selected' : 'border-2 border-gray-200'"
                             :style="currentPackage.type === 'small' ? 'border: 3px solid #2563eb; background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 50%, #93c5fd 100%); transform: scale(1.05); box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.3);' : ''"
                             @click="selectPackageType('small')"
                             style="transition: all 0.3s ease;">
                            <!-- Checkmark -->
                            <div x-show="currentPackage.type === 'small'" 
                                 class="absolute top-2 right-2 bg-blue-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm font-bold"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-75"
                                 x-transition:enter-end="opacity-100 scale-100">
                                ✓
                            </div>
                            <div class="text-center">
                                <div class="text-5xl mb-4">📦</div>
                                <h3 class="font-bold text-gray-900 mb-2">Mała paczka</h3>
                                <div class="text-sm text-gray-600 space-y-1">
                                    <div>25 × 20 × 15 cm</div>
                                    <div>do 5 kg</div>
                                    <div class="font-semibold text-green-600">od 8.99 zł</div>
                                </div>
                            </div>
                        </div>

                        <!-- Medium Package -->
                        <div class="package-card bg-white rounded-xl p-6 relative"
                             :class="currentPackage.type === 'medium' ? 'selected' : 'border-2 border-gray-200'"
                             :style="currentPackage.type === 'medium' ? 'border: 3px solid #2563eb; background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 50%, #93c5fd 100%); transform: scale(1.05); box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.3);' : ''"
                             @click="selectPackageType('medium')"
                             style="transition: all 0.3s ease;">
                            <!-- Checkmark -->
                            <div x-show="currentPackage.type === 'medium'" 
                                 class="absolute top-2 right-2 bg-blue-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm font-bold"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-75"
                                 x-transition:enter-end="opacity-100 scale-100">
                                ✓
                            </div>
                            <div class="text-center">
                                <div class="text-5xl mb-4">📦</div>
                                <h3 class="font-bold text-gray-900 mb-2">Średnia paczka</h3>
                                <div class="text-sm text-gray-600 space-y-1">
                                    <div>40 × 30 × 20 cm</div>
                                    <div>do 15 kg</div>
                                    <div class="font-semibold text-green-600">od 12.99 zł</div>
                                </div>
                            </div>
                        </div>

                        <!-- Large Package -->
                        <div class="package-card bg-white rounded-xl p-6 relative"
                             :class="currentPackage.type === 'large' ? 'selected' : 'border-2 border-gray-200'"
                             :style="currentPackage.type === 'large' ? 'border: 3px solid #2563eb; background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 50%, #93c5fd 100%); transform: scale(1.05); box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.3);' : ''"
                             @click="selectPackageType('large')"
                             style="transition: all 0.3s ease;">
                            <!-- Checkmark -->
                            <div x-show="currentPackage.type === 'large'" 
                                 class="absolute top-2 right-2 bg-blue-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm font-bold"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-75"
                                 x-transition:enter-end="opacity-100 scale-100">
                                ✓
                            </div>
                            <div class="text-center">
                                <div class="text-5xl mb-4">📦</div>
                                <h3 class="font-bold text-gray-900 mb-2">Duża paczka</h3>
                                <div class="text-sm text-gray-600 space-y-1">
                                    <div>60 × 40 × 30 cm</div>
                                    <div>do 30 kg</div>
                                    <div class="font-semibold text-green-600">od 18.99 zł</div>
                                </div>
                            </div>
                        </div>

                        <!-- Palette -->
                        <div class="package-card bg-white rounded-xl p-6 relative"
                             :class="currentPackage.type === 'palette' ? 'selected' : 'border-2 border-gray-200'"
                             :style="currentPackage.type === 'palette' ? 'border: 3px solid #2563eb; background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 50%, #93c5fd 100%); transform: scale(1.05); box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.3);' : ''"
                             @click="selectPackageType('palette')"
                             style="transition: all 0.3s ease;">
                            <!-- Checkmark -->
                            <div x-show="currentPackage.type === 'palette'" 
                                 class="absolute top-2 right-2 bg-blue-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm font-bold"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-75"
                                 x-transition:enter-end="opacity-100 scale-100">
                                ✓
                            </div>
                            <div class="text-center">
                                <div class="text-5xl mb-4">🏗️</div>
                                <h3 class="font-bold text-gray-900 mb-2">Paleta</h3>
                                <div class="text-sm text-gray-600 space-y-1">
                                    <div>120 × 80 × 180 cm</div>
                                    <div>do 1000 kg</div>
                                    <div class="font-semibold text-green-600">od 89.99 zł</div>
                                </div>
                            </div>
                        </div>

                        <!-- Custom -->
                        <div class="package-card bg-white rounded-xl p-6 relative"
                             :class="currentPackage.type === 'custom' ? 'selected' : 'border-2 border-gray-200'"
                             :style="currentPackage.type === 'custom' ? 'border: 3px solid #2563eb; background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 50%, #93c5fd 100%); transform: scale(1.05); box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.3);' : ''"
                             @click="selectPackageType('custom')"
                             style="transition: all 0.3s ease;">
                            <!-- Checkmark -->
                            <div x-show="currentPackage.type === 'custom'" 
                                 class="absolute top-2 right-2 bg-blue-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm font-bold"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-75"
                                 x-transition:enter-end="opacity-100 scale-100">
                                ✓
                            </div>
                            <div class="text-center">
                                <div class="text-5xl mb-4">📏</div>
                                <h3 class="font-bold text-gray-900 mb-2">Niestandardowy</h3>
                                <div class="text-sm text-gray-600 space-y-1">
                                    <div>Własne wymiary</div>
                                    <div>Własna waga</div>
                                    <div class="font-semibold text-blue-600">Obliczane</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Custom Dimensions -->
                    <div x-show="currentPackage.type === 'custom'" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 transform scale-95"
                         x-transition:enter-end="opacity-100 transform scale-100"
                         class="bg-white rounded-xl border-2 border-gray-200 p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Podaj niestandardowe wymiary</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Długość (cm)</label>
                                <input type="number" 
                                       x-model="currentPackage.dimensions.length" 
                                       class="modern-input"
                                       placeholder="np. 25"
                                       min="1" max="300">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Szerokość (cm)</label>
                                <input type="number" 
                                       x-model="currentPackage.dimensions.width" 
                                       class="modern-input"
                                       placeholder="np. 20"
                                       min="1" max="300">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Wysokość (cm)</label>
                                <input type="number" 
                                       x-model="currentPackage.dimensions.height" 
                                       class="modern-input"
                                       placeholder="np. 15"
                                       min="1" max="300">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Waga (kg)</label>
                                <input type="number" 
                                       x-model="currentPackage.dimensions.weight" 
                                       class="modern-input"
                                       placeholder="np. 2.5"
                                       min="0.1" max="1000" 
                                       step="0.1">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Addresses -->
                <div x-show="currentStep === 2" x-transition class="space-y-6">
                    <div class="text-center">
                        <h2 class="text-3xl font-bold text-gray-900 mb-2">Dane nadawcy i odbiorcy</h2>
                        <p class="text-gray-600">Wprowadź kompletne dane adresowe</p>
                    </div>

                    <div class="grid lg:grid-cols-2 gap-8">
                        <!-- Sender -->
                        <div class="bg-white rounded-xl border-2 border-gray-200 p-6">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-xl font-bold text-gray-900 flex items-center">
                                    <i class="fas fa-paper-plane text-blue-600 mr-3"></i>
                                    Nadawca
                                </h3>
                                <button @click="loadSenderFromProfile()" 
                                        class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                                    <i class="fas fa-user mr-1"></i>
                                    Użyj danych z profilu
                                </button>
                            </div>
                            
                            <div class="space-y-4">
                                <input type="text" 
                                       x-model="currentPackage.sender.name"
                                       class="modern-input"
                                       placeholder="Imię i nazwisko"
                                       required>
                                
                                <input type="text" 
                                       x-model="currentPackage.sender.company"
                                       class="modern-input"
                                       placeholder="Nazwa firmy (opcjonalnie)">
                                
                                <div class="grid grid-cols-3 gap-3">
                                    <input type="text" 
                                           x-model="currentPackage.sender.street"
                                           class="modern-input col-span-2"
                                           placeholder="Ulica"
                                           required>
                                    <input type="text" 
                                           x-model="currentPackage.sender.building_number"
                                           class="modern-input"
                                           placeholder="Nr domu"
                                           required>
                                </div>
                                
                                <input type="text" 
                                       x-model="currentPackage.sender.apartment_number"
                                       class="modern-input"
                                       placeholder="Nr lokalu (opcjonalnie)">
                                
                                <div class="grid grid-cols-2 gap-4">
                                    <input type="text" 
                                           x-model="currentPackage.sender.postal_code"
                                           class="modern-input"
                                           placeholder="Kod pocztowy"
                                           pattern="[0-9]{2}-[0-9]{3}"
                                           required>
                                    
                                    <input type="text" 
                                           x-model="currentPackage.sender.city"
                                           class="modern-input"
                                           placeholder="Miasto"
                                           required>
                                </div>
                                
                                <input type="tel" 
                                       x-model="currentPackage.sender.phone"
                                       class="modern-input"
                                       placeholder="Numer telefonu"
                                       required>
                                
                                <input type="email" 
                                       x-model="currentPackage.sender.email"
                                       class="modern-input"
                                       placeholder="Adres e-mail"
                                       required>
                            </div>
                        </div>

                        <!-- Recipient -->
                        <div class="bg-white rounded-xl border-2 border-gray-200 p-6">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-xl font-bold text-gray-900 flex items-center">
                                    <i class="fas fa-map-marker-alt text-green-600 mr-3"></i>
                                    Odbiorca
                                </h3>
                                <div class="flex space-x-2">
                                    <button @click="copySenderToRecipient()" 
                                            class="text-sm text-purple-600 hover:text-purple-800 font-medium">
                                        <i class="fas fa-copy mr-1"></i>
                                        Kopiuj z nadawcy
                                    </button>
                                    <button class="text-sm text-green-600 hover:text-green-800 font-medium">
                                        <i class="fas fa-address-book mr-1"></i>
                                        Książka adresowa
                                    </button>
                                </div>
                            </div>
                            
                            <div class="space-y-4">
                                <input type="text" 
                                       x-model="currentPackage.recipient.name"
                                       class="modern-input"
                                       placeholder="Imię i nazwisko"
                                       required>
                                
                                <input type="text" 
                                       x-model="currentPackage.recipient.company"
                                       class="modern-input"
                                       placeholder="Nazwa firmy (opcjonalnie)">
                                
                                <div class="grid grid-cols-3 gap-3">
                                    <input type="text" 
                                           x-model="currentPackage.recipient.street"
                                           class="modern-input col-span-2"
                                           placeholder="Ulica"
                                           required>
                                    <input type="text" 
                                           x-model="currentPackage.recipient.building_number"
                                           class="modern-input"
                                           placeholder="Nr domu"
                                           required>
                                </div>
                                
                                <input type="text" 
                                       x-model="currentPackage.recipient.apartment_number"
                                       class="modern-input"
                                       placeholder="Nr lokalu (opcjonalnie)">
                                
                                <div class="grid grid-cols-2 gap-4">
                                    <input type="text" 
                                           x-model="currentPackage.recipient.postal_code"
                                           class="modern-input"
                                           placeholder="Kod pocztowy"
                                           pattern="[0-9]{2}-[0-9]{3}"
                                           required>
                                    
                                    <input type="text" 
                                           x-model="currentPackage.recipient.city"
                                           class="modern-input"
                                           placeholder="Miasto"
                                           required>
                                </div>
                                
                                <input type="tel" 
                                       x-model="currentPackage.recipient.phone"
                                       class="modern-input"
                                       placeholder="Numer telefonu"
                                       required>
                                
                                <input type="email" 
                                       x-model="currentPackage.recipient.email"
                                       class="modern-input"
                                       placeholder="Adres e-mail"
                                       required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Additional Services -->
                <div x-show="currentStep === 3" x-transition class="space-y-6">
                    <div class="text-center">
                        <h2 class="text-3xl font-bold text-gray-900 mb-2">Usługi dodatkowe</h2>
                        <p class="text-gray-600">Wybierz dodatkowe opcje dla swojej przesyłki</p>
                    </div>

                    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- COD Service -->
                        <div class="service-card bg-white"
                             :class="currentPackage.services.cod.enabled ? 'selected' : 'border-gray-200'"
                             @click="toggleService('cod')">
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0 mt-1">
                                    <i class="fas fa-money-bill-wave text-green-600 text-xl"></i>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-bold text-gray-900 mb-1">Pobranie (COD)</h4>
                                    <p class="text-sm text-gray-600 mb-3">Odbierz płatność przy dostawie</p>
                                    
                                    <div x-show="currentPackage.services.cod.enabled" class="space-y-2">
                                        <input type="number" 
                                               x-model="currentPackage.services.cod.amount"
                                               class="w-full p-2 border rounded-lg text-sm"
                                               placeholder="Kwota do pobrania (PLN)"
                                               min="1" max="10000" step="0.01">
                                        
                                        <div class="text-xs text-gray-500">
                                            Prowizja: +2.5% (min. 2.50 zł)
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-shrink-0">
                                    <input type="checkbox" 
                                           :checked="currentPackage.services.cod.enabled"
                                           class="custom-checkbox"
                                           @click.stop="toggleService('cod')">
                                </div>
                            </div>
                        </div>

                        <!-- Insurance -->
                        <div class="service-card bg-white"
                             :class="currentPackage.services.insurance.enabled ? 'selected' : 'border-gray-200'"
                             @click="toggleService('insurance')">
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0 mt-1">
                                    <i class="fas fa-shield-alt text-blue-600 text-xl"></i>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-bold text-gray-900 mb-1">Ubezpieczenie</h4>
                                    <p class="text-sm text-gray-600 mb-3">Dodatkowa ochrona przesyłki</p>
                                    
                                    <div x-show="currentPackage.services.insurance.enabled" class="space-y-2">
                                        <select x-model="currentPackage.services.insurance.value"
                                                class="w-full p-2 border rounded-lg text-sm">
                                            <option value="500">500 PLN (+1.99 zł)</option>
                                            <option value="1000">1000 PLN (+3.99 zł)</option>
                                            <option value="2500">2500 PLN (+7.99 zł)</option>
                                            <option value="5000">5000 PLN (+14.99 zł)</option>
                                            <option value="custom">Inna kwota</option>
                                        </select>
                                        
                                        <input x-show="currentPackage.services.insurance.value === 'custom'"
                                               type="number" 
                                               x-model="currentPackage.services.insurance.customValue"
                                               class="w-full p-2 border rounded-lg text-sm"
                                               placeholder="Wartość ubezpieczenia (PLN)"
                                               min="500" max="50000">
                                    </div>
                                </div>
                                <div class="flex-shrink-0">
                                    <input type="checkbox" 
                                           :checked="currentPackage.services.insurance.enabled"
                                           class="custom-checkbox"
                                           @click.stop="toggleService('insurance')">
                                </div>
                            </div>
                        </div>

                        <!-- Handle with Care -->
                        <div class="service-card bg-white"
                             :class="currentPackage.services.fragile.enabled ? 'selected' : 'border-gray-200'"
                             @click="toggleService('fragile')">
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0 mt-1">
                                    <i class="fas fa-exclamation-triangle text-orange-600 text-xl"></i>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-bold text-gray-900 mb-1">Uwaga - ostrożnie!</h4>
                                    <p class="text-sm text-gray-600">Specjalne oznaczenie przesyłki</p>
                                    <div class="text-xs text-green-600 mt-2">Bezpłatnie</div>
                                </div>
                                <div class="flex-shrink-0">
                                    <input type="checkbox" 
                                           :checked="currentPackage.services.fragile.enabled"
                                           class="custom-checkbox"
                                           @click.stop="toggleService('fragile')">
                                </div>
                            </div>
                        </div>

                        <!-- Priority -->
                        <div class="service-card bg-white"
                             :class="currentPackage.services.priority.enabled ? 'selected' : 'border-gray-200'"
                             @click="toggleService('priority')">
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0 mt-1">
                                    <i class="fas fa-rocket text-red-600 text-xl"></i>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-bold text-gray-900 mb-1">Priorytet</h4>
                                    <p class="text-sm text-gray-600">Szybsza obsługa i doręczenie</p>
                                    <div class="text-xs text-orange-600 mt-2">+4.99 zł</div>
                                </div>
                                <div class="flex-shrink-0">
                                    <input type="checkbox" 
                                           :checked="currentPackage.services.priority.enabled"
                                           class="custom-checkbox"
                                           @click.stop="toggleService('priority')">
                                </div>
                            </div>
                        </div>

                        <!-- Saturday Delivery -->
                        <div class="service-card bg-white"
                             :class="currentPackage.services.saturday.enabled ? 'selected' : 'border-gray-200'"
                             @click="toggleService('saturday')">
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0 mt-1">
                                    <i class="fas fa-calendar-day text-purple-600 text-xl"></i>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-bold text-gray-900 mb-1">Doręczenie w sobotę</h4>
                                    <p class="text-sm text-gray-600">Doręczenie także w weekend</p>
                                    <div class="text-xs text-orange-600 mt-2">+9.99 zł</div>
                                </div>
                                <div class="flex-shrink-0">
                                    <input type="checkbox" 
                                           :checked="currentPackage.services.saturday.enabled"
                                           class="custom-checkbox"
                                           @click.stop="toggleService('saturday')">
                                </div>
                            </div>
                        </div>

                        <!-- Return Receipt -->
                        <div class="service-card bg-white"
                             :class="currentPackage.services.receipt.enabled ? 'selected' : 'border-gray-200'"
                             @click="toggleService('receipt')">
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0 mt-1">
                                    <i class="fas fa-receipt text-teal-600 text-xl"></i>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-bold text-gray-900 mb-1">Zwrotne potwierdzenie</h4>
                                    <p class="text-sm text-gray-600">Potwierdzenie odbioru</p>
                                    <div class="text-xs text-orange-600 mt-2">+2.99 zł</div>
                                </div>
                                <div class="flex-shrink-0">
                                    <input type="checkbox" 
                                           :checked="currentPackage.services.receipt.enabled"
                                           class="custom-checkbox"
                                           @click.stop="toggleService('receipt')">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Notes -->
                    <div class="bg-white rounded-xl border-2 border-gray-200 p-6">
                        <h4 class="font-bold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-sticky-note text-yellow-600 mr-3"></i>
                            Informacje dodatkowe dla kuriera
                        </h4>
                        <textarea x-model="currentPackage.notes"
                                  class="modern-input h-24 resize-none"
                                  placeholder="Dodaj specjalne instrukcje lub uwagi dla kuriera (opcjonalnie)..."></textarea>
                    </div>
                </div>

                <!-- Step 4: Offers Selection -->
                <div x-show="currentStep === 4" x-transition class="space-y-6">
                    <div class="text-center">
                        <h2 class="text-3xl font-bold text-gray-900 mb-2">Wybierz najlepszą ofertę</h2>
                        <p class="text-gray-600">Porównaj ceny i czas doręczenia</p>
                    </div>

                    <!-- Loading -->
                    <div x-show="isLoadingOffers" class="space-y-4">
                        <div class="loading-skeleton h-32 rounded-xl"></div>
                        <div class="loading-skeleton h-32 rounded-xl"></div>
                        <div class="loading-skeleton h-32 rounded-xl"></div>
                    </div>

                    <!-- Offers -->
                    <div x-show="!isLoadingOffers && offers.length > 0" class="space-y-4">
                        <template x-for="offer in offers" :key="offer.id">
                            <div class="offer-card bg-white rounded-xl p-6 relative"
                                 :class="currentPackage.selectedOffer?.id === offer.id ? 'selected' : 'border-2 border-gray-200'"
                                 :style="currentPackage.selectedOffer?.id === offer.id ? 'border: 3px solid #2563eb; background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); transform: scale(1.05); box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.5);' : ''"
                                 @click="selectOffer(offer)"
                                 style="transition: all 0.3s ease;">
                                <!-- Checkmark for offers -->
                                <div x-show="currentPackage.selectedOffer?.id === offer.id" 
                                     class="absolute top-3 right-3 bg-blue-600 text-white rounded-full w-7 h-7 flex items-center justify-center text-sm font-bold"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 scale-75"
                                     x-transition:enter-end="opacity-100 scale-100">
                                    ✓
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-shrink-0">
                                            <div class="w-16 h-16 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl flex items-center justify-center border-2 border-blue-200 shadow-sm"
                                                 :class="{
                                                     'from-orange-50 to-orange-100 border-orange-200': offer.courier === 'InPost',
                                                     'from-red-50 to-red-100 border-red-200': offer.courier === 'DHL',
                                                     'from-yellow-50 to-yellow-100 border-yellow-200': offer.courier === 'DPD',
                                                     'from-green-50 to-green-100 border-green-200': offer.courier === 'UPS'
                                                 }">
                                                <!-- InPost Logo -->
                                                <div x-show="offer.courier === 'InPost'" class="text-center">
                                                    <div class="w-12 h-8 bg-orange-500 rounded-md flex items-center justify-center">
                                                        <span class="text-white font-bold text-xs">In</span>
                                                    </div>
                                                    <div class="text-xs text-orange-600 font-medium mt-1">POST</div>
                                                </div>
                                                <!-- DHL Logo -->
                                                <div x-show="offer.courier === 'DHL'" class="text-center">
                                                    <div class="w-12 h-8 bg-red-600 rounded-md flex items-center justify-center">
                                                        <span class="text-white font-bold text-xs">DHL</span>
                                                    </div>
                                                </div>
                                                <!-- DPD Logo -->
                                                <div x-show="offer.courier === 'DPD'" class="text-center">
                                                    <div class="w-12 h-8 bg-red-600 rounded-md flex items-center justify-center">
                                                        <span class="text-white font-bold text-xs">DPD</span>
                                                    </div>
                                                </div>
                                                <!-- UPS Logo -->
                                                <div x-show="offer.courier === 'UPS'" class="text-center">
                                                    <div class="w-12 h-8 bg-amber-600 rounded-md flex items-center justify-center">
                                                        <span class="text-white font-bold text-xs">UPS</span>
                                                    </div>
                                                </div>
                                                <!-- Generic fallback -->
                                                <div x-show="!['InPost', 'DHL', 'DPD', 'UPS'].includes(offer.courier)">
                                                    <i class="fas fa-truck text-blue-600 text-2xl"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <h4 class="text-xl font-bold text-gray-900" x-text="offer.courier"></h4>
                                            <p class="text-gray-600" x-text="offer.service_name"></p>
                                            <div class="flex items-center space-x-4 mt-2">
                                                <div class="flex items-center text-yellow-500">
                                                    <i class="fas fa-star"></i>
                                                    <span class="ml-1 text-sm text-gray-600" x-text="offer.rating"></span>
                                                </div>
                                                <div class="flex items-center text-gray-500 text-sm">
                                                    <i class="fas fa-clock mr-1"></i>
                                                    <span x-text="offer.time"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-3xl font-bold text-gray-900" x-text="offer.price + ' zł'"></div>
                                        <div class="text-sm text-gray-500" x-text="'netto: ' + offer.price_net + ' zł'"></div>
                                    </div>
                                </div>
                                
                                <div class="mt-4 pt-4 border-t border-gray-200">
                                    <div class="flex flex-wrap gap-2">
                                        <template x-for="feature in offer.features" :key="feature">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-check mr-1"></i>
                                                <span x-text="feature"></span>
                                            </span>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Navigation -->
                <div class="flex items-center justify-between bg-white rounded-xl border-2 border-gray-200 p-6 sticky bottom-4">
                    <button @click="prevStep()" 
                            x-show="currentStep > 1"
                            class="flex items-center px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-medium transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Wstecz
                    </button>
                    
                    <div class="flex items-center space-x-4">
                        <button @click="addToCart()" 
                                x-show="currentStep === 4 && currentPackage.selectedOffer"
                                class="flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-xl font-medium transition-colors transform hover:scale-105">
                            <i class="fas fa-cart-plus mr-2"></i>
                            Dodaj do koszyka
                        </button>
                        
                        <button @click="nextStep()" 
                                x-show="currentStep < 4"
                                :disabled="!canProceedToNext()"
                                :class="canProceedToNext() ? 'bg-blue-600 hover:bg-blue-700 text-white' : 'bg-gray-100 text-gray-400 cursor-not-allowed'"
                                class="flex items-center px-8 py-3 rounded-xl font-medium transition-colors transform hover:scale-105">
                            <span x-show="currentStep === 1">
                                <i class="fas fa-calculator mr-2"></i>
                                Oblicz ceny
                            </span>
                            <span x-show="currentStep > 1">
                                Dalej
                                <i class="fas fa-arrow-right ml-2"></i>
                            </span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Sidebar - Cart -->
            <div class="lg:col-span-1">
                <div class="sticky top-24 space-y-6">
                    <!-- Cart -->
                    <div class="bg-white rounded-xl border-2 border-gray-200 p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-bold text-gray-900 flex items-center">
                                <i class="fas fa-shopping-cart text-blue-600 mr-3"></i>
                                Koszyk
                            </h3>
                            <span class="bg-blue-100 text-blue-800 text-sm font-bold px-3 py-1 rounded-full" 
                                  x-text="cart.length + ' szt.'"></span>
                        </div>

                        <div x-show="cart.length === 0" class="text-center py-8 text-gray-500">
                            <i class="fas fa-cart-plus text-4xl text-gray-300 mb-3"></i>
                            <p class="text-sm">Koszyk jest pusty</p>
                            <p class="text-xs">Dodaj przesyłki aby kontynuować</p>
                        </div>

                        <div x-show="cart.length > 0" class="space-y-4">
                            <template x-for="(item, index) in cart" :key="index">
                                <div class="cart-item bg-gray-50 rounded-lg p-4 border border-gray-200">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center mb-2">
                                                <span class="text-2xl mr-2" x-text="getPackageIcon(item.type)"></span>
                                                <div>
                                                    <h4 class="font-medium text-gray-900" x-text="getPackageTitle(item.type)"></h4>
                                                    <p class="text-sm text-gray-600" x-text="item.selectedOffer.courier"></p>
                                                </div>
                                            </div>
                                            
                                            <div class="text-sm text-gray-600 space-y-1">
                                                <div x-text="item.sender.city + ' → ' + item.recipient.city"></div>
                                                <div class="flex justify-between">
                                                    <span>Cena:</span>
                                                    <span class="font-medium" x-text="item.selectedOffer.price + ' zł'"></span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <button @click="removeFromCart(index)" 
                                                class="ml-3 p-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </div>
                            </template>

                            <!-- Cart Total -->
                            <div class="border-t border-gray-200 pt-4">
                                <div class="flex justify-between items-center mb-4">
                                    <span class="text-lg font-bold text-gray-900">Łącznie:</span>
                                    <span class="text-2xl font-bold text-blue-600" x-text="getCartTotal() + ' zł'"></span>
                                </div>

                                <!-- Payment Methods -->
                                <div class="space-y-3 mb-4">
                                    <h4 class="font-medium text-gray-900">Sposób płatności:</h4>
                                    <div class="space-y-2">
                                        <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                                            <input type="radio" 
                                                   name="payment_method" 
                                                   value="balance" 
                                                   x-model="paymentMethod" 
                                                   class="custom-checkbox mr-3">
                                            <div class="flex items-center">
                                                <i class="fas fa-wallet text-blue-600 mr-3"></i>
                                                <div>
                                                    <div class="font-medium text-sm">Saldo konta</div>
                                                    <div class="text-xs text-gray-500">{{ number_format(auth('customer_user')->user()->customer->current_balance ?? 0, 2) }} PLN dostępne</div>
                                                </div>
                                            </div>
                                        </label>
                                        
                                        @if(auth('customer_user')->user()->customer->credit_limit)
                                        <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                                            <input type="radio" 
                                                   name="payment_method" 
                                                   value="deferred" 
                                                   x-model="paymentMethod" 
                                                   class="custom-checkbox mr-3">
                                            <div class="flex items-center">
                                                <i class="fas fa-calendar-plus text-purple-600 mr-3"></i>
                                                <div>
                                                    <div class="font-medium text-sm">Płatność odroczona</div>
                                                    <div class="text-xs text-gray-500">
                                                        Limit: {{ number_format(auth('customer_user')->user()->customer->credit_limit, 2) }} PLN
                                                        @php 
                                                            $available = (auth('customer_user')->user()->customer->balance ?? 0) + (auth('customer_user')->user()->customer->credit_limit ?? 0);
                                                        @endphp
                                                        ({{ number_format($available, 2) }} PLN dostępne)
                                                    </div>
                                                </div>
                                            </div>
                                        </label>
                                        @endif
                                        
                                        <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                                            <input type="radio" 
                                                   name="payment_method" 
                                                   value="online" 
                                                   x-model="paymentMethod" 
                                                   class="custom-checkbox mr-3">
                                            <div class="flex items-center">
                                                <i class="fas fa-bolt text-green-600 mr-3"></i>
                                                <div>
                                                    <div class="font-medium text-sm">Szybka płatność online</div>
                                                    <div class="text-xs text-gray-500">BLIK, karty płatnicze, przelew</div>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                <div class="space-y-3">
                                    <button @click="submitOrder()" 
                                            :disabled="cart.length === 0 || !paymentMethod"
                                            :class="cart.length > 0 && paymentMethod ? 'bg-blue-600 hover:bg-blue-700 text-white' : 'bg-gray-100 text-gray-400 cursor-not-allowed'"
                                            class="w-full py-4 rounded-xl font-bold text-lg transition-all duration-300 transform hover:scale-105">
                                        <i class="fas fa-credit-card mr-2"></i>
                                        Złóż zamówienie
                                    </button>
                                    
                                    <button @click="clearCart()" 
                                            :disabled="cart.length === 0"
                                            class="w-full py-2 rounded-xl font-medium text-red-600 bg-red-50 hover:bg-red-100 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                        <i class="fas fa-trash mr-2"></i>
                                        Wyczyść koszyk
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Current Package Preview -->
                    <div x-show="currentStep > 1" class="bg-white rounded-xl border-2 border-gray-200 p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-eye text-purple-600 mr-3"></i>
                            Podgląd przesyłki
                        </h3>
                        
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Typ:</span>
                                <span class="font-medium" x-text="getPackageTitle(currentPackage.type)"></span>
                            </div>
                            
                            <div x-show="currentPackage.sender.city && currentPackage.recipient.city">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Trasa:</span>
                                    <span class="font-medium text-right" x-text="currentPackage.sender.city + ' → ' + currentPackage.recipient.city"></span>
                                </div>
                            </div>
                            
                            <div x-show="currentPackage.selectedOffer">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Kurier:</span>
                                    <span class="font-medium" x-text="currentPackage.selectedOffer?.courier"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Cena:</span>
                                    <span class="font-bold text-blue-600" x-text="currentPackage.selectedOffer?.price + ' zł'"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notifications -->
    <div x-show="showToast" 
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="translate-x-full opacity-0"
         x-transition:enter-end="translate-x-0 opacity-100"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="translate-x-0 opacity-100"
         x-transition:leave-end="translate-x-full opacity-0"
         class="toast">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i :class="toastType === 'success' ? 'fas fa-check-circle text-green-500' : 'fas fa-exclamation-circle text-red-500'"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-gray-900" x-text="toastMessage"></p>
            </div>
            <div class="ml-auto pl-3">
                <button @click="hideToast()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Cart Preview Modal -->
    <div x-show="showCartModal" 
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-black bg-opacity-50" @click="showCartModal = false"></div>
        
        <!-- Modal content -->
        <div class="relative min-h-screen flex items-center justify-center p-4">
            <div class="relative bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95">
                
                <!-- Header -->
                <div class="sticky top-0 bg-white rounded-t-2xl border-b border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">Podgląd koszyka</h2>
                            <p class="text-sm text-gray-600" x-text="`${cart.length} przesyłek w koszyku`"></p>
                        </div>
                        <button @click="showCartModal = false" 
                                class="p-2 hover:bg-gray-100 rounded-full transition-colors">
                            <i class="fas fa-times text-gray-500 text-xl"></i>
                        </button>
                    </div>
                </div>

                <!-- Cart Items -->
                <div class="p-6 space-y-4">
                    <template x-for="(item, index) in cart" :key="index">
                        <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <!-- Package info -->
                                    <div class="flex items-center space-x-3 mb-4">
                                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-box text-blue-600"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-gray-900" x-text="item.type"></h4>
                                            <p class="text-sm text-gray-600" x-text="`${item.dimensions.length} × ${item.dimensions.width} × ${item.dimensions.height} cm, ${item.dimensions.weight} kg`"></p>
                                        </div>
                                    </div>

                                    <!-- Addresses -->
                                    <div class="grid md:grid-cols-2 gap-4 mb-4">
                                        <div class="bg-white rounded-lg p-4 border">
                                            <h5 class="font-medium text-gray-900 mb-2">
                                                <i class="fas fa-user-circle text-green-600 mr-2"></i>Nadawca
                                            </h5>
                                            <div class="text-sm text-gray-600 space-y-1">
                                                <div x-text="item.sender.name"></div>
                                                <div x-text="item.sender.company" x-show="item.sender.company"></div>
                                                <div x-text="item.sender.address"></div>
                                                <div x-text="`${item.sender.postal_code} ${item.sender.city}`"></div>
                                            </div>
                                        </div>
                                        
                                        <div class="bg-white rounded-lg p-4 border">
                                            <h5 class="font-medium text-gray-900 mb-2">
                                                <i class="fas fa-map-marker-alt text-blue-600 mr-2"></i>Odbiorca
                                            </h5>
                                            <div class="text-sm text-gray-600 space-y-1">
                                                <div x-text="item.recipient.name"></div>
                                                <div x-text="item.recipient.company" x-show="item.recipient.company"></div>
                                                <div x-text="item.recipient.address"></div>
                                                <div x-text="`${item.recipient.postal_code} ${item.recipient.city}`"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Services -->
                                    <div x-show="Object.values(item.services || {}).some(service => service.enabled)" class="mb-4">
                                        <h5 class="font-medium text-gray-900 mb-2">
                                            <i class="fas fa-cogs text-purple-600 mr-2"></i>Usługi dodatkowe
                                        </h5>
                                        <div class="flex flex-wrap gap-2">
                                            <template x-for="[key, service] in Object.entries(item.services || {})" :key="key">
                                                <span x-show="service.enabled" 
                                                      class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                    <span x-text="key.toUpperCase()"></span>
                                                    <span x-show="service.amount" x-text="`(${service.amount} zł)`" class="ml-1"></span>
                                                </span>
                                            </template>
                                        </div>
                                    </div>

                                    <!-- Courier info -->
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-2">
                                            <i class="fas fa-truck text-orange-600"></i>
                                            <span class="text-sm text-gray-600" x-text="item.selectedOffer.courier"></span>
                                            <span class="text-sm text-gray-500" x-text="item.selectedOffer.service_name"></span>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-lg font-bold text-blue-600" x-text="item.finalPrice || item.selectedOffer.price + ' zł'"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Remove button -->
                                <button @click="removeFromCart(index)" 
                                        class="ml-4 p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </template>

                    <!-- Empty cart -->
                    <div x-show="cart.length === 0" class="text-center py-12">
                        <div class="text-6xl text-gray-300 mb-4">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <h3 class="text-xl font-medium text-gray-900 mb-2">Koszyk jest pusty</h3>
                        <p class="text-gray-600">Dodaj przesyłki do koszyka, aby kontynuować</p>
                    </div>
                </div>

                <!-- Footer with totals and actions -->
                <div x-show="cart.length > 0" class="sticky bottom-0 bg-white rounded-b-2xl border-t border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="text-lg font-medium text-gray-900">
                            Łącznie za <span x-text="cart.length"></span> przesyłek:
                        </div>
                        <div class="text-2xl font-bold text-blue-600" x-text="getCartTotal() + ' zł'"></div>
                    </div>

                    <!-- Payment method selection -->
                    <div class="mb-4">
                        <h4 class="font-medium text-gray-900 mb-3">Sposób płatności:</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <!-- Balance payment -->
                            <label class="relative flex items-center p-4 border rounded-xl cursor-pointer hover:bg-gray-50"
                                   :class="paymentMethod === 'balance' ? 'border-blue-500 bg-blue-50' : 'border-gray-200'">
                                <input type="radio" x-model="paymentMethod" value="balance" class="sr-only">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-wallet text-green-600"></i>
                                    </div>
                                    <div>
                                        <div class="font-medium">Saldo</div>
                                        <div class="text-sm text-gray-600">{{ number_format(auth('customer_user')->user()->customer->current_balance, 2) }} PLN</div>
                                    </div>
                                </div>
                                <div x-show="paymentMethod === 'balance'" class="absolute top-2 right-2">
                                    <i class="fas fa-check-circle text-blue-600"></i>
                                </div>
                            </label>

                            <!-- Deferred payment -->
                            @if(auth('customer_user')->user()->customer->credit_limit > 0)
                            <label class="relative flex items-center p-4 border rounded-xl cursor-pointer hover:bg-gray-50"
                                   :class="paymentMethod === 'deferred' ? 'border-blue-500 bg-blue-50' : 'border-gray-200'">
                                <input type="radio" x-model="paymentMethod" value="deferred" class="sr-only">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-clock text-orange-600"></i>
                                    </div>
                                    <div>
                                        <div class="font-medium">Płatność odroczona</div>
                                        <div class="text-sm text-gray-600">Limit: {{ number_format(auth('customer_user')->user()->customer->credit_limit, 2) }} PLN</div>
                                    </div>
                                </div>
                                <div x-show="paymentMethod === 'deferred'" class="absolute top-2 right-2">
                                    <i class="fas fa-check-circle text-blue-600"></i>
                                </div>
                            </label>
                            @endif

                            <!-- Online payment -->
                            <label class="relative flex items-center p-4 border rounded-xl cursor-pointer hover:bg-gray-50"
                                   :class="paymentMethod === 'online' ? 'border-blue-500 bg-blue-50' : 'border-gray-200'">
                                <input type="radio" x-model="paymentMethod" value="online" class="sr-only">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-credit-card text-blue-600"></i>
                                    </div>
                                    <div>
                                        <div class="font-medium">Szybka płatność</div>
                                        <div class="text-sm text-gray-600">Karta/BLIK/Przelew</div>
                                    </div>
                                </div>
                                <div x-show="paymentMethod === 'online'" class="absolute top-2 right-2">
                                    <i class="fas fa-check-circle text-blue-600"></i>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Action buttons -->
                    <div class="flex space-x-3">
                        <button @click="clearCart()" 
                                :disabled="cart.length === 0"
                                class="py-3 px-4 bg-red-100 text-red-700 rounded-xl font-medium hover:bg-red-200 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                            <i class="fas fa-trash"></i>
                        </button>
                        <button @click="showCartModal = false" 
                                class="flex-1 py-3 px-6 bg-gray-100 text-gray-700 rounded-xl font-medium hover:bg-gray-200 transition-colors">
                            Kontynuuj zakupy
                        </button>
                        <button @click="submitOrderFromModal()" 
                                :disabled="!paymentMethod"
                                :class="paymentMethod ? 'bg-blue-600 hover:bg-blue-700 text-white' : 'bg-gray-100 text-gray-400 cursor-not-allowed'"
                                class="flex-1 py-3 px-6 rounded-xl font-medium transition-colors">
                            <i class="fas fa-credit-card mr-2"></i>
                            Złóż zamówienie
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('modernShipmentManager', () => ({
        currentStep: 1,
        isLoadingOffers: false,
        showToast: false,
        toastMessage: '',
        toastType: 'success',
        cart: [],
        paymentMethod: '',
        showCartModal: false,
        
        currentPackage: {
            type: '',
            dimensions: { length: '', width: '', height: '', weight: '' },
            sender: { name: '', company: '', street: '', building_number: '', apartment_number: '', city: '', postal_code: '', phone: '', email: '' },
            recipient: { name: '', company: '', street: '', building_number: '', apartment_number: '', city: '', postal_code: '', phone: '', email: '' },
            services: {
                cod: { enabled: false, amount: '' },
                insurance: { enabled: false, value: '500', customValue: '' },
                fragile: { enabled: false },
                priority: { enabled: false },
                saturday: { enabled: false },
                receipt: { enabled: false }
            },
            notes: '',
            selectedOffer: null
        },
        
        offers: [],

        init() {
            this.loadCustomerData();
            this.loadCartFromStorage();
            
            // Auto-save cart
            this.$watch('cart', () => {
                this.saveCartToStorage();
            });
        },

        loadCustomerData() {
            @if(auth('customer_user')->user()->customer)
                this.currentPackage.sender = {
                    name: '{{ auth("customer_user")->user()->first_name }} {{ auth("customer_user")->user()->last_name }}',
                    company: '{{ auth("customer_user")->user()->customer->company_name }}',
                    street: '{{ auth("customer_user")->user()->customer->address ?? "" }}',
                    building_number: '',
                    apartment_number: '',
                    city: '{{ auth("customer_user")->user()->customer->city ?? "" }}',
                    postal_code: '{{ auth("customer_user")->user()->customer->postal_code ?? "" }}',
                    phone: '{{ auth("customer_user")->user()->customer->phone ?? "" }}',
                    email: '{{ auth("customer_user")->user()->customer->email ?? "" }}'
                };
            @endif
        },

        loadSenderFromProfile() {
            this.loadCustomerData();
            this.showToast('Załadowano dane z profilu', 'success');
        },

        copySenderToRecipient() {
            this.currentPackage.recipient = { ...this.currentPackage.sender };
            this.showToast('Skopiowano dane nadawcy', 'success');
        },

        selectPackageType(type) {
            this.currentPackage.type = type;
            
            const presets = {
                'envelope': { length: 35, width: 25, height: 2, weight: 0.5 },
                'small': { length: 25, width: 20, height: 15, weight: 5 },
                'medium': { length: 40, width: 30, height: 20, weight: 15 },
                'large': { length: 60, width: 40, height: 30, weight: 30 },
                'palette': { length: 120, width: 80, height: 180, weight: 1000 }
            };
            
            if (presets[type]) {
                this.currentPackage.dimensions = { ...presets[type] };
            } else {
                this.currentPackage.dimensions = { length: '', width: '', height: '', weight: '' };
            }
        },

        toggleService(service) {
            this.currentPackage.services[service].enabled = !this.currentPackage.services[service].enabled;
        },

        canProceedToNext() {
            switch (this.currentStep) {
                case 1:
                    return this.currentPackage.type && this.validateDimensions();
                case 2:
                    return this.validateAddresses();
                case 3:
                    return true;
                case 4:
                    return this.currentPackage.selectedOffer;
                default:
                    return true;
            }
        },

        validateDimensions() {
            if (this.currentPackage.type === 'custom') {
                const d = this.currentPackage.dimensions;
                return d.length > 0 && d.width > 0 && d.height > 0 && d.weight > 0;
            }
            return true; // Pre-defined packages have valid dimensions by default
        },

        validateAddresses() {
            const s = this.currentPackage.sender;
            const r = this.currentPackage.recipient;
            
            const senderValid = s.name && s.street && s.building_number && s.city && s.postal_code && s.phone && s.email;
            const recipientValid = r.name && r.street && r.building_number && r.city && r.postal_code && r.phone && r.email;
            
            return senderValid && recipientValid;
        },

        async nextStep() {
            if (!this.canProceedToNext()) {
                this.showToast('Uzupełnij wszystkie wymagane pola', 'error');
                return;
            }

            if (this.currentStep === 3) {
                await this.calculateOffers();
            }

            this.currentStep = Math.min(this.currentStep + 1, 4);
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
                            weight: parseFloat(this.currentPackage.dimensions.weight) || 1,
                            length: parseFloat(this.currentPackage.dimensions.length) || 20,
                            width: parseFloat(this.currentPackage.dimensions.width) || 15,
                            height: parseFloat(this.currentPackage.dimensions.height) || 10,
                            value: 100
                        },
                        sender: {
                            ...this.currentPackage.sender,
                            address: this.currentPackage.sender.street + ' ' + this.currentPackage.sender.building_number + 
                                   (this.currentPackage.sender.apartment_number ? '/' + this.currentPackage.sender.apartment_number : '')
                        },
                        recipient: {
                            ...this.currentPackage.recipient,
                            address: this.currentPackage.recipient.street + ' ' + this.currentPackage.recipient.building_number + 
                                   (this.currentPackage.recipient.apartment_number ? '/' + this.currentPackage.recipient.apartment_number : '')
                        }
                    })
                });

                const data = await response.json();
                
                if (data.success && data.prices) {
                    this.offers = data.prices.map(price => ({
                        id: price.service_type,
                        courier: 'InPost',
                        service_name: price.service_name,
                        price: price.price_gross,
                        price_net: price.price_net,
                        time: this.getEstimatedTime(price.service_type),
                        rating: 4.7,
                        features: this.getServiceFeatures(price.service_type)
                    }));
                } else {
                    this.offers = [
                        {
                            id: 'inpost_locker_standard',
                            courier: 'InPost',
                            service_name: 'Paczkomat Standard',
                            price: 12.99,
                            price_net: 10.56,
                            time: '1-2 dni robocze',
                            rating: 4.8,
                            features: ['Paczkomat 24/7', 'SMS powiadomienia', 'Śledzenie online']
                        },
                        {
                            id: 'inpost_locker_express',
                            courier: 'InPost',
                            service_name: 'Paczkomat Express',
                            price: 16.99,
                            price_net: 13.81,
                            time: '24 godziny',
                            rating: 4.9,
                            features: ['Paczkomat 24/7', 'Express 24h', 'SMS powiadomienia']
                        },
                        {
                            id: 'inpost_courier_standard',
                            courier: 'InPost',
                            service_name: 'Kurier Standard',
                            price: 18.99,
                            price_net: 15.44,
                            time: '1-2 dni robocze',
                            rating: 4.6,
                            features: ['Odbiór kurierem', 'Doręczenie kurierem', 'SMS powiadomienia']
                        },
                        {
                            id: 'inpost_courier_express',
                            courier: 'InPost',
                            service_name: 'Kurier Express',
                            price: 24.99,
                            price_net: 20.32,
                            time: '24 godziny',
                            rating: 4.7,
                            features: ['Odbiór kurierem', 'Doręczenie kurierem', 'Express 24h']
                        },
                        {
                            id: 'inpost_pop_standard',
                            courier: 'InPost',
                            service_name: 'POP Standard',
                            price: 10.99,
                            price_net: 8.94,
                            time: '1-2 dni robocze',
                            rating: 4.5,
                            features: ['Punkt odbioru', 'SMS powiadomienia', 'Śledzenie online']
                        },
                        {
                            id: 'inpost_pop_express',
                            courier: 'InPost',
                            service_name: 'POP Express',
                            price: 14.99,
                            price_net: 12.19,
                            time: '24 godziny',
                            rating: 4.6,
                            features: ['Punkt odbioru', 'Express 24h', 'SMS powiadomienia']
                        },
                        {
                            id: 'inpost_courier_to_locker',
                            courier: 'InPost',
                            service_name: 'Kurier → Paczkomat',
                            price: 15.99,
                            price_net: 13.00,
                            time: '1-2 dni robocze',
                            rating: 4.7,
                            features: ['Odbiór kurierem', 'Paczkomat 24/7', 'SMS powiadomienia']
                        },
                        {
                            id: 'inpost_locker_to_courier',
                            courier: 'InPost',
                            service_name: 'Paczkomat → Kurier',
                            price: 17.99,
                            price_net: 14.63,
                            time: '1-2 dni robocze',
                            rating: 4.8,
                            features: ['Paczkomat nadanie', 'Doręczenie kurierem', 'SMS powiadomienia']
                        },
                        {
                            id: 'inpost_no_label',
                            courier: 'InPost',
                            service_name: 'Bez etykiety',
                            price: 8.99,
                            price_net: 7.31,
                            time: '1-2 dni robocze',
                            rating: 4.4,
                            features: ['Bez etykiety', 'Płatność przy odbiorze', 'Tańsze rozwiązanie']
                        }
                    ];
                }
            } catch (error) {
                console.error('Calculate offers failed:', error);
                this.showToast('Nie udało się obliczyć cen. Spróbuj ponownie.', 'error');
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
                'inpost_pop_standard': '1-2 dni robocze',
                'inpost_pop_express': '24 godziny',
                'inpost_courier_to_locker': '1-2 dni robocze',
                'inpost_locker_to_courier': '1-2 dni robocze',
                'inpost_no_label': '1-2 dni robocze'
            };
            return times[serviceType] || '1-2 dni robocze';
        },

        getServiceFeatures(serviceType) {
            const features = {
                'inpost_locker_standard': ['Paczkomat 24/7', 'SMS powiadomienia', 'Śledzenie online'],
                'inpost_locker_express': ['Paczkomat 24/7', 'Express 24h', 'SMS powiadomienia'],
                'inpost_courier_standard': ['Odbiór kurierem', 'Doręczenie kurierem', 'SMS powiadomienia'],
                'inpost_courier_express': ['Odbiór kurierem', 'Doręczenie kurierem', 'Express 24h'],
                'inpost_pop_standard': ['Punkt odbioru', 'SMS powiadomienia', 'Śledzenie online'],
                'inpost_pop_express': ['Punkt odbioru', 'Express 24h', 'SMS powiadomienia'],
                'inpost_courier_to_locker': ['Odbiór kurierem', 'Paczkomat 24/7', 'SMS powiadomienia'],
                'inpost_locker_to_courier': ['Paczkomat nadanie', 'Doręczenie kurierem', 'SMS powiadomienia'],
                'inpost_no_label': ['Bez etykiety', 'Płatność przy odbiorze', 'Tańsze rozwiązanie']
            };
            return features[serviceType] || ['SMS powiadomienia', 'Śledzenie online'];
        },

        selectOffer(offer) {
            this.currentPackage.selectedOffer = offer;
        },

        addToCart() {
            if (!this.currentPackage.selectedOffer) {
                this.showToast('Wybierz ofertę kurierską', 'error');
                return;
            }

            // Add services cost
            let totalPrice = parseFloat(this.currentPackage.selectedOffer.price);
            
            if (this.currentPackage.services.cod.enabled) {
                totalPrice += Math.max(2.5, totalPrice * 0.025);
            }
            if (this.currentPackage.services.insurance.enabled) {
                const value = this.currentPackage.services.insurance.value === 'custom' 
                    ? this.currentPackage.services.insurance.customValue 
                    : this.currentPackage.services.insurance.value;
                totalPrice += this.calculateInsuranceCost(parseFloat(value));
            }
            if (this.currentPackage.services.priority.enabled) totalPrice += 4.99;
            if (this.currentPackage.services.saturday.enabled) totalPrice += 9.99;
            if (this.currentPackage.services.receipt.enabled) totalPrice += 2.99;

            // Create cart item
            const cartItem = {
                ...JSON.parse(JSON.stringify(this.currentPackage)),
                finalPrice: totalPrice.toFixed(2),
                id: Date.now()
            };

            this.cart.push(cartItem);
            this.resetCurrentPackage();
            this.currentStep = 1;
            
            this.showToast('Dodano przesyłkę do koszyka!', 'success');
        },

        calculateInsuranceCost(value) {
            if (value <= 500) return 1.99;
            if (value <= 1000) return 3.99;
            if (value <= 2500) return 7.99;
            if (value <= 5000) return 14.99;
            return Math.max(14.99, value * 0.005);
        },

        removeFromCart(index) {
            this.cart.splice(index, 1);
            this.showToast('Usunięto przesyłkę z koszyka', 'success');
        },

        resetCurrentPackage() {
            const senderData = this.currentPackage.sender;
            this.currentPackage = {
                type: '',
                dimensions: { length: '', width: '', height: '', weight: '' },
                sender: senderData,
                recipient: { name: '', company: '', street: '', building_number: '', apartment_number: '', city: '', postal_code: '', phone: '', email: '' },
                services: {
                    cod: { enabled: false, amount: '' },
                    insurance: { enabled: false, value: '500', customValue: '' },
                    fragile: { enabled: false },
                    priority: { enabled: false },
                    saturday: { enabled: false },
                    receipt: { enabled: false }
                },
                notes: '',
                selectedOffer: null
            };
            this.offers = [];
        },

        getCartTotal() {
            return this.cart.reduce((sum, item) => sum + parseFloat(item.finalPrice || item.selectedOffer.price), 0).toFixed(2);
        },

        getPackageIcon(type) {
            const icons = {
                'envelope': '📄',
                'small': '📦',
                'medium': '📦',
                'large': '📦',
                'palette': '🏗️',
                'custom': '📏'
            };
            return icons[type] || '📦';
        },

        getPackageTitle(type) {
            const titles = {
                'envelope': 'Koperta',
                'small': 'Mała paczka',
                'medium': 'Średnia paczka',
                'large': 'Duża paczka',
                'palette': 'Paleta',
                'custom': 'Niestandardowy'
            };
            return titles[type] || 'Przesyłka';
        },

        async submitOrder() {
            if (this.cart.length === 0) {
                this.showToast('Koszyk jest pusty', 'error');
                return;
            }

            if (!this.paymentMethod) {
                this.showToast('Wybierz sposób płatności', 'error');
                return;
            }

            // Validate payment method
            const totalAmount = parseFloat(this.getCartTotal());
            if (this.paymentMethod === 'balance') {
                const balance = {{ auth('customer_user')->user()->customer->current_balance ?? 0 }};
                if (balance < totalAmount) {
                    this.showToast(`Niewystarczające saldo. Dostępne: ${balance.toFixed(2)} PLN`, 'error');
                    return;
                }
            } else if (this.paymentMethod === 'deferred') {
                const balance = {{ auth('customer_user')->user()->customer->current_balance ?? 0 }};
                const creditLimit = {{ auth('customer_user')->user()->customer->credit_limit ?? 0 }};
                const available = balance + creditLimit;
                
                if (creditLimit === 0) {
                    this.showToast('Płatność odroczona nie jest dostępna - brak limitu kredytowego', 'error');
                    return;
                }
                
                if (available < totalAmount) {
                    this.showToast(`Przekroczono limit kredytowy. Dostępne: ${available.toFixed(2)} PLN`, 'error');
                    return;
                }
            }

            try {
                // Transform cart data to expected format
                const transformedShipments = this.cart.map(item => ({
                    type: item.type,
                    sender: {
                        ...item.sender,
                        address: item.sender.address || (item.sender.street + ' ' + item.sender.building_number + 
                               (item.sender.apartment_number ? '/' + item.sender.apartment_number : ''))
                    },
                    recipient: {
                        ...item.recipient,
                        address: item.recipient.address || (item.recipient.street + ' ' + item.recipient.building_number + 
                               (item.recipient.apartment_number ? '/' + item.recipient.apartment_number : ''))
                    },
                    selectedOffer: item.selectedOffer,
                    dimensions: item.dimensions,
                    services: item.services,
                    notes: item.notes
                }));

                const response = await fetch('{{ route("customer.shipments.bulk-create") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        shipments: transformedShipments,
                        payment_method: this.paymentMethod,
                        total_amount: this.getCartTotal()
                    })
                });

                const data = await response.json();
                
                if (data.success) {
                    this.cart = [];
                    this.saveCartToStorage();
                    this.showToast('Zamówienie zostało złożone pomyślnie!', 'success');
                    
                    setTimeout(() => {
                        window.location.href = data.redirect_url || '{{ route("customer.shipments.index") }}';
                    }, 2000);
                } else {
                    throw new Error(data.message || 'Błąd podczas składania zamówienia');
                }
            } catch (error) {
                console.error('Submit order failed:', error);
                console.log('Cart data:', this.cart);
                console.log('Payment method:', this.paymentMethod);
                this.showToast('Nie udało się złożyć zamówienia. Spróbuj ponownie. Błąd: ' + error.message, 'error');
            }
        },

        toggleCart() {
            this.showCartModal = !this.showCartModal;
        },

        submitOrderFromModal() {
            this.showCartModal = false;
            this.submitOrder();
        },

        removeFromCart(index) {
            this.cart.splice(index, 1);
            this.saveCartToStorage();
            this.showToast('Przesyłka została usunięta z koszyka', 'success');
        },

        clearCart() {
            if (confirm('Czy na pewno chcesz wyczyścić cały koszyk? Ta akcja jest nieodwracalna.')) {
                this.cart = [];
                this.paymentMethod = '';
                this.saveCartToStorage();
                this.showToast('Koszyk został wyczyszczony', 'success');
                this.showCartModal = false;
            }
        },

        goBack() {
            window.location.href = '{{ route("customer.shipments.index") }}';
        },

        saveCartToStorage() {
            localStorage.setItem('shipment_cart', JSON.stringify({
                cart: this.cart,
                timestamp: Date.now()
            }));
        },

        loadCartFromStorage() {
            const saved = localStorage.getItem('shipment_cart');
            if (!saved) return;

            try {
                const { cart, timestamp } = JSON.parse(saved);
                
                // Only restore if saved within last 24 hours
                if (Date.now() - timestamp < 24 * 60 * 60 * 1000) {
                    this.cart = cart || [];
                }
            } catch (error) {
                console.warn('Failed to load cart from storage:', error);
            }
        },

        showToast(message, type = 'success') {
            this.toastMessage = message;
            this.toastType = type;
            this.showToast = true;
            
            setTimeout(() => {
                this.hideToast();
            }, 4000);
        },

        hideToast() {
            this.showToast = false;
        }
    }));
});
</script>
@endpush
@endsection