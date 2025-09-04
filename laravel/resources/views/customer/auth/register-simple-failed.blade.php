@extends('layouts.auth')

@section('title', 'Rejestracja Konto')
@section('header', 'Załóż konto w SkyBroker')
@section('description', 'Wybierz typ konta i rozpocznij współpracę z nami')

@section('content')
<div class="space-y-6">
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
    <form method="POST" action="{{ route('customer.register.company') }}" class="space-y-6" id="registration-form">
        @csrf
        <input type="hidden" name="account_type" id="account-type" value="company">
        
        <!-- Company Information (Company Only) -->
        <div id="company-section" class="bg-gray-50 p-4 rounded-lg border border-gray-200">
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
                        >
                    </div>
                    @error('company_name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- NIP with GUS Button -->
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
                    >
                </div>
            </div>
        </div>

        <!-- Common Contact Information -->
        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                <svg class="h-5 w-5 text-primary-600 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                </svg>
                <span id="contact-header">Dane osoby kontaktowej</span>
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
                            class="block w-full rounded-md border-0 py-2.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6 @error('email') ring-red-300 focus:ring-red-500 @enderror"
                            placeholder="kontakt@firma.pl"
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
                            class="block w-full rounded-md border-0 py-2.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6 @error('phone') ring-red-300 focus:ring-red-500 @enderror"
                            placeholder="+48 123 456 789"
                        >
                    </div>
                    @error('phone')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Password Section -->
        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                <svg class="h-5 w-5 text-primary-600 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
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
                    <div class="mt-2">
                        <input 
                            id="password" 
                            name="password" 
                            type="password" 
                            required
                            class="block w-full rounded-md border-0 py-2.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6 @error('password') ring-red-300 focus:ring-red-500 @enderror"
                            placeholder="min. 8 znaków"
                        >
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
                    <div class="mt-2">
                        <input 
                            id="password_confirmation" 
                            name="password_confirmation" 
                            type="password" 
                            required
                            class="block w-full rounded-md border-0 py-2.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6"
                            placeholder="powtórz hasło"
                        >
                    </div>
                </div>
            </div>
        </div>

        <!-- Terms and Conditions -->
        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
            <div class="space-y-3">
                <label class="flex items-start">
                    <input type="checkbox" name="terms_accepted" required class="mt-1 h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded" {{ old('terms_accepted') ? 'checked' : '' }}>
                    <span class="ml-2 text-sm text-gray-700">
                        Akceptuję <a href="#" class="text-primary-600 hover:text-primary-500">Regulamin</a> oraz 
                        <a href="#" class="text-primary-600 hover:text-primary-500">Politykę Prywatności</a> <span class="text-red-500">*</span>
                    </span>
                </label>

                <label class="flex items-start">
                    <input type="checkbox" name="privacy_accepted" required class="mt-1 h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded" {{ old('privacy_accepted') ? 'checked' : '' }}>
                    <span class="ml-2 text-sm text-gray-700">
                        Wyrażam zgodę na przetwarzanie moich danych osobowych zgodnie z RODO <span class="text-red-500">*</span>
                    </span>
                </label>

                <label class="flex items-start">
                    <input type="checkbox" name="marketing_accepted" class="mt-1 h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded" {{ old('marketing_accepted') ? 'checked' : '' }}>
                    <span class="ml-2 text-sm text-gray-700">
                        Wyrażam zgodę na otrzymywanie informacji marketingowych (opcjonalnie)
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
                <span id="submit-text">Załóż konto firmowe</span>
            </button>
        </div>

        <p class="text-xs text-gray-500 text-center">
            Po rejestracji Twoje konto zostanie poddane weryfikacji (1-2 dni robocze)
        </p>
    </form>
</div>
@endsection

@push('scripts')
<script>
// Global functions for switching account types
function switchToCompany() {
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
    
    // Update text labels
    document.getElementById('contact-header').textContent = 'Dane osoby kontaktowej';
    document.getElementById('submit-text').textContent = 'Załóż konto firmowe';
}

function switchToIndividual() {
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
    
    // Update text labels
    document.getElementById('contact-header').textContent = 'Twoje dane kontaktowe';
    document.getElementById('submit-text').textContent = 'Załóż konto indywidualne';
}

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

// Initialize on page load - company is default
document.addEventListener('DOMContentLoaded', function() {
    switchToCompany();
});
</script>
@endpush