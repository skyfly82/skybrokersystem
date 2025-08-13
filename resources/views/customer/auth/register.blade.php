@extends('layouts.auth')

@section('title', 'Rejestracja Konto Firmowe')
@section('header', 'Załóż konto firmowe')
@section('description', 'Rozpocznij współpracę z SkyBroker już dziś')

@section('content')
<form method="POST" action="{{ route('customer.register') }}" class="space-y-6" x-data="customerRegister">
    @csrf
    
    <!-- Registration Steps Indicator -->
    <div class="mb-8">
        <div class="flex items-center justify-center">
            <div class="flex items-center">
                <div class="flex items-center justify-center w-8 h-8 bg-primary-600 rounded-full">
                    <span class="text-sm font-medium text-white">1</span>
                </div>
                <div class="ml-2 text-sm font-medium text-primary-600">Dane firmowe</div>
                <div class="ml-4 w-16 h-0.5 bg-gray-200"></div>
                <div class="ml-4 flex items-center justify-center w-8 h-8 border-2 border-gray-200 rounded-full">
                    <span class="text-sm font-medium text-gray-400">2</span>
                </div>
                <div class="ml-2 text-sm font-medium text-gray-400">Weryfikacja</div>
            </div>
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
                <label for="nip" class="block text-sm font-medium leading-6 text-gray-900">
                    NIP
                </label>
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
                    Adres firmy <span class="text-red-500">*</span>
                </label>
                <div class="mt-2">
                    <input 
                        id="company_address" 
                        name="company_address" 
                        type="text" 
                        required 
                        value="{{ old('company_address') }}"
                        class="block w-full rounded-md border-0 py-2.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6 @error('company_address') ring-red-300 focus:ring-red-500 @enderror"
                        placeholder="ul. Przykładowa 123/45"
                        x-model="address"
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
            @click="loading = true"
            x-text="loading ? 'Tworzenie konta...' : 'Załóż konto firmowe'"
        >
            Załóż konto firmowe
        </button>
        <p class="mt-2 text-xs text-gray-500 text-center">
            Po rejestracji Twoje konto zostanie poddane weryfikacji (1-2 dni robocze)
        </p>
    </div>
</form>
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
        }
    }))
});
</script>
@endpush