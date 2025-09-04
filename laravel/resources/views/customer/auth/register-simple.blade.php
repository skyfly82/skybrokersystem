@extends('layouts.auth')

@section('title', 'Rejestracja Konto')
@section('header', 'Załóż konto w SkyBroker')
@section('description', 'Wybierz typ konta i rozpocznij współpracę z nami')

@section('content')
<div x-data="registrationForm" class="space-y-6">
    <!-- Account Type Toggle -->
    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
        <h3 class="text-lg font-medium text-gray-900 mb-4 text-center">Wybierz typ konta</h3>
        <div class="flex justify-center">
            <div class="bg-white rounded-lg p-1 flex border">
                <button 
                    type="button" 
                    id="btn-company"
                    onclick="switchToCompany()"
                    class="px-6 py-2 rounded-md font-medium transition-colors duration-200 bg-primary-600 text-white"
                >
                    <i class="fas fa-building mr-2"></i>Klient Firmowy
                </button>
                <button 
                    type="button" 
                    id="btn-individual"
                    onclick="switchToIndividual()"
                    class="px-6 py-2 rounded-md font-medium transition-colors duration-200 text-gray-500 hover:text-gray-700"
                >
                    <i class="fas fa-user mr-2"></i>Klient Indywidualny
                </button>
            </div>
        </div>
    </div>

    <!-- Social Media Login (Individual Only) -->
    <div id="social-section" style="display: none;" class="bg-blue-50 p-4 rounded-lg border border-blue-200">
        <h3 class="text-lg font-medium text-blue-900 mb-4 text-center">Szybka rejestracja przez social media</h3>
        <div class="flex justify-center space-x-3">
            <!-- Google -->
            <a href="{{ route('customer.auth.social', 'google') }}?type=individual" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
                Google
            </a>

            <!-- Facebook -->
            <a href="{{ route('customer.auth.social', 'facebook') }}?type=individual" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                <svg class="w-5 h-5 mr-2" fill="#1877F2" viewBox="0 0 24 24">
                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                </svg>
                Facebook
            </a>
        </div>
        <p class="text-center text-sm text-blue-700 mt-3">Unikaj długiej rejestracji - zaloguj się przez social media!</p>
        
        <div class="text-center mt-4">
            <div class="relative">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-blue-300" />
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-blue-50 text-blue-600">lub wypełnij formularz</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Registration Form -->
    <form method="POST" action="{{ route('customer.register.company') }}" class="space-y-6">
        @csrf
        <input type="hidden" name="account_type" value="company">
        
        <!-- Company Information (Company Only) -->
        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                <svg class="h-5 w-5 text-primary-600 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m2.25-18v18m13.5-18v18m2.25-18v18M6.75 9h.75m-.75 3h.75M6.75 15h.75m-.75 3h.75M10.5 9h.75m-.75 3h.75m-.75 3h.75m-.75 3h.75M14.25 9h.75m-.75 3h.75m-.75 3h.75m-.75 3h.75" />
                </svg>
                Informacje o firmie
            </h3>
            
            <div class="grid grid-cols-1 gap-4">
                <!-- Company Name -->
                <div>
                    <label for="company_name" class="block text-sm font-medium leading-6 text-gray-900">
                        Nazwa firmy <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-2">
                        <input 
                            id="company_name" 
                            name="company_name" 
                            type="text" 
                            value="{{ old('company_name') }}"
                            class="block w-full rounded-md border-0 py-2.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6 @error('company_name') ring-red-300 focus:ring-red-500 @enderror"
                            placeholder="np. ABC Sp. z o.o."
                            x-model="formData.company_name"
                        >
                    </div>
                    @error('company_name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- NIP with GUS Button -->
                <div>
                    <div class="flex items-center justify-between">
                        <label for="nip" class="block text-sm font-medium leading-6 text-gray-900">
                            NIP
                        </label>
                        <button 
                            type="button" 
                            @click="fetchFromGUS()" 
                            :disabled="!formData.nip || formData.nip.length < 10 || gusLoading"
                            class="text-sm bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white px-3 py-1 rounded-md transition-colors duration-200"
                        >
                            <span x-show="!gusLoading">
                                <i class="fas fa-download mr-1"></i>Pobierz z GUS
                            </span>
                            <span x-show="gusLoading">
                                <i class="fas fa-spinner fa-spin mr-1"></i>Pobieranie...
                            </span>
                        </button>
                    </div>
                    <div class="mt-2">
                        <input 
                            id="nip" 
                            name="nip" 
                            type="text" 
                            value="{{ old('nip') }}"
                            class="block w-full rounded-md border-0 py-2.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6 @error('nip') ring-red-300 focus:ring-red-500 @enderror"
                            placeholder="1234567890"
                            x-model="formData.nip"
                            @input="formatNip()"
                            maxlength="12"
                        >
                    </div>
                    @error('nip')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Wpisz NIP i kliknij "Pobierz z GUS" aby automatycznie uzupełnić dane</p>
                </div>

                <!-- Company Address -->
                <div>
                    <label for="company_address" class="block text-sm font-medium leading-6 text-gray-900">
                        Adres firmy <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-2">
                        <input 
                            id="company_address" 
                            name="company_address" 
                            type="text" 
                            value="{{ old('company_address') }}"
                            class="block w-full rounded-md border-0 py-2.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6 @error('company_address') ring-red-300 focus:ring-red-500 @enderror"
                            placeholder="ul. Przykładowa 123/45"
                            x-model="formData.company_address"
                        >
                    </div>
                    @error('company_address')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- City and Postal Code -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="city" class="block text-sm font-medium leading-6 text-gray-900">
                            Miasto <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-2">
                            <input 
                                id="city" 
                                name="city" 
                                type="text" 
                                value="{{ old('city') }}"
                                class="block w-full rounded-md border-0 py-2.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6 @error('city') ring-red-300 focus:ring-red-500 @enderror"
                                placeholder="Warszawa"
                                x-model="formData.city"
                            >
                        </div>
                        @error('city')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="postal_code" class="block text-sm font-medium leading-6 text-gray-900">
                            Kod pocztowy <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-2">
                            <input 
                                id="postal_code" 
                                name="postal_code" 
                                type="text" 
                                value="{{ old('postal_code') }}"
                                class="block w-full rounded-md border-0 py-2.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6 @error('postal_code') ring-red-300 focus:ring-red-500 @enderror"
                                placeholder="00-000"
                                x-model="formData.postal_code"
                                @input="formatPostalCode()"
                                maxlength="6"
                            >
                        </div>
                        @error('postal_code')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Individual Information (Individual Only) -->
        <div id="individual-section" style="display: none;" class="bg-blue-50 p-4 rounded-lg border border-blue-200">
            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                <svg class="h-5 w-5 text-blue-600 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                </svg>
                Twoje dane
            </h3>

            <!-- Individual Address -->
            <div>
                <label for="individual_address" class="block text-sm font-medium leading-6 text-gray-900">
                    Adres <span class="text-red-500">*</span>
                </label>
                <div class="mt-2">
                    <input 
                        id="individual_address" 
                        name="address" 
                        type="text" 
                        value="{{ old('address') }}"
                        class="block w-full rounded-md border-0 py-2.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6"
                        placeholder="ul. Przykładowa 123/45"
                        x-model="formData.address"
                    >
                </div>
            </div>
        </div>

        <!-- Common Contact Information -->
        <div :class="accountType === 'company' ? 'bg-gray-50' : 'bg-blue-50'" class="p-4 rounded-lg border" :class="accountType === 'company' ? 'border-gray-200' : 'border-blue-200'">
            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                <svg class="h-5 w-5 mr-2" :class="accountType === 'company' ? 'text-primary-600' : 'text-blue-600'" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                </svg>
                <span x-text="accountType === 'company' ? 'Dane osoby kontaktowej' : 'Twoje dane'"></span>
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- First Name -->
                <div>
                    <label for="first_name" class="block text-sm font-medium leading-6 text-gray-900">
                        Imię <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-2">
                        <input 
                            id="first_name" 
                            name="first_name" 
                            type="text" 
                            required 
                            value="{{ old('first_name') }}"
                            :class="accountType === 'company' ? 'focus:ring-primary-600' : 'focus:ring-blue-600'"
                            class="block w-full rounded-md border-0 py-2.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset sm:text-sm sm:leading-6"
                            placeholder="Jan"
                            x-model="formData.first_name"
                        >
                    </div>
                    @error('first_name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Last Name -->
                <div>
                    <label for="last_name" class="block text-sm font-medium leading-6 text-gray-900">
                        Nazwisko <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-2">
                        <input 
                            id="last_name" 
                            name="last_name" 
                            type="text" 
                            required 
                            value="{{ old('last_name') }}"
                            :class="accountType === 'company' ? 'focus:ring-primary-600' : 'focus:ring-blue-600'"
                            class="block w-full rounded-md border-0 py-2.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset sm:text-sm sm:leading-6"
                            placeholder="Kowalski"
                            x-model="formData.last_name"
                        >
                    </div>
                    @error('last_name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium leading-6 text-gray-900">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-2">
                        <input 
                            id="email" 
                            name="email" 
                            type="email" 
                            required 
                            value="{{ old('email') }}"
                            :class="accountType === 'company' ? 'focus:ring-primary-600' : 'focus:ring-blue-600'"
                            class="block w-full rounded-md border-0 py-2.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset sm:text-sm sm:leading-6"
                            :placeholder="accountType === 'company' ? 'kontakt@firma.pl' : 'jan@example.com'"
                            x-model="formData.email"
                        >
                    </div>
                    @error('email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-medium leading-6 text-gray-900">
                        Telefon <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-2">
                        <input 
                            id="phone" 
                            name="phone" 
                            type="tel" 
                            required 
                            value="{{ old('phone') }}"
                            :class="accountType === 'company' ? 'focus:ring-primary-600' : 'focus:ring-blue-600'"
                            class="block w-full rounded-md border-0 py-2.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset sm:text-sm sm:leading-6"
                            placeholder="+48 123 456 789"
                            x-model="formData.phone"
                        >
                    </div>
                    @error('phone')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Password Section -->
        <div :class="accountType === 'company' ? 'bg-gray-50' : 'bg-blue-50'" class="p-4 rounded-lg border" :class="accountType === 'company' ? 'border-gray-200' : 'border-blue-200'">
            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                <svg class="h-5 w-5 mr-2" :class="accountType === 'company' ? 'text-primary-600' : 'text-blue-600'" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                </svg>
                Hasło dostępu
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium leading-6 text-gray-900">
                        Hasło <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-2 relative">
                        <input 
                            id="password" 
                            name="password" 
                            :type="showPassword ? 'text' : 'password'" 
                            required
                            :class="accountType === 'company' ? 'focus:ring-primary-600' : 'focus:ring-blue-600'"
                            class="block w-full rounded-md border-0 py-2.5 px-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset sm:text-sm sm:leading-6"
                            placeholder="min. 8 znaków"
                            x-model="formData.password"
                            @input="checkPasswordStrength()"
                        >
                        <button 
                            type="button" 
                            class="absolute inset-y-0 right-0 flex items-center pr-3"
                            @click="showPassword = !showPassword"
                        >
                            <svg x-show="!showPassword" class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <svg x-show="showPassword" class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="display: none;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 11-4.243-4.243m4.242 4.242L9.88 9.88" />
                            </svg>
                        </button>
                    </div>
                    <!-- Password Strength Indicator -->
                    <div x-show="formData.password.length > 0" class="mt-2">
                        <div class="flex space-x-1">
                            <div class="h-1 flex-1 rounded" :class="passwordStrength >= 1 ? 'bg-red-400' : 'bg-gray-200'"></div>
                            <div class="h-1 flex-1 rounded" :class="passwordStrength >= 2 ? 'bg-yellow-400' : 'bg-gray-200'"></div>
                            <div class="h-1 flex-1 rounded" :class="passwordStrength >= 3 ? 'bg-green-400' : 'bg-gray-200'"></div>
                        </div>
                        <p class="text-xs text-gray-600 mt-1" x-text="passwordStrengthText"></p>
                    </div>
                    @error('password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Confirmation -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium leading-6 text-gray-900">
                        Potwierdź hasło <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-2 relative">
                        <input 
                            id="password_confirmation" 
                            name="password_confirmation" 
                            :type="showPasswordConfirm ? 'text' : 'password'" 
                            required
                            :class="accountType === 'company' ? 'focus:ring-primary-600' : 'focus:ring-blue-600'"
                            class="block w-full rounded-md border-0 py-2.5 px-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset sm:text-sm sm:leading-6"
                            placeholder="powtórz hasło"
                            x-model="formData.password_confirmation"
                        >
                        <button 
                            type="button" 
                            class="absolute inset-y-0 right-0 flex items-center pr-3"
                            @click="showPasswordConfirm = !showPasswordConfirm"
                        >
                            <svg x-show="!showPasswordConfirm" class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <svg x-show="showPasswordConfirm" class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="display: none;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 11-4.243-4.243m4.242 4.242L9.88 9.88" />
                            </svg>
                        </button>
                    </div>
                    <div x-show="formData.password_confirmation.length > 0" class="mt-1">
                        <p class="text-xs" :class="formData.password === formData.password_confirmation ? 'text-green-600' : 'text-red-600'">
                            <span x-show="formData.password === formData.password_confirmation">✓ Hasła są identyczne</span>
                            <span x-show="formData.password !== formData.password_confirmation">✗ Hasła nie są identyczne</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Agreements -->
        <div class="space-y-4">
            <div class="flex items-start">
                <div class="flex items-center h-5">
                    <input 
                        id="terms_accepted" 
                        name="terms_accepted" 
                        type="checkbox" 
                        required 
                        :class="accountType === 'company' ? 'text-primary-600 focus:ring-primary-600' : 'text-blue-600 focus:ring-blue-600'"
                        class="h-4 w-4 rounded border-gray-300"
                        x-model="formData.terms_accepted"
                    >
                </div>
                <div class="ml-3 text-sm">
                    <label for="terms_accepted" class="font-medium text-gray-700">
                        Akceptuję <a href="#" :class="accountType === 'company' ? 'text-primary-600 hover:text-primary-500' : 'text-blue-600 hover:text-blue-500'">Regulamin</a> oraz 
                        <a href="#" :class="accountType === 'company' ? 'text-primary-600 hover:text-primary-500' : 'text-blue-600 hover:text-blue-500'">Politykę Prywatności</a> <span class="text-red-500">*</span>
                    </label>
                </div>
            </div>
            @error('terms_accepted')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror

            <div class="flex items-start">
                <div class="flex items-center h-5">
                    <input 
                        id="privacy_accepted" 
                        name="privacy_accepted" 
                        type="checkbox" 
                        required 
                        :class="accountType === 'company' ? 'text-primary-600 focus:ring-primary-600' : 'text-blue-600 focus:ring-blue-600'"
                        class="h-4 w-4 rounded border-gray-300"
                        x-model="formData.privacy_accepted"
                    >
                </div>
                <div class="ml-3 text-sm">
                    <label for="privacy_accepted" class="font-medium text-gray-700">
                        Wyrażam zgodę na przetwarzanie moich danych osobowych zgodnie z RODO <span class="text-red-500">*</span>
                    </label>
                </div>
            </div>
            @error('privacy_accepted')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror

            <div class="flex items-start">
                <div class="flex items-center h-5">
                    <input 
                        id="marketing_accepted" 
                        name="marketing_accepted" 
                        type="checkbox" 
                        :class="accountType === 'company' ? 'text-primary-600 focus:ring-primary-600' : 'text-blue-600 focus:ring-blue-600'"
                        class="h-4 w-4 rounded border-gray-300"
                        x-model="formData.marketing_accepted"
                    >
                </div>
                <div class="ml-3 text-sm">
                    <label for="marketing_accepted" class="font-medium text-gray-700">
                        Wyrażam zgodę na otrzymywanie informacji marketingowych (opcjonalnie)
                    </label>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div>
            <button 
                type="submit" 
                :class="accountType === 'company' ? 'bg-primary-600 hover:bg-primary-500 focus-visible:outline-primary-600' : 'bg-blue-600 hover:bg-blue-500 focus-visible:outline-blue-600'"
                class="flex w-full justify-center rounded-md px-3 py-3 text-sm font-semibold leading-6 text-white shadow-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200"
                :disabled="loading || !canSubmit"
            >
                <span x-show="!loading">
                    <span x-text="accountType === 'company' ? 'Załóż konto firmowe' : 'Załóż konto indywidualne'"></span>
                </span>
                <span x-show="loading" class="flex items-center">
                    <i class="fas fa-spinner fa-spin mr-2"></i>
                    Tworzenie konta...
                </span>
            </button>
            <p class="mt-2 text-xs text-gray-500 text-center">
                Po rejestracji Twoje konto zostanie poddane weryfikacji (1-2 dni robocze)
            </p>
        </div>
    </form>
</div>
@endsection

@section('footer')
<div class="text-center">
    <p class="text-sm text-gray-600">
        Masz już konto?
        <a href="{{ route('customer.login') }}" class="text-primary-600 hover:text-primary-500 font-medium">Zaloguj się</a>
    </p>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('registrationForm', () => ({
        // Account type
        accountType: 'company',
        
        // Form data
        formData: {
            company_name: '{{ old('company_name') }}',
            nip: '{{ old('nip') }}',
            company_address: '{{ old('company_address') }}',
            address: '{{ old('address') }}',
            city: '{{ old('city') }}',
            postal_code: '{{ old('postal_code') }}',
            first_name: '{{ old('first_name') }}',
            last_name: '{{ old('last_name') }}',
            email: '{{ old('email') }}',
            phone: '{{ old('phone') }}',
            password: '',
            password_confirmation: '',
            terms_accepted: {{ old('terms_accepted') ? 'true' : 'false' }},
            privacy_accepted: {{ old('privacy_accepted') ? 'true' : 'false' }},
            marketing_accepted: {{ old('marketing_accepted') ? 'true' : 'false' }}
        },
        
        // UI state
        loading: false,
        showPassword: false,
        showPasswordConfirm: false,
        passwordStrength: 0,
        passwordStrengthText: '',
        gusLoading: false,
        
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
        
        getFormAction() {
            return this.accountType === 'company' 
                ? "{{ route('customer.register.company') }}"
                : "{{ route('customer.register.individual') }}";
        },
        
        formatNip() {
            // Remove all non-digits
            let value = this.formData.nip.replace(/\D/g, '');
            // Limit to 10 digits
            if (value.length > 10) {
                value = value.substring(0, 10);
            }
            this.formData.nip = value;
        },
        
        formatPostalCode() {
            // Remove all non-digits and hyphens
            let value = this.formData.postal_code.replace(/[^0-9-]/g, '');
            
            // Add hyphen after 2 digits if not present
            if (value.length >= 2 && value.indexOf('-') === -1) {
                value = value.substring(0, 2) + '-' + value.substring(2);
            }
            
            // Limit to XX-XXX format
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
        
        // Simple vanilla JS functions for form switching
        let currentAccountType = 'company';

        function switchToCompany() {
            currentAccountType = 'company';
            
            // Update buttons
            document.getElementById('btn-company').className = 'px-6 py-2 rounded-md font-medium transition-colors duration-200 bg-primary-600 text-white';
            document.getElementById('btn-individual').className = 'px-6 py-2 rounded-md font-medium transition-colors duration-200 text-gray-500 hover:text-gray-700';
            
            // Show/hide sections
            document.getElementById('company-section').style.display = 'block';
            document.getElementById('individual-section').style.display = 'none';
            document.getElementById('social-section').style.display = 'none';
            
            // Update form action and hidden field
            document.getElementById('registration-form').action = "{{ route('customer.register.company') }}";
            document.getElementById('account-type').value = 'company';
        }

        function switchToIndividual() {
            currentAccountType = 'individual';
            
            // Update buttons
            document.getElementById('btn-company').className = 'px-6 py-2 rounded-md font-medium transition-colors duration-200 text-gray-500 hover:text-gray-700';
            document.getElementById('btn-individual').className = 'px-6 py-2 rounded-md font-medium transition-colors duration-200 bg-blue-600 text-white';
            
            // Show/hide sections
            document.getElementById('company-section').style.display = 'none';
            document.getElementById('individual-section').style.display = 'block';
            document.getElementById('social-section').style.display = 'block';
            
            // Update form action and hidden field
            document.getElementById('registration-form').action = "{{ route('customer.register.individual') }}";
            document.getElementById('account-type').value = 'individual';
        }

        // Initialize - company is default (already visible)
        document.addEventListener('DOMContentLoaded', function() {
            switchToCompany();
        });
// Global functions for switching account types
window.switchToCompany = function() {
    // Update buttons
    document.getElementById('btn-company').className = 'px-6 py-2 rounded-md font-medium transition-colors duration-200 bg-primary-600 text-white';
    document.getElementById('btn-individual').className = 'px-6 py-2 rounded-md font-medium transition-colors duration-200 text-gray-500 hover:text-gray-700';
    
    // Show/hide sections
    document.getElementById('company-section').style.display = 'block';
    document.getElementById('individual-section').style.display = 'none';
    document.getElementById('social-section').style.display = 'none';
    
    // Update form action and hidden field
    document.getElementById('registration-form').action = "{{ route('customer.register.company') }}";
    document.getElementById('account-type').value = 'company';
};

window.switchToIndividual = function() {
    // Update buttons
    document.getElementById('btn-company').className = 'px-6 py-2 rounded-md font-medium transition-colors duration-200 text-gray-500 hover:text-gray-700';
    document.getElementById('btn-individual').className = 'px-6 py-2 rounded-md font-medium transition-colors duration-200 bg-blue-600 text-white';
    
    // Show/hide sections
    document.getElementById('company-section').style.display = 'none';
    document.getElementById('individual-section').style.display = 'block';
    document.getElementById('social-section').style.display = 'block';
    
    // Update form action and hidden field
    document.getElementById('registration-form').action = "{{ route('customer.register.individual') }}";
    document.getElementById('account-type').value = 'individual';
};

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    switchToCompany(); // Company is default
});
</script>
@endpush