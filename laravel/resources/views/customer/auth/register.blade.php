@extends('layouts.auth')

@section('title', 'Rejestracja')
@section('header', 'Załóż konto')
@section('description', 'Wybierz typ konta i rozpocznij współpracę z nami')

@section('content')
<!-- Account Type Toggle -->
<div class="mb-6 bg-white p-4 rounded-lg border border-gray-200">
    <h3 class="text-lg font-medium text-gray-900 mb-4 text-center">Wybierz typ konta</h3>
    <div class="flex justify-center">
        <div class="bg-gray-100 rounded-lg p-1 flex border">
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

<div id="individual-content" style="display: none;">
@if($type === 'individual')
    <!-- Individual Registration Form -->
    <div class="text-center mb-6">
        <h2 class="text-2xl font-bold text-blue-600">Konto Indywidualne</h2>
        <p class="text-gray-600 mt-2">Dla klientów prywatnych</p>
    </div>
    
    <!-- Social Login Options First for Individual -->
    <div class="mb-8 p-4 bg-blue-50 rounded-lg border border-blue-200">
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
    </div>

    <div class="text-center mb-6">
        <div class="relative">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-gray-300" />
            </div>
            <div class="relative flex justify-center text-sm">
                <span class="px-2 bg-white text-gray-500">lub wypełnij formularz</span>
            </div>
        </div>
    </div>
@endif
</div>

<div id="company-content">
<form method="POST" action="{{ route('customer.register.company') }}" class="space-y-6" x-data="customerRegister" @submit="loading = true" id="registration-form">
    @csrf
    
    <!-- Progress Bar -->
    <div class="mb-6 bg-white p-4 rounded-lg border border-gray-200">
        <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium text-gray-700">Krok 1 z 2</span>
            <span class="text-sm text-gray-500">50%</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2">
            <div class="bg-primary-600 h-2 rounded-full" style="width: 50%"></div>
        </div>
        <div class="flex justify-between mt-2 text-xs text-gray-500">
            <span class="text-primary-600 font-medium">Rejestracja</span>
            <span>Weryfikacja</span>
        </div>
    </div>

    <!-- Company Information Section -->
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
                        required 
                        value="{{ old('company_name') }}"
                        class="block w-full rounded-md border-0 py-2.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6 @error('company_name') ring-red-300 focus:ring-red-500 @enderror"
                        placeholder="np. ABC Sp. z o.o."
                        x-model="companyName"
                    >
                </div>
                @error('company_name')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- NIP -->
            <div>
                <div class="flex items-center justify-between">
                    <label for="nip" class="block text-sm font-medium leading-6 text-gray-900">NIP</label>
                    <button 
                        type="button" 
                        onclick="fetchFromGUS()" 
                        class="text-sm bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-md transition-colors duration-200"
                    >
                        <i class="fas fa-download mr-1"></i>Pobierz z GUS
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
                        x-model="nip"
                        @input="formatNip()"
                        maxlength="12"
                    >
                </div>
                @error('nip')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">Opcjonalnie - pomaga w weryfikacji</p>
            </div>

            <!-- Address -->
            <div>
                <label for="company_address" class="block text-sm font-medium leading-6 text-gray-900">
                    Ulica <span class="text-red-500">*</span>
                </label>
                <div class="mt-2">
                    <input 
                        id="company_address" 
                        name="company_address" 
                        type="text" 
                        required 
                        value="{{ old('company_address') }}"
                        class="block w-full rounded-md border-0 py-2.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6 @error('company_address') ring-red-300 focus:ring-red-500 @enderror"
                        placeholder="ul. Przykładowa"
                        x-model="address"
                    >
                </div>
                @error('company_address')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Building and Apartment Numbers -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="building_number" class="block text-sm font-medium leading-6 text-gray-900">
                        Nr budynku <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-2">
                        <input 
                            id="building_number" 
                            name="building_number" 
                            type="text" 
                            required
                            value="{{ old('building_number') }}"
                            class="block w-full rounded-md border-0 py-2.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6 @error('building_number') ring-red-300 focus:ring-red-500 @enderror"
                            placeholder="123"
                        >
                    </div>
                    @error('building_number')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="apartment_number" class="block text-sm font-medium leading-6 text-gray-900">
                        Nr lokalu
                    </label>
                    <div class="mt-2">
                        <input 
                            id="apartment_number" 
                            name="apartment_number" 
                            type="text" 
                            value="{{ old('apartment_number') }}"
                            class="block w-full rounded-md border-0 py-2.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6 @error('apartment_number') ring-red-300 focus:ring-red-500 @enderror"
                            placeholder="45"
                        >
                    </div>
                    @error('apartment_number')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
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
                            required 
                            value="{{ old('city') }}"
                            class="block w-full rounded-md border-0 py-2.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6 @error('city') ring-red-300 focus:ring-red-500 @enderror"
                            placeholder="Warszawa"
                            x-model="city"
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
                            required 
                            value="{{ old('postal_code') }}"
                            class="block w-full rounded-md border-0 py-2.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6 @error('postal_code') ring-red-300 focus:ring-red-500 @enderror"
                            placeholder="00-000"
                            x-model="postalCode"
                            @input="formatPostalCode()"
                            maxlength="6"
                        >
                    </div>
                    @error('postal_code')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Phone and Email -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                            class="block w-full rounded-md border-0 py-2.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6 @error('phone') ring-red-300 focus:ring-red-500 @enderror"
                            placeholder="+48 123 456 789"
                            x-model="phone"
                        >
                    </div>
                    @error('phone')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium leading-6 text-gray-900">
                        Email firmowy <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-2">
                        <input 
                            id="email" 
                            name="email" 
                            type="email" 
                            required 
                            value="{{ old('email') }}"
                            class="block w-full rounded-md border-0 py-2.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6 @error('email') ring-red-300 focus:ring-red-500 @enderror"
                            placeholder="kontakt@firma.pl"
                            x-model="email"
                        >
                    </div>
                    @error('email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <!-- User Account Section -->
    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
        <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
            <svg class="h-5 w-5 text-primary-600 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
            </svg>
            Dane osoby kontaktowej
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
                        class="block w-full rounded-md border-0 py-2.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6 @error('first_name') ring-red-300 focus:ring-red-500 @enderror"
                        placeholder="Jan"
                        x-model="firstName"
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
                        class="block w-full rounded-md border-0 py-2.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6 @error('last_name') ring-red-300 focus:ring-red-500 @enderror"
                        placeholder="Kowalski"
                        x-model="lastName"
                    >
                </div>
                @error('last_name')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

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
                        class="block w-full rounded-md border-0 py-2.5 px-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6 @error('password') ring-red-300 focus:ring-red-500 @enderror"
                        placeholder="min. 8 znaków"
                        x-model="password"
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
                <div x-show="password.length > 0" class="mt-2">
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
                        class="block w-full rounded-md border-0 py-2.5 px-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6"
                        placeholder="powtórz hasło"
                        x-model="passwordConfirmation"
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
                <div x-show="passwordConfirmation.length > 0" class="mt-1">
                    <p class="text-xs" :class="password === passwordConfirmation ? 'text-green-600' : 'text-red-600'">
                        <span x-show="password === passwordConfirmation">✓ Hasła są identyczne</span>
                        <span x-show="password !== passwordConfirmation">✗ Hasła nie są identyczne</span>
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
                    class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-600"
                    x-model="termsAccepted"
                >
            </div>
            <div class="ml-3 text-sm">
                <label for="terms_accepted" class="font-medium text-gray-700">
                    Akceptuję <a href="#" class="text-primary-600 hover:text-primary-500">Regulamin</a> oraz 
                    <a href="#" class="text-primary-600 hover:text-primary-500">Politykę Prywatności</a> <span class="text-red-500">*</span>
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
                    class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-600"
                    x-model="privacyAccepted"
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
                    class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-600"
                    x-model="marketingAccepted"
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
            class="flex w-full justify-center rounded-md bg-primary-600 px-3 py-3 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200"
            :disabled="loading || !canSubmit"
        >
            <span x-show="!loading">Załóż konto firmowe</span>
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

<!-- Individual Form (Copy of company form adapted for individual) -->
<div id="individual-form" style="display: none;">
<form method="POST" action="{{ route('customer.register.individual') }}" class="space-y-6">
    @csrf
    
    <!-- Progress Bar -->
    <div class="mb-6 bg-white p-4 rounded-lg border border-gray-200">
        <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium text-gray-700">Krok 1 z 2</span>
            <span class="text-sm text-gray-500">50%</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2">
            <div class="bg-blue-600 h-2 rounded-full" style="width: 50%"></div>
        </div>
        <div class="flex justify-between mt-2 text-xs text-gray-500">
            <span class="text-blue-600 font-medium">Rejestracja</span>
            <span>Weryfikacja</span>
        </div>
    </div>
    
    <!-- Individual Address -->
    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
        <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
            <svg class="h-5 w-5 text-primary-600 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
            </svg>
            Twój adres
        </h3>
        
        <div class="space-y-4">
            <div>
                <label for="individual_address" class="block text-sm font-medium leading-6 text-gray-900">
                    Ulica <span class="text-red-500">*</span>
                </label>
                <div class="mt-2">
                    <input 
                        id="individual_address" 
                        name="address" 
                        type="text" 
                        required
                        value="{{ old('address') }}"
                        class="block w-full rounded-md border-0 py-2.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6"
                        placeholder="ul. Przykładowa"
                    >
                </div>
            </div>

            <!-- Building and Apartment Numbers -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="individual_building_number" class="block text-sm font-medium leading-6 text-gray-900">
                        Nr budynku <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-2">
                        <input 
                            id="individual_building_number" 
                            name="building_number" 
                            type="text" 
                            required
                            value="{{ old('building_number') }}"
                            class="block w-full rounded-md border-0 py-2.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6"
                            placeholder="123"
                        >
                    </div>
                </div>

                <div>
                    <label for="individual_apartment_number" class="block text-sm font-medium leading-6 text-gray-900">
                        Nr lokalu
                    </label>
                    <div class="mt-2">
                        <input 
                            id="individual_apartment_number" 
                            name="apartment_number" 
                            type="text" 
                            value="{{ old('apartment_number') }}"
                            class="block w-full rounded-md border-0 py-2.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6"
                            placeholder="45"
                        >
                    </div>
                </div>
            </div>

            <!-- City and Postal Code -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="individual_city" class="block text-sm font-medium leading-6 text-gray-900">
                        Miasto <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-2">
                        <input 
                            id="individual_city" 
                            name="city" 
                            type="text" 
                            required
                            value="{{ old('city') }}"
                            class="block w-full rounded-md border-0 py-2.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6"
                            placeholder="Warszawa"
                        >
                    </div>
                </div>

                <div>
                    <label for="individual_postal_code" class="block text-sm font-medium leading-6 text-gray-900">
                        Kod pocztowy <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-2">
                        <input 
                            id="individual_postal_code" 
                            name="postal_code" 
                            type="text" 
                            required
                            value="{{ old('postal_code') }}"
                            class="block w-full rounded-md border-0 py-2.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6"
                            placeholder="00-000"
                            maxlength="6"
                        >
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Common fields (name, email, etc) -->
    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Twoje dane kontaktowe</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="individual_first_name" class="block text-sm font-medium leading-6 text-gray-900">
                    Imię <span class="text-red-500">*</span>
                </label>
                <input 
                    id="individual_first_name" 
                    name="first_name" 
                    type="text" 
                    required
                    value="{{ old('first_name') }}"
                    class="mt-2 block w-full rounded-md border-0 py-2.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6"
                    placeholder="Jan"
                >
            </div>
            
            <div>
                <label for="individual_last_name" class="block text-sm font-medium leading-6 text-gray-900">
                    Nazwisko <span class="text-red-500">*</span>
                </label>
                <input 
                    id="individual_last_name" 
                    name="last_name" 
                    type="text" 
                    required
                    value="{{ old('last_name') }}"
                    class="mt-2 block w-full rounded-md border-0 py-2.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6"
                    placeholder="Kowalski"
                >
            </div>
            
            <div>
                <label for="individual_email" class="block text-sm font-medium leading-6 text-gray-900">
                    Email <span class="text-red-500">*</span>
                </label>
                <input 
                    id="individual_email" 
                    name="email" 
                    type="email" 
                    required
                    value="{{ old('email') }}"
                    class="mt-2 block w-full rounded-md border-0 py-2.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6"
                    placeholder="jan@example.com"
                >
            </div>
            
            <div>
                <label for="individual_phone" class="block text-sm font-medium leading-6 text-gray-900">
                    Telefon <span class="text-red-500">*</span>
                </label>
                <input 
                    id="individual_phone" 
                    name="phone" 
                    type="tel" 
                    required
                    value="{{ old('phone') }}"
                    class="mt-2 block w-full rounded-md border-0 py-2.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6"
                    placeholder="+48 123 456 789"
                >
            </div>
            
            <div>
                <label for="individual_password" class="block text-sm font-medium leading-6 text-gray-900">
                    Hasło <span class="text-red-500">*</span>
                </label>
                <input 
                    id="individual_password" 
                    name="password" 
                    type="password" 
                    required
                    class="mt-2 block w-full rounded-md border-0 py-2.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6"
                    placeholder="min. 8 znaków"
                >
            </div>
            
            <div>
                <label for="individual_password_confirmation" class="block text-sm font-medium leading-6 text-gray-900">
                    Potwierdź hasło <span class="text-red-500">*</span>
                </label>
                <input 
                    id="individual_password_confirmation" 
                    name="password_confirmation" 
                    type="password" 
                    required
                    class="mt-2 block w-full rounded-md border-0 py-2.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6"
                    placeholder="powtórz hasło"
                >
            </div>
        </div>
    </div>
    
    <!-- Terms -->
    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
        <div class="space-y-3">
            <label class="flex items-start">
                <input type="checkbox" name="terms_accepted" required class="mt-1 h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                <span class="ml-2 text-sm text-gray-700">
                    Akceptuję <a href="#" class="text-blue-600 hover:text-blue-500">Regulamin</a> oraz 
                    <a href="#" class="text-blue-600 hover:text-blue-500">Politykę Prywatności</a> <span class="text-red-500">*</span>
                </span>
            </label>
            <label class="flex items-start">
                <input type="checkbox" name="privacy_accepted" required class="mt-1 h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                <span class="ml-2 text-sm text-gray-700">
                    Wyrażam zgodę na przetwarzanie moich danych osobowych zgodnie z RODO <span class="text-red-500">*</span>
                </span>
            </label>
        </div>
    </div>

    <!-- Submit Button -->
    <div>
        <button 
            type="submit" 
            class="flex w-full justify-center rounded-md bg-primary-600 px-3 py-3 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200"
        >
            Załóż konto indywidualne
        </button>
    </div>
</form>
</div>
@endsection

@section('footer')
<div class="text-center">
    <p class="text-sm text-gray-600">
        Masz już konto?
    </p>
    <a href="{{ route('customer.login') }}" class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-all duration-200 mt-2">
        <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
        </svg>
        Zaloguj się
    </a>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('customerRegister', () => ({
        // Form data
        companyName: '{{ old('company_name') }}',
        nip: '{{ old('nip') }}',
        address: '{{ old('company_address') }}',
        city: '{{ old('city') }}',
        postalCode: '{{ old('postal_code') }}',
        phone: '{{ old('phone') }}',
        email: '{{ old('email') }}',
        firstName: '{{ old('first_name') }}',
        lastName: '{{ old('last_name') }}',
        password: '',
        passwordConfirmation: '',
        
        // UI state
        loading: false,
        showPassword: false,
        showPasswordConfirm: false,
        passwordStrength: 0,
        passwordStrengthText: '',
        
        // Agreements
        termsAccepted: {{ old('terms_accepted') ? 'true' : 'false' }},
        privacyAccepted: {{ old('privacy_accepted') ? 'true' : 'false' }},
        marketingAccepted: {{ old('marketing_accepted') ? 'true' : 'false' }},
        
        get canSubmit() {
            return this.companyName && this.address && this.city && this.postalCode && 
                   this.phone && this.email && this.firstName && this.lastName && 
                   this.password && this.passwordConfirmation && 
                   this.password === this.passwordConfirmation &&
                   this.termsAccepted && this.privacyAccepted;
        },
        
        formatNip() {
            // Remove all non-digits
            let value = this.nip.replace(/\D/g, '');
            // Limit to 10 digits
            if (value.length > 10) {
                value = value.substring(0, 10);
            }
            this.nip = value;
        },
        
        formatPostalCode() {
            // Remove all non-digits and hyphens
            let value = this.postalCode.replace(/[^0-9-]/g, '');
            
            // Add hyphen after 2 digits if not present
            if (value.length >= 2 && value.indexOf('-') === -1) {
                value = value.substring(0, 2) + '-' + value.substring(2);
            }
            
            // Limit to XX-XXX format
            if (value.length > 6) {
                value = value.substring(0, 6);
            }
            
            this.postalCode = value;
        },
        
        checkPasswordStrength() {
            const password = this.password;
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
            const nip = document.getElementById('nip').value.replace(/\D/g, '');
            
            if (!nip || nip.length < 10) {
                alert('Wpisz prawidłowy NIP (10 cyfr)');
                return;
            }
            
            try {
                const response = await fetch('/api/gus/company/' + nip);
                const data = await response.json();
                
                if (data.success) {
                    document.getElementById('company_name').value = data.data.name || '';
                    document.getElementById('company_address').value = data.data.address || '';
                    document.getElementById('city').value = data.data.city || '';
                    document.getElementById('postal_code').value = data.data.postal_code || '';
                    
                    alert('Dane zostały pobrane z GUS');
                } else {
                    alert('Nie udało się pobrać danych z GUS: ' + (data.message || 'Nieznany błąd'));
                }
            } catch (error) {
                alert('Błąd połączenia z GUS');
            }
        }
    }))
});

// Switching functions
function switchToCompany() {
    document.getElementById('btn-company').className = 'px-6 py-2 rounded-md font-medium transition-colors duration-200 bg-primary-600 text-white';
    document.getElementById('btn-individual').className = 'px-6 py-2 rounded-md font-medium transition-colors duration-200 text-gray-500 hover:text-gray-700';
    
    document.getElementById('company-content').style.display = 'block';
    document.getElementById('individual-content').style.display = 'none';
    document.getElementById('individual-form').style.display = 'none';
}

function switchToIndividual() {
    document.getElementById('btn-company').className = 'px-6 py-2 rounded-md font-medium transition-colors duration-200 text-gray-500 hover:text-gray-700';
    document.getElementById('btn-individual').className = 'px-6 py-2 rounded-md font-medium transition-colors duration-200 bg-blue-600 text-white';
    
    document.getElementById('company-content').style.display = 'none';
    document.getElementById('individual-content').style.display = 'block';
    document.getElementById('individual-form').style.display = 'block';
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    switchToCompany();
});

// Global function for GUS button
async function fetchFromGUS() {
    const nip = document.getElementById('nip').value.replace(/\D/g, '');
    
    if (!nip || nip.length < 10) {
        alert('Wpisz prawidłowy NIP (10 cyfr)');
        return;
    }
    
    try {
        const response = await fetch('/api/gus/company/' + nip);
        const data = await response.json();
        
        if (data.success) {
            document.getElementById('company_name').value = data.data.name || '';
            
            // Parse address to extract street, building and apartment
            const fullAddress = data.data.address || '';
            const addressParts = fullAddress.split(' ');
            if (addressParts.length > 0) {
                // Extract street (everything except last part which might be number)
                const lastPart = addressParts[addressParts.length - 1];
                if (/\d/.test(lastPart)) {
                    // Last part contains numbers
                    const street = addressParts.slice(0, -1).join(' ');
                    document.getElementById('company_address').value = street;
                    
                    // Try to parse building/apartment from number part
                    if (lastPart.includes('/')) {
                        const [building, apartment] = lastPart.split('/');
                        document.getElementById('building_number').value = building;
                        document.getElementById('apartment_number').value = apartment;
                    } else {
                        document.getElementById('building_number').value = lastPart;
                    }
                } else {
                    // No number detected, put everything as street
                    document.getElementById('company_address').value = fullAddress;
                }
            }
            
            document.getElementById('city').value = data.data.city || '';
            document.getElementById('postal_code').value = data.data.postal_code || '';
            
            alert('Dane zostały pobrane z GUS');
        } else {
            alert('Nie udało się pobrać danych z GUS: ' + (data.message || 'Nieznany błąd'));
        }
    } catch (error) {
        alert('Błąd połączenia z GUS');
    }
}
</script>
@endpush