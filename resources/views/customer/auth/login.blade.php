@extends('layouts.auth')

@section('title', __('auth.customer_login'))
@section('header', __('auth.customer_login'))
@section('description', __('auth.customer_login_subtitle'))

@section('content')
<form method="POST" action="{{ route('customer.login') }}" class="space-y-6" x-data="customerLogin">
    @csrf
    
    <!-- Email Address -->
    <div>
        <label for="email" class="block text-sm font-medium leading-6 text-gray-900">
            {{ __('auth.email') }}
        </label>
        <div class="mt-2 relative">
            <input 
                id="email" 
                name="email" 
                type="email" 
                autocomplete="email" 
                required 
                value="{{ old('email') }}"
                class="block w-full rounded-md border-0 py-2.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6 @error('email') ring-red-300 focus:ring-red-500 @enderror"
                placeholder="twoj@email.pl"
                x-model="email"
            >
            <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                </svg>
            </div>
        </div>
        @error('email')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Password -->
    <div>
        <label for="password" class="block text-sm font-medium leading-6 text-gray-900">
            {{ __('auth.password') }}
        </label>
        <div class="mt-2 relative">
            <input 
                id="password" 
                name="password" 
                :type="showPassword ? 'text' : 'password'" 
                autocomplete="current-password" 
                required
                class="block w-full rounded-md border-0 py-2.5 px-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6 @error('password') ring-red-300 focus:ring-red-500 @enderror"
                placeholder="••••••••"
                x-model="password"
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
        @error('password')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Remember Me & Forgot Password -->
    <div class="flex items-center justify-between">
        <div class="flex items-center">
            <input 
                id="remember" 
                name="remember" 
                type="checkbox" 
                class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-600"
                {{ old('remember') ? 'checked' : '' }}
            >
            <label for="remember" class="ml-3 block text-sm leading-6 text-gray-700">
                {{ __('auth.remember_me') }}
            </label>
        </div>

        <div class="text-sm leading-6">
            <a href="#" class="font-semibold text-primary-600 hover:text-primary-500 transition-colors duration-200">
                {{ __('auth.forgot_password') }}
            </a>
        </div>
    </div>

    <!-- Submit Button -->
    <div>
        <button 
            type="submit" 
            class="flex w-full justify-center rounded-md bg-primary-600 px-3 py-2.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200"
            :disabled="loading"
            @click="loading = true"
            x-text="loading ? '{{ __('auth.login') }}...' : '{{ __('auth.login') }}'"
        >
            Zaloguj się
        </button>
    </div>

    <!-- Features Preview -->
    <div class="rounded-md bg-blue-50 p-4 border border-blue-200">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">
                    Twój system kurierski
                </h3>
                <div class="mt-2 text-sm text-blue-700">
                    <p>
                        Zarządzaj przesyłkami, monitoruj dostawy i kontroluj koszty - 
                        wszystko w jednym miejscu.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Development Login Helper -->
    @if(app()->environment('local'))
    <div class="rounded-md bg-green-50 p-4 border border-green-200">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-green-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-green-800">
                    Konta testowe
                </h3>
                <div class="mt-2 text-sm text-green-700">
                    <ul class="space-y-1">
                        <li>
                            <button 
                                type="button"
                                class="font-mono text-xs hover:underline"
                                @click="fillCredentials('test@company.pl', 'password')"
                            >
                                test@company.pl / password
                            </button>
                            <span class="text-xs text-green-600 ml-2">(aktywne konto)</span>
                        </li>
                        <li>
                            <button 
                                type="button"
                                class="font-mono text-xs hover:underline"
                                @click="fillCredentials('demo@business.pl', 'password')"
                            >
                                demo@business.pl / password
                            </button>
                            <span class="text-xs text-green-600 ml-2">(z przykładowymi danymi)</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @endif
</form>
@endsection

@section('footer')
<div class="text-center space-y-4">
    <div>
        <p class="text-sm text-gray-600">
            {{ __('auth.dont_have_account') }}
        </p>
        <a href="{{ route('customer.register') }}" class="inline-flex items-center justify-center rounded-md bg-primary-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-all duration-200 mt-2">
            <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM3 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 019.374 21c-2.331 0-4.512-.645-6.374-1.764z" />
            </svg>
            {{ __('auth.create_account') }}
        </a>
    </div>
    
    <div class="border-t border-gray-200 pt-4">
        <p class="text-xs text-gray-500">
            {{ __('common.administrator_question') }}
        </p>
        <a href="{{ route('admin.login') }}" class="text-sm text-gray-600 hover:text-primary-600 transition-colors duration-200">
            {{ __('auth.go_to_admin') }}
        </a>
    </div>
</div>
@endsection

@section('additional-links')
<a href="#" class="text-gray-600 hover:text-primary-600 transition-colors duration-200">
    📞 Kontakt: +48 123 456 789
</a>
<a href="#" class="text-gray-600 hover:text-primary-600 transition-colors duration-200">
    ✉️ Email: support@skybroker.pl
</a>
<a href="#" class="text-gray-600 hover:text-primary-600 transition-colors duration-200">
    🕒 Godziny: Pon-Pt 8:00-18:00
</a>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('customerLogin', () => ({
        email: '{{ old('email') }}',
        password: '',
        loading: false,
        showPassword: false,
        
        fillCredentials(email, password) {
            this.email = email;
            this.password = password;
        }
    }))
});
</script>
@endpush