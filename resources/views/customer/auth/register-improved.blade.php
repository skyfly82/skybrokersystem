@extends('layouts.auth')

@section('title', 'Rejestracja - SkyBroker')
@section('header', 'Załóż konto w SkyBroker')
@section('description', 'Wypróbuj za darmo - bez zobowiązań')

@push('styles')
<style>
    .bg-gradient-custom {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    .bg-base-blue {
        background-color: #3B82F6;
    }
    .bg-base-blue:hover {
        background-color: #2563EB;
    }
    .text-base-blue {
        color: #3B82F6;
    }
    .border-base-blue {
        border-color: #3B82F6;
    }
    .ring-base-blue {
        --tw-ring-color: #3B82F6;
    }
    .progress-bar {
        transition: width 0.3s ease;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-custom">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-white mb-2">Załóż konto w SkyBroker</h1>
                <p class="text-blue-100">Wypróbuj za darmo - bez zobowiązań, okres próbny 14 dni</p>
            </div>

            <!-- Main Form Card -->
            <div class="bg-white rounded-lg shadow-xl p-8" x-data="registrationForm">
                <!-- Progress Bar -->
                <div class="mb-8">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium text-gray-700">Krok <span x-text="currentStep"></span> z 3</span>
                        <span class="text-sm text-gray-500"><span x-text="Math.round(progress)"></span>%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-base-blue h-2 rounded-full progress-bar" :style="`width: ${progress}%`"></div>
                    </div>
                    <div class="flex justify-between mt-2 text-xs text-gray-500">
                        <span :class="currentStep >= 1 ? 'text-blue-600 font-medium' : ''">Typ konta</span>
                        <span :class="currentStep >= 2 ? 'text-blue-600 font-medium' : ''">Dane <span x-text="accountType === 'company' ? 'firmowe' : 'osobowe'"></span></span>
                        <span :class="currentStep >= 3 ? 'text-blue-600 font-medium' : ''">Hasło i regulamin</span>
                    </div>
                </div>

                <!-- Step 1: Account Type -->
                <div x-show="currentStep === 1" x-transition>
                    <h2 class="text-xl font-semibold text-gray-900 mb-6 text-center">Wybierz typ konta</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <!-- Company Account -->
                        <div 
                            @click="selectAccountType('company')" 
                            :class="accountType === 'company' ? 'ring-2 ring-blue-500 border-blue-500' : 'border-gray-200 hover:border-gray-300'"
                            class="border-2 rounded-lg p-6 cursor-pointer transition-all duration-200 hover:shadow-md"
                        >
                            <div class="text-center">
                                <div class="w-16 h-16 mx-auto mb-4 bg-blue-50 rounded-full flex items-center justify-center">
                                    <svg class="w-8 h-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-2m-2 0H7m5 0v-5a2 2 0 00-2-2H8a2 2 0 00-2 2v5"/>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Konto Firmowe</h3>
                                <p class="text-sm text-gray-600 mb-4">Dla firm i przedsiębiorców</p>
                                <ul class="text-xs text-gray-500 space-y-1">
                                    <li>✓ Wyższy limit kredytowy</li>
                                    <li>✓ Zarządzanie użytkownikami</li>
                                    <li>✓ Faktury VAT</li>
                                    <li>✓ Integracja z GUS</li>
                                </ul>
                            </div>
                        </div>

                        <!-- Individual Account -->
                        <div 
                            @click="selectAccountType('individual')" 
                            :class="accountType === 'individual' ? 'ring-2 ring-blue-500 border-blue-500' : 'border-gray-200 hover:border-gray-300'"
                            class="border-2 rounded-lg p-6 cursor-pointer transition-all duration-200 hover:shadow-md"
                        >
                            <div class="text-center">
                                <div class="w-16 h-16 mx-auto mb-4 bg-green-50 rounded-full flex items-center justify-center">
                                    <svg class="w-8 h-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Konto Indywidualne</h3>
                                <p class="text-sm text-gray-600 mb-4">Dla klientów prywatnych</p>
                                <ul class="text-xs text-gray-500 space-y-1">
                                    <li>✓ Szybka rejestracja</li>
                                    <li>✓ Social media login</li>
                                    <li>✓ Wszystkie kurierzy</li>
                                    <li>✓ Proste zarządzanie</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Social Media for Individual -->
                    <div x-show="accountType === 'individual'" x-transition class="mb-6 p-4 bg-green-50 rounded-lg border border-green-200">
                        <h3 class="text-center font-medium text-green-900 mb-3">Szybka rejestracja przez social media</h3>
                        <div class="flex justify-center space-x-3">
                            <a href="{{ route('customer.auth.social', 'google') }}?type=individual" class="flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 transition-colors">
                                <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24">
                                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                                </svg>
                                <span class="text-sm">Google</span>
                            </a>
                            <a href="{{ route('customer.auth.social', 'facebook') }}?type=individual" class="flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="#1877F2" viewBox="0 0 24 24">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                                <span class="text-sm">Facebook</span>
                            </a>
                        </div>
                        <div class="text-center mt-3">
                            <div class="relative">
                                <div class="absolute inset-0 flex items-center">
                                    <div class="w-full border-t border-green-300"></div>
                                </div>
                                <div class="relative flex justify-center text-sm">
                                    <span class="px-2 bg-green-50 text-green-700">lub wypełnij formularz</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button @click="nextStep()" :disabled="!accountType" class="bg-base-blue hover:bg-blue-700 disabled:bg-gray-400 text-white px-6 py-2 rounded-md font-medium transition-colors">
                            Dalej
                        </button>
                    </div>
                </div>

                <!-- Step 2: Form Data -->
                <div x-show="currentStep === 2" x-transition>
                    <div class="flex items-center justify-between mb-6">
                        <button @click="prevStep()" class="text-gray-600 hover:text-gray-800 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                            Wstecz
                        </button>
                        <h2 class="text-xl font-semibold text-gray-900">
                            <span x-text="accountType === 'company' ? 'Dane firmowe' : 'Twoje dane'"></span>
                        </h2>
                        <div></div>
                    </div>

                    <!-- Company Fields -->
                    <div x-show="accountType === 'company'">
                        <div class="space-y-4">
                            <!-- Company Name -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Nazwa firmy <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    x-model="formData.company_name"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="np. ABC Sp. z o.o."
                                    required
                                >
                            </div>

                            <!-- NIP with GUS -->
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label class="block text-sm font-medium text-gray-700">NIP</label>
                                    <button 
                                        type="button" 
                                        @click="fetchFromGUS()" 
                                        :disabled="!formData.nip || formData.nip.length < 10 || gusLoading"
                                        class="text-sm bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white px-3 py-1 rounded transition-colors"
                                    >
                                        <span x-show="!gusLoading">
                                            <svg class="w-4 h-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                                            </svg>
                                            Pobierz z GUS
                                        </span>
                                        <span x-show="gusLoading">
                                            <svg class="w-4 h-4 inline mr-1 animate-spin" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            Pobieranie...
                                        </span>
                                    </button>
                                </div>
                                <input 
                                    type="text" 
                                    x-model="formData.nip"
                                    @input="formatNip()"
                                    maxlength="12"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="1234567890"
                                >
                                <p class="text-xs text-gray-500 mt-1">Wpisz NIP i kliknij "Pobierz z GUS" aby automatycznie uzupełnić dane</p>
                            </div>

                            <!-- Company Address -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Adres firmy <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    x-model="formData.company_address"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="ul. Przykładowa 123/45"
                                    required
                                >
                            </div>

                            <!-- City and Postal Code -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Miasto <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        x-model="formData.city"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        placeholder="Warszawa"
                                        required
                                    >
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Kod pocztowy <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        x-model="formData.postal_code"
                                        @input="formatPostalCode()"
                                        maxlength="6"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        placeholder="00-000"
                                        required
                                    >
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Individual Fields -->
                    <div x-show="accountType === 'individual'">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Adres <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    x-model="formData.address"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="ul. Przykładowa 123/45"
                                    required
                                >
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Miasto <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        x-model="formData.city"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        placeholder="Warszawa"
                                        required
                                    >
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Kod pocztowy <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        x-model="formData.postal_code"
                                        @input="formatPostalCode()"
                                        maxlength="6"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        placeholder="00-000"
                                        required
                                    >
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information (Both Types) -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            <span x-text="accountType === 'company' ? 'Dane osoby kontaktowej' : 'Twoje dane kontaktowe'"></span>
                        </h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Imię <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    x-model="formData.first_name"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="Jan"
                                    required
                                >
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Nazwisko <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    x-model="formData.last_name"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="Kowalski"
                                    required
                                >
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Email <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="email" 
                                    x-model="formData.email"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    :placeholder="accountType === 'company' ? 'kontakt@firma.pl' : 'jan@example.com'"
                                    required
                                >
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Telefon <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="tel" 
                                    x-model="formData.phone"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="+48 123 456 789"
                                    required
                                >
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between mt-6">
                        <button @click="prevStep()" class="text-gray-600 hover:text-gray-800 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                            Wstecz
                        </button>
                        <button @click="nextStep()" :disabled="!isStep2Valid()" class="bg-base-blue hover:bg-blue-700 disabled:bg-gray-400 text-white px-6 py-2 rounded-md font-medium transition-colors">
                            Dalej
                        </button>
                    </div>
                </div>

                <!-- Step 3: Password and Terms -->
                <div x-show="currentStep === 3" x-transition>
                    <form method="POST" :action="getFormAction()" @submit="loading = true">
                        @csrf
                        <input type="hidden" name="account_type" x-model="accountType">
                        
                        <!-- Hidden fields for form data -->
                        <template x-if="accountType === 'company'">
                            <div>
                                <input type="hidden" name="company_name" x-model="formData.company_name">
                                <input type="hidden" name="nip" x-model="formData.nip">
                                <input type="hidden" name="company_address" x-model="formData.company_address">
                            </div>
                        </template>
                        <template x-if="accountType === 'individual'">
                            <div>
                                <input type="hidden" name="address" x-model="formData.address">
                            </div>
                        </template>
                        
                        <input type="hidden" name="city" x-model="formData.city">
                        <input type="hidden" name="postal_code" x-model="formData.postal_code">
                        <input type="hidden" name="first_name" x-model="formData.first_name">
                        <input type="hidden" name="last_name" x-model="formData.last_name">
                        <input type="hidden" name="email" x-model="formData.email">
                        <input type="hidden" name="phone" x-model="formData.phone">

                        <div class="flex items-center justify-between mb-6">
                            <button type="button" @click="prevStep()" class="text-gray-600 hover:text-gray-800 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                                Wstecz
                            </button>
                            <h2 class="text-xl font-semibold text-gray-900">Hasło i regulamin</h2>
                            <div></div>
                        </div>

                        <!-- Password Fields -->
                        <div class="space-y-4 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Hasło <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input 
                                        :type="showPassword ? 'text' : 'password'"
                                        name="password"
                                        x-model="formData.password"
                                        @input="checkPasswordStrength()"
                                        class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        placeholder="min. 8 znaków"
                                        required
                                    >
                                    <button 
                                        type="button" 
                                        @click="showPassword = !showPassword"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center"
                                    >
                                        <svg x-show="!showPassword" class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        <svg x-show="showPassword" class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/>
                                        </svg>
                                    </button>
                                </div>
                                <!-- Password Strength -->
                                <div x-show="formData.password.length > 0" class="mt-2">
                                    <div class="flex space-x-1">
                                        <div class="h-1 flex-1 rounded" :class="passwordStrength >= 1 ? 'bg-red-400' : 'bg-gray-200'"></div>
                                        <div class="h-1 flex-1 rounded" :class="passwordStrength >= 2 ? 'bg-yellow-400' : 'bg-gray-200'"></div>
                                        <div class="h-1 flex-1 rounded" :class="passwordStrength >= 3 ? 'bg-green-400' : 'bg-gray-200'"></div>
                                    </div>
                                    <p class="text-xs text-gray-600 mt-1" x-text="passwordStrengthText"></p>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Potwierdź hasło <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    :type="showPasswordConfirm ? 'text' : 'password'"
                                    name="password_confirmation"
                                    x-model="formData.password_confirmation"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="powtórz hasło"
                                    required
                                >
                                <div x-show="formData.password_confirmation.length > 0" class="mt-1">
                                    <p class="text-xs" :class="formData.password === formData.password_confirmation ? 'text-green-600' : 'text-red-600'">
                                        <span x-show="formData.password === formData.password_confirmation">✓ Hasła są identyczne</span>
                                        <span x-show="formData.password !== formData.password_confirmation">✗ Hasła nie są identyczne</span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Terms -->
                        <div class="space-y-3 mb-6">
                            <label class="flex items-start">
                                <input type="checkbox" name="terms_accepted" x-model="formData.terms_accepted" class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" required>
                                <span class="ml-2 text-sm text-gray-700">
                                    Akceptuję <a href="#" class="text-blue-600 hover:text-blue-500">Regulamin</a> oraz 
                                    <a href="#" class="text-blue-600 hover:text-blue-500">Politykę Prywatności</a> <span class="text-red-500">*</span>
                                </span>
                            </label>

                            <label class="flex items-start">
                                <input type="checkbox" name="privacy_accepted" x-model="formData.privacy_accepted" class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" required>
                                <span class="ml-2 text-sm text-gray-700">
                                    Wyrażam zgodę na przetwarzanie moich danych osobowych zgodnie z RODO <span class="text-red-500">*</span>
                                </span>
                            </label>

                            <label class="flex items-start">
                                <input type="checkbox" name="marketing_accepted" x-model="formData.marketing_accepted" class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <span class="ml-2 text-sm text-gray-700">
                                    Wyrażam zgodę na otrzymywanie informacji marketingowych (opcjonalnie)
                                </span>
                            </label>
                        </div>

                        <!-- Submit -->
                        <div class="flex justify-between items-center">
                            <button type="button" @click="prevStep()" class="text-gray-600 hover:text-gray-800 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                                Wstecz
                            </button>
                            <button 
                                type="submit"
                                :disabled="loading || !canSubmit"
                                class="bg-base-blue hover:bg-blue-700 disabled:bg-gray-400 text-white px-8 py-3 rounded-md font-medium transition-colors flex items-center"
                            >
                                <span x-show="!loading">
                                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span x-text="accountType === 'company' ? 'Załóż konto firmowe' : 'Załóż konto indywidualne'"></span>
                                </span>
                                <span x-show="loading" class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Tworzenie konta...
                                </span>
                            </button>
                        </div>

                        <p class="text-xs text-gray-500 text-center mt-4">
                            Po rejestracji Twoje konto zostanie poddane weryfikacji (1-2 dni robocze)
                        </p>
                    </form>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center mt-6">
                <p class="text-blue-100">
                    Masz już konto?
                    <a href="{{ route('customer.login') }}" class="text-white font-medium hover:text-blue-200 transition-colors">Zaloguj się</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('registrationForm', () => ({
        // Steps
        currentStep: 1,
        totalSteps: 3,
        
        // Account type
        accountType: '',
        
        // Form data
        formData: {
            company_name: '',
            nip: '',
            company_address: '',
            address: '',
            city: '',
            postal_code: '',
            first_name: '',
            last_name: '',
            email: '',
            phone: '',
            password: '',
            password_confirmation: '',
            terms_accepted: false,
            privacy_accepted: false,
            marketing_accepted: false
        },
        
        // UI state
        loading: false,
        showPassword: false,
        showPasswordConfirm: false,
        passwordStrength: 0,
        passwordStrengthText: '',
        gusLoading: false,
        
        get progress() {
            return (this.currentStep / this.totalSteps) * 100;
        },
        
        get canSubmit() {
            const base = this.formData.first_name && this.formData.last_name && this.formData.email && 
                        this.formData.phone && this.formData.password && this.formData.password_confirmation && 
                        this.formData.password === this.formData.password_confirmation &&
                        this.formData.terms_accepted && this.formData.privacy_accepted;
            
            if (this.accountType === 'company') {
                return base && this.formData.company_name && this.formData.company_address && 
                       this.formData.city && this.formData.postal_code;
            } else {
                return base && this.formData.address && this.formData.city && this.formData.postal_code;
            }
        },
        
        selectAccountType(type) {
            this.accountType = type;
        },
        
        nextStep() {
            if (this.currentStep < this.totalSteps) {
                this.currentStep++;
            }
        },
        
        prevStep() {
            if (this.currentStep > 1) {
                this.currentStep--;
            }
        },
        
        isStep2Valid() {
            if (this.accountType === 'company') {
                return this.formData.company_name && this.formData.company_address && 
                       this.formData.city && this.formData.postal_code &&
                       this.formData.first_name && this.formData.last_name && 
                       this.formData.email && this.formData.phone;
            } else {
                return this.formData.address && this.formData.city && this.formData.postal_code &&
                       this.formData.first_name && this.formData.last_name && 
                       this.formData.email && this.formData.phone;
            }
        },
        
        getFormAction() {
            return this.accountType === 'company' 
                ? "{{ route('customer.register.company') }}"
                : "{{ route('customer.register.individual') }}";
        },
        
        formatNip() {
            let value = this.formData.nip.replace(/\D/g, '');
            if (value.length > 10) {
                value = value.substring(0, 10);
            }
            this.formData.nip = value;
        },
        
        formatPostalCode() {
            let value = this.formData.postal_code.replace(/[^0-9-]/g, '');
            if (value.length >= 2 && value.indexOf('-') === -1) {
                value = value.substring(0, 2) + '-' + value.substring(2);
            }
            if (value.length > 6) {
                value = value.substring(0, 6);
            }
            this.formData.postal_code = value;
        },
        
        checkPasswordStrength() {
            const password = this.formData.password;
            let strength = 0;
            let text = '';
            
            if (password.length >= 8) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^A-Za-z0-9]/.test(password)) strength++;
            
            switch(strength) {
                case 0:
                case 1:
                    this.passwordStrength = 1;
                    text = 'Słabe hasło';
                    break;
                case 2:
                    this.passwordStrength = 2;
                    text = 'Średnie hasło';
                    break;
                case 3:
                case 4:
                    this.passwordStrength = 3;
                    text = 'Silne hasło';
                    break;
            }
            
            this.passwordStrengthText = text;
        },
        
        async fetchFromGUS() {
            if (!this.formData.nip || this.formData.nip.length < 10) {
                alert('Wpisz prawidłowy NIP (10 cyfr)');
                return;
            }
            
            this.gusLoading = true;
            
            try {
                const response = await fetch('/api/gus/company/' + this.formData.nip);
                const data = await response.json();
                
                if (data.success) {
                    this.formData.company_name = data.data.name || '';
                    this.formData.company_address = data.data.address || '';
                    this.formData.city = data.data.city || '';
                    this.formData.postal_code = data.data.postal_code || '';
                    
                    alert('Dane zostały pobrane z GUS');
                } else {
                    alert('Nie udało się pobrać danych z GUS: ' + (data.message || 'Nieznany błąd'));
                }
            } catch (error) {
                alert('Błąd połączenia z GUS');
            }
            
            this.gusLoading = false;
        }
    }))
});
</script>
@endpush