@extends('layouts.guest')

@section('title', 'Logowanie')

@section('content')
<div class="min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8 bg-gray-50">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
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
            Zaloguj się do swojego konta
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600">
            Lub
            <a href="{{ route('register') }}" class="font-medium text-blue-600 hover:text-blue-500">
                załóż nowe konto firmowe
            </a>
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10" x-data="loginForm">
            <!-- Login Type Selector -->
            <div class="mb-6">
                <div class="flex rounded-lg bg-gray-100 p-1">
                    <button type="button" 
                            @click="loginType = 'customer'"
                            :class="loginType === 'customer' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                            class="flex-1 px-3 py-2 text-sm font-medium rounded-md transition-all duration-200">
                        <i class="fas fa-building mr-2"></i>
                        Panel Klienta
                    </button>
                    <button type="button" 
                            @click="loginType = 'admin'"
                            :class="loginType === 'admin' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                            class="flex-1 px-3 py-2 text-sm font-medium rounded-md transition-all duration-200">
                        <i class="fas fa-cog mr-2"></i>
                        Panel Admina
                    </button>
                </div>
            </div>

            <!-- Success Message -->
            @if (session('success'))
                <div class="mb-4 bg-green-50 border border-green-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle text-green-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-800">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="mb-4 bg-red-50 border border-red-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-400"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">
                                Wystąpiły błędy podczas logowania:
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

            <!-- Login Form -->
            <form class="space-y-6" 
                  :action="loginType === 'customer' ? '{{ route('customer.login') }}' : '{{ route('admin.login') }}'" 
                  method="POST">
                @csrf
                
                <!-- Email Address -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">
                        Adres email
                    </label>
                    <div class="mt-1 relative">
                        <input id="email" 
                               name="email" 
                               type="email" 
                               autocomplete="email" 
                               required 
                               value="{{ old('email') }}"
                               class="appearance-none block w-full px-3 py-2 pl-10 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm"
                               placeholder="twoj@email.com">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400"></i>
                        </div>
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">
                        Hasło
                    </label>
                    <div class="mt-1 relative">
                        <input id="password" 
                               name="password" 
                               type="password" 
                               autocomplete="current-password" 
                               required 
                               class="appearance-none block w-full px-3 py-2 pl-10 pr-10 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm"
                               placeholder="Twoje hasło"
                               x-ref="passwordInput">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <button type="button" 
                                    @click="togglePasswordVisibility()"
                                    class="text-gray-400 hover:text-gray-500 focus:outline-none">
                                <i class="fas" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember_me" 
                               name="remember" 
                               type="checkbox" 
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="remember_me" class="ml-2 block text-sm text-gray-900">
                            Zapamiętaj mnie
                        </label>
                    </div>

                    <div class="text-sm">
                        <a href="{{ route('password.request') }}" 
                           class="font-medium text-blue-600 hover:text-blue-500">
                            Zapomniałeś hasła?
                        </a>
                    </div>
                </div>

                <!-- Submit Button -->
                <div>
                    <button type="submit" 
                            :disabled="isSubmitting"
                            @click="isSubmitting = true"
                            class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <i class="fas fa-sign-in-alt text-blue-500 group-hover:text-blue-400" 
                               :class="{ 'fa-spinner fa-spin': isSubmitting, 'fa-sign-in-alt': !isSubmitting }"></i>
                        </span>
                        <span x-text="isSubmitting ? 'Logowanie...' : 'Zaloguj się'"></span>
                    </button>
                </div>

                <!-- Additional Info based on login type -->
                <div class="mt-6">
                    <div x-show="loginType === 'customer'" class="bg-blue-50 border border-blue-200 rounded-md p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-info-circle text-blue-400"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">
                                    Panel Klienta
                                </h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <p>Zaloguj się aby zarządzać przesyłkami, sprawdzać saldo i przeglądać historię płatności.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div x-show="loginType === 'admin'" class="bg-amber-50 border border-amber-200 rounded-md p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-shield-alt text-amber-400"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-amber-800">
                                    Panel Administracyjny
                                </h3>
                                <div class="mt-2 text-sm text-amber-700">
                                    <p>Dostęp tylko dla administratorów systemu. Wymagane specjalne uprawnienia.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Demo Credentials (only in development) -->
            @if(app()->environment('local'))
            <div class="mt-6 border-t border-gray-200 pt-6">
                <h3 class="text-sm font-medium text-gray-700 mb-3">Konta testowe:</h3>
                <div class="space-y-2 text-xs">
                    <div class="bg-gray-50 p-2 rounded">
                        <strong>Admin:</strong> admin@skybroker.pl / admin123
                    </div>
                    <div class="bg-gray-50 p-2 rounded">
                        <strong>Klient:</strong> klient@example.com / klient123
                    </div>
                </div>
                <div class="mt-2 flex space-x-2">
                    <button type="button" 
                            @click="fillDemoCredentials('admin')"
                            class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded hover:bg-red-200">
                        Wypełnij dane admina
                    </button>
                    <button type="button" 
                            @click="fillDemoCredentials('customer')"
                            class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded hover:bg-blue-200">
                        Wypełnij dane klienta
                    </button>
                </div>
            </div>
            @endif

            <!-- Social Login (Future feature) -->
            <div class="mt-6">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300" />
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white text-gray-500">Lub kontynuuj z</span>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-2 gap-3">
                    <button type="button" 
                            class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 cursor-not-allowed opacity-50">
                        <i class="fab fa-google text-red-500"></i>
                        <span class="ml-2">Google</span>
                    </button>

                    <button type="button" 
                            class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 cursor-not-allowed opacity-50">
                        <i class="fab fa-microsoft text-blue-500"></i>
                        <span class="ml-2">Microsoft</span>
                    </button>
                </div>
                <p class="mt-2 text-center text-xs text-gray-500">
                    Logowanie społecznościowe będzie dostępne wkrótce
                </p>
            </div>
        </div>

        <!-- Footer Links -->
        <div class="mt-8 text-center">
            <div class="flex justify-center space-x-6 text-sm">
                <a href="#" class="text-gray-500 hover:text-gray-700">O nas</a>
                <a href="#" class="text-gray-500 hover:text-gray-700">Pomoc</a>
                <a href="#" class="text-gray-500 hover:text-gray-700">Kontakt</a>
                <a href="#" class="text-gray-500 hover:text-gray-700">Regulamin</a>
            </div>
            <p class="mt-4 text-xs text-gray-400">
                © {{ date('Y') }} SkyBrokerSystem. Wszystkie prawa zastrzeżone.
            </p>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('loginForm', () => ({
        loginType: 'customer',
        showPassword: false,
        isSubmitting: false,
        
        togglePasswordVisibility() {
            this.showPassword = !this.showPassword;
            const input = this.$refs.passwordInput;
            input.type = this.showPassword ? 'text' : 'password';
        },
        
        fillDemoCredentials(type) {
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            
            if (type === 'admin') {
                this.loginType = 'admin';
                emailInput.value = 'admin@skybroker.pl';
                passwordInput.value = 'admin123';
            } else {
                this.loginType = 'customer';
                emailInput.value = 'klient@example.com';
                passwordInput.value = 'klient123';
            }
        }
    }));
});

// Auto-focus on email input
document.addEventListener('DOMContentLoaded', function() {
    const emailInput = document.getElementById('email');
    if (emailInput && !emailInput.value) {
        emailInput.focus();
    }
});

// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    
    if (!email || !password) {
        e.preventDefault();
        alert('Proszę wypełnić wszystkie pola');
        return false;
    }
    
    if (!isValidEmail(email)) {
        e.preventDefault();
        alert('Proszę podać prawidłowy adres email');
        return false;
    }
});

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// Show appropriate message based on redirect
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const redirected = urlParams.get('redirected');
    
    if (redirected === 'session_expired') {
        // Show session expired message
        const alertDiv = document.createElement('div');
        alertDiv.className = 'mb-4 bg-yellow-50 border border-yellow-200 rounded-md p-4';
        alertDiv.innerHTML = `
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-clock text-yellow-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-800">Twoja sesja wygasła. Proszę zalogować się ponownie.</p>
                </div>
            </div>
        `;
        
        const form = document.querySelector('form');
        form.parentNode.insertBefore(alertDiv, form);
    }
});
</script>
@endpush
@endsection