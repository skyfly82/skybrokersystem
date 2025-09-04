@extends('layouts.auth')

@section('title', 'Logowanie Administrator')
@section('header', 'Panel Administratora')
@section('description', 'Zaloguj się do panelu zarządzania systemem SkyBroker')

@section('content')
<form method="POST" action="{{ route('admin.login') }}" class="space-y-6">
    @csrf
    
    <!-- Email Address -->
    <div>
        <label for="email" class="block text-sm font-medium leading-6 text-gray-900">
            Adres email
        </label>
        <div class="mt-2 relative">
            <input 
                id="email" 
                name="email" 
                type="email" 
                autocomplete="email" 
                required 
                value="{{ old('email') }}"
                class="block w-full rounded-md border-0 py-2.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-red-600 sm:text-sm sm:leading-6 @error('email') ring-red-300 focus:ring-red-500 @enderror"
                placeholder="admin@skybroker.pl"
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
            Hasło
        </label>
        <div class="mt-2 relative">
            <input 
                id="password" 
                name="password" 
                type="password" 
                autocomplete="current-password" 
                required
                class="block w-full rounded-md border-0 py-2.5 px-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-red-600 sm:text-sm sm:leading-6 @error('password') ring-red-300 focus:ring-red-500 @enderror"
                placeholder="••••••••"
            >
            <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>
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
                class="h-4 w-4 rounded border-gray-300 text-red-600 focus:ring-red-600"
                {{ old('remember') ? 'checked' : '' }}
            >
            <label for="remember" class="ml-3 block text-sm leading-6 text-gray-700">
                Zapamiętaj mnie
            </label>
        </div>

        <div class="text-sm leading-6">
            <a href="#" class="font-semibold text-red-600 hover:text-red-500 transition-colors duration-200">
                Zapomniałeś hasła?
            </a>
        </div>
    </div>

    <!-- Admin Access Notice -->
    <div class="rounded-md bg-red-50 p-4 border border-red-200">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">
                    Dostęp tylko dla administratorów
                </h3>
                <div class="mt-2 text-sm text-red-700">
                    <p>
                        Ten panel jest przeznaczony wyłącznie dla administratorów systemu. 
                        Wszystkie działania są monitorowane i rejestrowane.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Submit Button -->
    <div>
        <button 
            type="submit" 
            class="flex w-full justify-center rounded-md bg-red-600 px-3 py-2.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-red-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600 transition-all duration-200"
        >
            Zaloguj się
        </button>
    </div>

    <!-- Development Login Helper -->
    @if(app()->environment('local'))
    <div class="rounded-md bg-blue-50 p-4 border border-blue-200">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">
                    Środowisko deweloperskie
                </h3>
                <div class="mt-2 text-sm text-blue-700">
                    <p>Konta testowe:</p>
                    <ul class="mt-1 space-y-1 text-xs font-mono">
                        <li>admin@skybroker.pl / admin123</li>
                        <li>superadmin@skybroker.pl / admin123</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @endif
</form>
@endsection

@section('footer')
<div class="text-center">
    <p class="text-sm text-gray-600">
        Szukasz panelu klienta?
    </p>
    <a href="{{ route('customer.login') }}" class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 mt-2">
        <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
        </svg>
        Panel Klienta
    </a>
</div>
@endsection