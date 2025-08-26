@extends('layouts.customer')

@section('title', 'Dodaj użytkownika')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Dodaj użytkownika</h2>
            <p class="mt-1 text-sm text-gray-600">
                Utwórz nowe konto użytkownika dla swojego zespołu.
            </p>
        </div>
        <a href="{{ route('customer.users.index') }}" 
           class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
            <i class="fas fa-arrow-left mr-2"></i>
            Powrót
        </a>
    </div>
    
    <!-- Form -->
    <div class="bg-white shadow rounded-lg">
        <div class="p-6">
                <form method="POST" action="{{ route('customer.users.store') }}">
                    @csrf

                    <!-- Personal Information -->
                    <div class="mb-8">
                        <h3 class="text-lg font-heading font-medium text-black-coal mb-4">Dane osobowe</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- First Name -->
                            <div>
                                <label for="first_name" class="block text-sm font-body font-medium text-gray-700 mb-2">
                                    Imię <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       name="first_name" 
                                       id="first_name" 
                                       value="{{ old('first_name') }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-skywave focus:ring-skywave font-body" 
                                       required>
                                @error('first_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Last Name -->
                            <div>
                                <label for="last_name" class="block text-sm font-body font-medium text-gray-700 mb-2">
                                    Nazwisko <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       name="last_name" 
                                       id="last_name" 
                                       value="{{ old('last_name') }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-skywave focus:ring-skywave font-body" 
                                       required>
                                @error('last_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="mb-8">
                        <h3 class="text-lg font-heading font-medium text-black-coal mb-4">Dane kontaktowe</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-body font-medium text-gray-700 mb-2">
                                    Email <span class="text-red-500">*</span>
                                </label>
                                <input type="email" 
                                       name="email" 
                                       id="email" 
                                       value="{{ old('email') }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-skywave focus:ring-skywave font-body" 
                                       required>
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Phone -->
                            <div>
                                <label for="phone" class="block text-sm font-body font-medium text-gray-700 mb-2">
                                    Telefon
                                </label>
                                <input type="text" 
                                       name="phone" 
                                       id="phone" 
                                       value="{{ old('phone') }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-skywave focus:ring-skywave font-body">
                                @error('phone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Role & Permissions -->
                    <div class="mb-8">
                        <h3 class="text-lg font-heading font-medium text-black-coal mb-4">Rola i uprawnienia</h3>
                        
                        <div>
                            <label for="role" class="block text-sm font-body font-medium text-gray-700 mb-2">
                                Rola <span class="text-red-500">*</span>
                            </label>
                            <select name="role" 
                                    id="role" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-skywave focus:ring-skywave font-body" 
                                    required>
                                <option value="">Wybierz rolę</option>
                                @foreach($roles as $value => $display)
                                <option value="{{ $value }}" {{ old('role') == $value ? 'selected' : '' }}>
                                    {{ $display }}
                                </option>
                                @endforeach
                            </select>
                            @error('role')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror

                            <!-- Role descriptions -->
                            <div class="mt-3 bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <div class="text-sm font-body text-blue-700">
                                    <h4 class="font-medium mb-2">Opisy ról:</h4>
                                    <ul class="list-disc list-inside space-y-1">
                                        <li><strong>Standardowy użytkownik:</strong> Może tworzyć przesyłki i przeglądać historię</li>
                                        <li><strong>Księgowy:</strong> Dostęp do faktur, płatności i raportów finansowych</li>
                                        <li><strong>Magazynier:</strong> Zarządzanie przesyłkami i stanami magazynowymi</li>
                                        <li><strong>Tylko do odczytu:</strong> Może przeglądać dane bez możliwości edycji</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="mb-8">
                        <h3 class="text-lg font-heading font-medium text-black-coal mb-4">Hasło</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Password -->
                            <div>
                                <label for="password" class="block text-sm font-body font-medium text-gray-700 mb-2">
                                    Hasło <span class="text-red-500">*</span>
                                </label>
                                <input type="password" 
                                       name="password" 
                                       id="password" 
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-skywave focus:ring-skywave font-body" 
                                       required>
                                @error('password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Confirm Password -->
                            <div>
                                <label for="password_confirmation" class="block text-sm font-body font-medium text-gray-700 mb-2">
                                    Potwierdź hasło <span class="text-red-500">*</span>
                                </label>
                                <input type="password" 
                                       name="password_confirmation" 
                                       id="password_confirmation" 
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-skywave focus:ring-skywave font-body" 
                                       required>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex items-center justify-end space-x-4">
                        <a href="{{ route('customer.users.index') }}" 
                           class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-body text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 transition">
                            Anuluj
                        </a>
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 bg-skywave border border-transparent rounded-md font-body text-xs text-white uppercase tracking-widest hover:bg-skywave/90 transition">
                            <i class="fas fa-save mr-2"></i>
                            Dodaj użytkownika
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection