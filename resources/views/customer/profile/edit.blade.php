@extends('layouts.app')

@section('title', 'Edytuj profil')

@section('header')
    <div class="flex items-center justify-between">
        <h2 class="font-heading font-semibold text-xl text-black-coal leading-tight">
            {{ __('Edytuj profil') }}
        </h2>
        <a href="{{ route('customer.profile.show') }}" 
           class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-body text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
            <i class="fas fa-arrow-left mr-2"></i>
            Powrót
        </a>
    </div>
@endsection

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <!-- Update Profile Information -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <header class="mb-6">
                    <h3 class="text-lg font-heading font-medium text-black-coal">Informacje o profilu</h3>
                    <p class="mt-1 font-body text-sm text-gray-600">
                        Zaktualizuj informacje o profilu i adres email swojego konta.
                    </p>
                </header>

                <form method="POST" action="{{ route('customer.profile.update') }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- First Name -->
                        <div>
                            <label for="first_name" class="block font-body text-sm font-medium text-gray-700">
                                Imię
                            </label>
                            <input type="text" 
                                   id="first_name" 
                                   name="first_name" 
                                   value="{{ old('first_name', $user->first_name) }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-skywave focus:ring-skywave sm:text-sm @error('first_name') border-red-300 @enderror"
                                   required>
                            @error('first_name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Last Name -->
                        <div>
                            <label for="last_name" class="block font-body text-sm font-medium text-gray-700">
                                Nazwisko
                            </label>
                            <input type="text" 
                                   id="last_name" 
                                   name="last_name" 
                                   value="{{ old('last_name', $user->last_name) }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-skywave focus:ring-skywave sm:text-sm @error('last_name') border-red-300 @enderror"
                                   required>
                            @error('last_name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block font-body text-sm font-medium text-gray-700">
                                Email
                            </label>
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email', $user->email) }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-skywave focus:ring-skywave sm:text-sm @error('email') border-red-300 @enderror"
                                   required>
                            @error('email')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div>
                            <label for="phone" class="block font-body text-sm font-medium text-gray-700">
                                Telefon
                            </label>
                            <input type="tel" 
                                   id="phone" 
                                   name="phone" 
                                   value="{{ old('phone', $user->phone) }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-skywave focus:ring-skywave sm:text-sm @error('phone') border-red-300 @enderror">
                            @error('phone')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Company Information (only for primary user) -->
                    @if($user->is_primary)
                    <div class="border-t border-gray-200 pt-6">
                        <h4 class="text-md font-heading font-medium text-black-coal mb-4">Dane firmy</h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Company Name -->
                            <div>
                                <label for="company_name" class="block font-body text-sm font-medium text-gray-700">
                                    Nazwa firmy
                                </label>
                                <input type="text" 
                                       id="company_name" 
                                       name="company_name" 
                                       value="{{ old('company_name', $customer->company_name) }}"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-skywave focus:ring-skywave sm:text-sm @error('company_name') border-red-300 @enderror"
                                       required>
                                @error('company_name')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Tax Number -->
                            <div>
                                <label for="tax_number" class="block font-body text-sm font-medium text-gray-700">
                                    NIP
                                </label>
                                <input type="text" 
                                       id="tax_number" 
                                       name="tax_number" 
                                       value="{{ old('tax_number', $customer->tax_number) }}"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-skywave focus:ring-skywave sm:text-sm @error('tax_number') border-red-300 @enderror">
                                @error('tax_number')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Company Email -->
                            <div>
                                <label for="company_email" class="block font-body text-sm font-medium text-gray-700">
                                    Email firmy
                                </label>
                                <input type="email" 
                                       id="company_email" 
                                       name="company_email" 
                                       value="{{ old('company_email', $customer->email) }}"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-skywave focus:ring-skywave sm:text-sm @error('company_email') border-red-300 @enderror">
                                @error('company_email')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Save Button -->
                    <div class="flex items-center justify-end pt-6 border-t border-gray-200">
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-skywave border border-transparent rounded-md font-body text-sm text-white tracking-widest hover:bg-skywave/90 focus:bg-skywave/90 active:bg-skywave/90 focus:outline-none focus:ring-2 focus:ring-skywave focus:ring-offset-2 transition ease-in-out duration-150">
                            <i class="fas fa-save mr-2"></i>
                            Zapisz zmiany
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Update Password -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <header class="mb-6">
                    <h3 class="text-lg font-heading font-medium text-black-coal">Zmień hasło</h3>
                    <p class="mt-1 font-body text-sm text-gray-600">
                        Upewnij się, że używasz długiego, losowego hasła dla bezpieczeństwa konta.
                    </p>
                </header>

                <form method="POST" action="{{ route('customer.profile.update-password') }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6">
                        <!-- Current Password -->
                        <div>
                            <label for="current_password" class="block font-body text-sm font-medium text-gray-700">
                                Obecne hasło
                            </label>
                            <input type="password" 
                                   id="current_password" 
                                   name="current_password"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-skywave focus:ring-skywave sm:text-sm @error('current_password') border-red-300 @enderror"
                                   required>
                            @error('current_password')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- New Password -->
                        <div>
                            <label for="password" class="block font-body text-sm font-medium text-gray-700">
                                Nowe hasło
                            </label>
                            <input type="password" 
                                   id="password" 
                                   name="password"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-skywave focus:ring-skywave sm:text-sm @error('password') border-red-300 @enderror"
                                   required>
                            @error('password')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label for="password_confirmation" class="block font-body text-sm font-medium text-gray-700">
                                Potwierdź nowe hasło
                            </label>
                            <input type="password" 
                                   id="password_confirmation" 
                                   name="password_confirmation"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-skywave focus:ring-skywave sm:text-sm"
                                   required>
                        </div>
                    </div>

                    <div class="flex items-center justify-end pt-6 border-t border-gray-200">
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-body text-sm text-white tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <i class="fas fa-key mr-2"></i>
                            Zmień hasło
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection