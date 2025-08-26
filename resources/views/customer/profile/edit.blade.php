@extends('layouts.customer')

@section('title', 'Edytuj profil')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Edytuj profil</h2>
            <p class="mt-1 text-sm text-gray-600">
                Zaktualizuj informacje o profilu i ustawieniach konta.
            </p>
        </div>
        <a href="{{ route('customer.profile.show') }}" 
           class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
            <i class="fas fa-arrow-left mr-2"></i>
            Powrót
        </a>
    </div>

    <form method="POST" action="{{ route('customer.profile.update') }}" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- User Information -->
        <div class="bg-white shadow rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Dane użytkownika</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- First Name -->
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700">
                            Imię
                        </label>
                        <input type="text" 
                               id="first_name" 
                               name="first_name" 
                               value="{{ old('first_name', $user->first_name) }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('first_name') border-red-300 @enderror"
                               required>
                        @error('first_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Last Name -->
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700">
                            Nazwisko
                        </label>
                        <input type="text" 
                               id="last_name" 
                               name="last_name" 
                               value="{{ old('last_name', $user->last_name) }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('last_name') border-red-300 @enderror"
                               required>
                        @error('last_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">
                            Email
                        </label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               value="{{ old('email', $user->email) }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-300 @enderror"
                               required>
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700">
                            Telefon
                        </label>
                        <input type="text" 
                               id="phone" 
                               name="phone" 
                               value="{{ old('phone', $user->phone) }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('phone') border-red-300 @enderror">
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Company Information (only admins can edit core company data) -->
        @if($user->canCreateUsers() || $user->is_primary)
        <div class="bg-white shadow rounded-lg">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Dane firmy</h3>
                    <div class="text-xs text-gray-500">
                        <i class="fas fa-info-circle mr-1"></i>
                        Tylko administratorzy mogą edytować dane firmy
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Company Name -->
                    <div>
                        <label for="company_name" class="block text-sm font-medium text-gray-700">
                            Nazwa firmy
                        </label>
                        <input type="text" 
                               id="company_name" 
                               name="company_name" 
                               value="{{ old('company_name', $customer->company_name) }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('company_name') border-red-300 @enderror"
                               required>
                        @error('company_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tax Number -->
                    <div>
                        <label for="tax_number" class="block text-sm font-medium text-gray-700">
                            NIP
                        </label>
                        <input type="text" 
                               id="tax_number" 
                               name="tax_number" 
                               value="{{ old('tax_number', $customer->tax_number) }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('tax_number') border-red-300 @enderror">
                        @error('tax_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Company Email -->
                    <div>
                        <label for="company_email" class="block text-sm font-medium text-gray-700">
                            Email firmy
                        </label>
                        <input type="email" 
                               id="company_email" 
                               name="company_email" 
                               value="{{ old('company_email', $customer->email) }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('company_email') border-red-300 @enderror"
                               required>
                        @error('company_email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Company Phone -->
                    <div>
                        <label for="company_phone" class="block text-sm font-medium text-gray-700">
                            Telefon firmy
                        </label>
                        <input type="text" 
                               id="company_phone" 
                               name="company_phone" 
                               value="{{ old('company_phone', $customer->phone) }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('company_phone') border-red-300 @enderror">
                        @error('company_phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Company Address -->
                    <div class="md:col-span-2">
                        <label for="company_address" class="block text-sm font-medium text-gray-700">
                            Adres firmy
                        </label>
                        <textarea id="company_address" 
                                  name="company_address" 
                                  rows="3"
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('company_address') border-red-300 @enderror">{{ old('company_address', $customer->address) }}</textarea>
                        @error('company_address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Financial Information -->
        <div class="bg-white shadow rounded-lg">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Dane finansowe</h3>
                    <div class="text-xs text-gray-500">
                        <i class="fas fa-shield-alt mr-1"></i>
                        Zmiany wymagają weryfikacji
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- COD Return Account -->
                    <div>
                        <label for="cod_return_account" class="block text-sm font-medium text-gray-700">
                            Konto zwrotów COD
                            <span class="text-xs text-gray-500 block mt-1">NRB (IBAN) do zwrotów pobrań</span>
                        </label>
                        <input type="text" 
                               id="cod_return_account" 
                               name="cod_return_account" 
                               value="{{ old('cod_return_account', $customer->cod_return_account) }}"
                               placeholder="PL61 1090 1014 0000 0712 1981 2874"
                               pattern="^(PL)?[0-9]{26}$"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('cod_return_account') border-red-300 @enderror">
                        @error('cod_return_account')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Settlement Account -->
                    <div>
                        <label for="settlement_account" class="block text-sm font-medium text-gray-700">
                            Konto rozliczeniowe
                            <span class="text-xs text-gray-500 block mt-1">NRB (IBAN) do rozliczeń za przesyłki</span>
                        </label>
                        <input type="text" 
                               id="settlement_account" 
                               name="settlement_account" 
                               value="{{ old('settlement_account', $customer->settlement_account) }}"
                               placeholder="PL61 1090 1014 0000 0712 1981 2874"
                               pattern="^(PL)?[0-9]{26}$"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('settlement_account') border-red-300 @enderror">
                        @error('settlement_account')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="mt-4 p-3 bg-blue-50 rounded-lg border border-blue-200">
                    <div class="flex">
                        <i class="fas fa-info-circle text-blue-400 mt-0.5 mr-2"></i>
                        <div class="text-sm">
                            <p class="text-blue-800 font-medium">Informacje o kontach bankowych:</p>
                            <ul class="text-blue-700 mt-1 space-y-1 text-xs">
                                <li>• Konto COD - zwroty pobrań będą przekazywane na to konto</li>
                                <li>• Konto rozliczeniowe - rozliczenia za usługi kurierskie</li>
                                <li>• Wszystkie zmiany danych finansowych są audytowane</li>
                                <li>• W przypadku problemów skontaktuj się z obsługą klienta</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @else
        <!-- Limited view for non-admins -->
        <div class="bg-white shadow rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Informacje o firmie</h3>
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex">
                        <i class="fas fa-exclamation-triangle text-yellow-400 mt-0.5 mr-2"></i>
                        <div class="text-sm">
                            <p class="text-yellow-800 font-medium">Ograniczony dostęp</p>
                            <p class="text-yellow-700 mt-1">
                                Tylko administratorzy mogą edytować dane firmy i informacje finansowe. 
                                Jeśli potrzebujesz zmienić te dane, skontaktuj się z administratorem konta.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Submit Button -->
        <div class="flex justify-end">
            <button type="submit" 
                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-save mr-2"></i>
                Zapisz zmiany
            </button>
        </div>
    </form>
</div>

<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endsection