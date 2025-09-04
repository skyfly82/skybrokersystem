@extends('layouts.guest')

@section('title', 'Rejestracja')

@section('content')
<div class="min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8 bg-gray-50">
    <div class="sm:mx-auto sm:w-full sm:max-w-2xl">
        <!-- Logo -->
        <div class="flex justify-center">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-truck text-white text-xl"></i>
                </div>
                <span class="ml-3 text-2xl font-bold text-gray-900">SkyBroker</span>
            </div>
        </div>
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
            Załóż konto firmowe
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600">
            Już masz konto?
            <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-500">
                Zaloguj się tutaj
            </a>
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-2xl">
        <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10" x-data="registerForm">
            <!-- Progress Steps -->
            <div class="mb-8">
                <nav aria-label="Progress">
                    <ol class="flex items-center">
                        <li class="relative pr-8 sm:pr-20">
                            <div class="absolute inset-0 flex items-center" aria-hidden="true">
                                <div class="h-0.5 w-full bg-gray-200"></div>
                            </div>
                            <div class="relative flex h-8 w-8 items-center justify-center rounded-full"
                                 :class="currentStep >= 1 ? 'bg-blue-600' : 'bg-white border-2 border-gray-300'">
                                <span class="text-xs font-medium" 
                                      :class="currentStep >= 1 ? 'text-white' : 'text-gray-500'">1</span>
                            </div>
                            <div class="mt-2">
                                <p class="text-xs font-medium text-gray-900">Dane firmy</p>
                            </div>
                        </li>

                        <li class="relative pr-8 sm:pr-20">
                            <div class="absolute inset-0 flex items-center" aria-hidden="true">
                                <div class="h-0.5 w-full bg-gray-200"></div>
                            </div>
                            <div class="relative flex h-8 w-8 items-center justify-center rounded-full"
                                 :class="currentStep >= 2 ? 'bg-blue-600' : 'bg-white border-2 border-gray-300'">
                                <span class="text-xs font-medium" 
                                      :class="currentStep >= 2 ? 'text-white' : 'text-gray-500'">2</span>
                            </div>
                            <div class="mt-2">
                                <p class="text-xs font-medium text-gray-900">Dane użytkownika</p>
                            </div>
                        </li>

                        <li class="relative">
                            <div class="relative flex h-8 w-8 items-center justify-center rounded-full"
                                 :class="currentStep >= 3 ? 'bg-blue-600' : 'bg-white border-2 border-gray-300'">
                                <span class="text-xs font-medium" 
                                      :class="currentStep >= 3 ? 'text-white' : 'text-gray-500'">3</span>
                            </div>
                            <div class="mt-2">
                                <p class="text-xs font-medium text-gray-900">Potwierdzenie</p>
                            </div>
                        </li>
                    </ol>
                </nav>
            </div>

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-400"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">
                                Wystąpiły błędy podczas rejestracji:
                            </h3>
                            <div class="mt-2 text-sm text-red-700">
                                <ul class="list-disc list-inside space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-6">
                @csrf

                <!-- Step 1: Company Data -->
                <div x-show="currentStep === 1" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-6" x-transition:enter-end="opacity-100 transform translate-x-0">
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900">Dane firmy</h3>
                        <p class="mt-1 text-sm text-gray-600">Wprowadź podstawowe informacje o swojej firmie</p>
                    </div>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <!-- Company Name -->
                        <div class="sm:col-span-2">
                            <label for="company_name" class="block text-sm font-medium text-gray-700">
                                Nazwa firmy <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1">
                                <input type="text" 
                                       name="company_name" 
                                       id="company_name"
                                       value="{{ old('company_name') }}"
                                       required
                                       class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                       placeholder="ABC Sp. z o.o.">
                            </div>
                            @error('company_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- NIP -->
                        <div>
                            <label for="nip" class="block text-sm font-medium text-gray-700">
                                NIP <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1">
                                <input type="text" 
                                       name="nip" 
                                       id="nip"
                                       value="{{ old('nip') }}"
                                       required
                                       pattern="[0-9]{10}"
                                       maxlength="10"
                                       class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                       placeholder="1234567890">
                            </div>
                            <p class="mt-1 text-xs text-gray-500">10 cyfr bez kresek</p>
                            @error('nip')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700">
                                Telefon <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1">
                                <input type="tel" 
                                       name="phone" 
                                       id="phone"
                                       value="{{ old('phone') }}"
                                       required
                                       class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                       placeholder="+48 123 456 789">
                            </div>
                            @error('phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Company Address -->
                        <div class="sm:col-span-2">
                            <label for="company_address" class="block text-sm font-medium text-gray-700">
                                Adres firmy <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1">
                                <textarea name="company_address" 
                                          id="company_address"
                                          rows="2"
                                          required
                                          class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                          placeholder="ul. Przykładowa 123">{{ old('company_address') }}</textarea>
                            </div>
                            @error('company_address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- City -->
                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700">
                                Miasto <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1">
                                <input type="text" 
                                       name="city" 
                                       id="city"
                                       value="{{ old('city') }}"
                                       required
                                       class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                       placeholder="Warszawa">
                            </div>
                            @error('city')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Postal Code -->
                        <div>
                            <label for="postal_code" class="block text-sm font-medium text-gray-700">
                                Kod pocztowy <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1">
                                <input type="text" 
                                       name="postal_code" 
                                       id="postal_code"
                                       value="{{ old('postal_code') }}"
                                       required
                                       pattern="[0-9]{2}-[0-9]{3}"
                                       placeholder="00-000"
                                       class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            </div>
                            @error('postal_code')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="sm:col-span-2">
                            <label for="email" class="block text-sm font-medium text-gray-700">
                                Email firmowy <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1">
                                <input type="email" 
                                       name="email" 
                                       id="email"
                                       value="{{ old('email') }}"
                                       required
                                       class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                       placeholder="kontakt@twojafirma.pl">
                            </div>
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Step 2: User Data -->
                <div x-show="currentStep === 2" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-6" x-transition:enter-end="opacity-100 transform translate-x-0">
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900">Dane głównego użytkownika</h3>
                        <p class="mt-1 text-sm text-gray-600">Utworzymy dla Ciebie konto administratora</p>
                    </div>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <!-- First Name -->
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700">
                                Imię <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1">
                                <input type="text" 
                                       name="first_name" 
                                       id="first_name"
                                       value="{{ old('first_name') }}"
                                       required
                                       class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                       placeholder="Jan">
                            </div>
                            @error('first_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Last Name -->
                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700">
                                Nazwisko <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1">
                                <input type="text" 
                                       name="last_name" 
                                       id="last_name"
                                       value="{{ old('last_name') }}"
                                       required
                                       class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                       placeholder="Kowalski">
                            </div>
                            @error('last_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">
                                Hasło <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1 relative">
                                <input type="password" 
                                       name="password" 
                                       id="password"
                                       required
                                       minlength="8"
                                       x-ref="passwordInput"
                                       @input="checkPasswordStrength($event.target.value)"
                                       class="block w-full pr-10 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                       placeholder="Minimum 8 znaków">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <button type="button" 
                                            @click="togglePasswordVisibility('password')"
                                            class="text-gray-400 hover:text-gray-500 focus:outline-none">
                                        <i class="fas" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                                    </button>
                                </div>
                            </div>
                            <!-- Password Strength Indicator -->
                            <div class="mt-2">
                                <div class="flex space-x-1">
                                    <div class="h-1 flex-1 rounded" :class="passwordStrength >= 1 ? 'bg-red-500' : 'bg-gray-200'"></div>
                                    <div class="h-1 flex-1 rounded" :class="passwordStrength >= 2 ? 'bg-yellow-500' : 'bg-gray-200'"></div>
                                    <div class="h-1 flex-1 rounded" :class="passwordStrength >= 3 ? 'bg-green-500' : 'bg-gray-200'"></div>
                                </div>
                                <p class="mt-1 text-xs" :class="passwordStrengthColor">
                                    <span x-text="passwordStrengthText"></span>
                                </p>
                            </div>
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                                Potwierdź hasło <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1 relative">
                                <input type="password" 
                                       name="password_confirmation" 
                                       id="password_confirmation"
                                       required
                                       x-ref="passwordConfirmInput"
                                       class="block w-full pr-10 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                       placeholder="Powtórz hasło">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <button type="button" 
                                            @click="togglePasswordVisibility('confirm')"
                                            class="text-gray-400 hover:text-gray-500 focus:outline-none">
                                        <i class="fas" :class="showPasswordConfirm ? 'fa-eye-slash' : 'fa-eye'"></i>
                                    </button>
                                </div>
                            </div>
                            @error('password_confirmation')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Step 3: Confirmation -->
                <div x-show="currentStep === 3" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-6" x-transition:enter-end="opacity-100 transform translate-x-0">
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900">Potwierdzenie i regulamin</h3>
                        <p class="mt-1 text-sm text-gray-600">Sprawdź wprowadzone dane i zaakceptuj regulamin</p>
                    </div>

                    <!-- Summary -->
                    <div class="bg-gray-50 rounded-lg p-4 mb-6">
                        <h4 class="text-sm font-medium text-gray-900 mb-3">Podsumowanie:</h4>
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-2 sm:grid-cols-2 text-sm">
                            <div>
                                <dt class="text-gray-500">Firma:</dt>
                                <dd class="text-gray-900 font-medium" x-text="getFormValue('company_name')"></dd>
                            </div>
                            <div>
                                <dt class="text-gray-500">NIP:</dt>
                                <dd class="text-gray-900" x-text="getFormValue('nip')"></dd>
                            </div>
                            <div>
                                <dt class="text-gray-500">Email:</dt>
                                <dd class="text-gray-900" x-text="getFormValue('email')"></dd>
                            </div>
                            <div>
                                <dt class="text-gray-500">Administrator:</dt>
                                <dd class="text-gray-900" x-text="getFormValue('first_name') + ' ' + getFormValue('last_name')"></dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Terms and Conditions -->
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="terms" 
                                       name="terms" 
                                       type="checkbox" 
                                       required
                                       class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="terms" class="text-gray-700">
                                    Akceptuję <a href="#" class="font-medium text-blue-600 hover:text-blue-500">Regulamin świadczenia usług</a> 
                                    oraz <a href="#" class="font-medium text-blue-600 hover:text-blue-500">Politykę prywatności</a> <span class="text-red-500">*</span>
                                </label>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="marketing" 
                                       name="marketing" 
                                       type="checkbox" 
                                       class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="marketing" class="text-gray-700">
                                    Zgadzam się na otrzymywanie informacji marketingowych (opcjonalnie)
                                </label>
                            </div>
                        </div>

                        <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-info-circle text-blue-400"></i>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-blue-800">
                                        Co dalej?
                                    </h3>
                                    <div class="mt-2 text-sm text-blue-700">
                                        <p>Po rejestracji Twoje konto będzie oczekiwać na zatwierdzenie przez administratora. Otrzymasz email z potwierdzeniem gdy konto zostanie aktywowane.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <div class="flex justify-between pt-6">
                    <button type="button" 
                            x-show="currentStep > 1"
                            @click="previousStep()"
                            class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Wstecz
                    </button>

                    <div class="flex space-x-3">
                        <button type="button" 
                                x-show="currentStep < 3"
                                @click="nextStep()"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                            Dalej
                            <i class="fas fa-arrow-right ml-2"></i>
                        </button>

                        <button type="submit" 
                                x-show="currentStep === 3"
                                :disabled="isSubmitting"
                                @click="isSubmitting = true"
                                class="bg-green-600 hover:bg-green-700 disabled:opacity-50 text-white px-6 py-2 rounded-md text-sm font-medium">
                            <span x-show="!isSubmitting">
                                <i class="fas fa-check mr-2"></i>
                                Załóż konto
                            </span>
                            <span x-show="isSubmitting">
                                <i class="fas fa-spinner fa-spin mr-2"></i>
                                Tworzenie konta...
                            </span>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Footer -->
        <div class="mt-8 text-center">
            <div class="flex justify-center space-x-6 text-sm">
                <a href="#" class="text-gray-500 hover:text-gray-700">Pomoc</a>
                <a href="#" class="text-gray-500 hover:text-gray-700">Kontakt</a>
                <a href="#" class="text-gray-500 hover:text-gray-700">FAQ</a>
            </div>
            <p class="mt-4 text-xs text-gray-400">
                Rejestrując się, akceptujesz nasze warunki świadczenia usług
            </p>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('registerForm', () => ({
        currentStep: 1,
        showPassword: false,
        showPasswordConfirm: false,
        isSubmitting: false,
        passwordStrength: 0,
        passwordStrengthText: '',
        passwordStrengthColor: 'text-gray-500',
        
        nextStep() {
            if (this.validateCurrentStep()) {
                this.currentStep++;
            }
        },
        
        previousStep() {
            this.currentStep--;
        },
        
        validateCurrentStep() {
            const step = this.currentStep;
            
            if (step === 1) {
                const requiredFields = ['company_name', 'nip', 'phone', 'company_address', 'city', 'postal_code', 'email'];
                return this.validateRequiredFields(requiredFields);
            } else if (step === 2) {
                const requiredFields = ['first_name', 'last_name', 'password', 'password_confirmation'];
                const isValid = this.validateRequiredFields(requiredFields);
                
                if (isValid) {
                    const password = document.getElementById('password').value;
                    const passwordConfirm = document.getElementById('password_confirmation').value;
                    
                    if (password !== passwordConfirm) {
                        alert('Hasła muszą być identyczne');
                        return false;
                    }
                    
                    if (password.length < 8) {
                        alert('Hasło musi mieć co najmniej 8 znaków');
                        return false;
                    }
                }
                
                return isValid;
            }
            
            return true;
        },
        
        validateRequiredFields(fields) {
            for (const field of fields) {
                const element = document.getElementById(field);
                if (!element || !element.value.trim()) {
                    alert(`Pole "${this.getFieldLabel(field)}" jest wymagane`);
                    element?.focus();
                    return false;
                }
            }
            return true;
        },
        
        getFieldLabel(fieldName) {
            const labels = {
                'company_name': 'Nazwa firmy',
                'nip': 'NIP',
                'phone': 'Telefon',
                'company_address': 'Adres firmy',
                'city': 'Miasto',
                'postal_code': 'Kod pocztowy',
                'email': 'Email',
                'first_name': 'Imię',
                'last_name': 'Nazwisko',
                'password': 'Hasło',
                'password_confirmation': 'Potwierdzenie hasła'
            };
            return labels[fieldName] || fieldName;
        },
        
        getFormValue(fieldName) {
            const element = document.getElementById(fieldName);
            return element ? element.value : '';
        },
        
        togglePasswordVisibility(type) {
            if (type === 'password') {
                this.showPassword = !this.showPassword;
                const input = this.$refs.passwordInput;
                input.type = this.showPassword ? 'text' : 'password';
            } else {
                this.showPasswordConfirm = !this.showPasswordConfirm;
                const input = this.$refs.passwordConfirmInput;
                input.type = this.showPasswordConfirm ? 'text' : 'password';
            }
        },
        
        checkPasswordStrength(password) {
            let strength = 0;
            let text = '';
            let color = 'text-gray-500';
            
            if (password.length >= 8) strength++;
            if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
            if (password.match(/[0-9]/)) strength++;
            if (password.match(/[^a-zA-Z0-9]/)) strength++;
            
            switch (strength) {
                case 0:
                case 1:
                    text = 'Hasło słabe';
                    color = 'text-red-600';
                    break;
                case 2:
                    text = 'Hasło średnie';
                    color = 'text-yellow-600';
                    break;
                case 3:
                case 4:
                    text = 'Hasło silne';
                    color = 'text-green-600';
                    break;
            }
            
            this.passwordStrength = strength;
            this.passwordStrengthText = text;
            this.passwordStrengthColor = color;
        }
    }));
});

// Format NIP input
document.getElementById('nip')?.addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length > 10) {
        value = value.slice(0, 10);
    }
    e.target.value = value;
});

// Format postal code input
document.getElementById('postal_code')?.addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length >= 2) {
        value = value.slice(0, 2) + '-' + value.slice(2, 5);
    }
    e.target.value = value;
});

// Format phone input
document.getElementById('phone')?.addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.startsWith('48')) {
        value = '+48 ' + value.slice(2);
    } else if (value.startsWith('0')) {
        value = '+48 ' + value.slice(1);
    } else if (!value.startsWith('+')) {
        value = '+48 ' + value;
    }
    
    // Format as +48 123 456 789
    value = value.replace(/(\+48\s?)(\d{3})(\d{3})(\d{3})/, '+48 $2 $3 $4');
    e.target.value = value;
});

// Email validation
document.getElementById('email')?.addEventListener('blur', function(e) {
    const email = e.target.value;
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    
    if (email && !emailRegex.test(email)) {
        e.target.classList.add('border-red-300');
        e.target.classList.remove('border-gray-300');
        
        // Show error message
        let errorMsg = e.target.parentNode.querySelector('.email-error');
        if (!errorMsg) {
            errorMsg = document.createElement('p');
            errorMsg.className = 'mt-1 text-sm text-red-600 email-error';
            errorMsg.textContent = 'Proszę podać prawidłowy adres email';
            e.target.parentNode.appendChild(errorMsg);
        }
    } else {
        e.target.classList.remove('border-red-300');
        e.target.classList.add('border-gray-300');
        
        // Remove error message
        const errorMsg = e.target.parentNode.querySelector('.email-error');
        if (errorMsg) {
            errorMsg.remove();
        }
    }
});

// Auto-focus on first field
document.addEventListener('DOMContentLoaded', function() {
    const firstInput = document.getElementById('company_name');
    if (firstInput) {
        firstInput.focus();
    }
});

// Prevent form submission on Enter (except on submit button)
document.querySelector('form').addEventListener('keydown', function(e) {
    if (e.key === 'Enter' && e.target.type !== 'submit') {
        e.preventDefault();
        const registerForm = Alpine.$data(document.querySelector('[x-data="registerForm"]'));
        if (registerForm.currentStep < 3) {
            registerForm.nextStep();
        }
    }
});

// Real-time password match validation
document.getElementById('password_confirmation')?.addEventListener('input', function(e) {
    const password = document.getElementById('password').value;
    const confirmPassword = e.target.value;
    
    if (confirmPassword && password !== confirmPassword) {
        e.target.classList.add('border-red-300');
        e.target.classList.remove('border-gray-300');
    } else {
        e.target.classList.remove('border-red-300');
        e.target.classList.add('border-gray-300');
    }
});

// Form submission validation
document.querySelector('form').addEventListener('submit', function(e) {
    const termsCheckbox = document.getElementById('terms');
    
    if (!termsCheckbox.checked) {
        e.preventDefault();
        alert('Musisz zaakceptować regulamin aby kontynuować');
        termsCheckbox.focus();
        return false;
    }
    
    // Additional validation
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('password_confirmation').value;
    
    if (password !== confirmPassword) {
        e.preventDefault();
        alert('Hasła muszą być identyczne');
        return false;
    }
    
    if (password.length < 8) {
        e.preventDefault();
        alert('Hasło musi mieć co najmniej 8 znaków');
        return false;
    }
});
</script>
@endpush
@endsection