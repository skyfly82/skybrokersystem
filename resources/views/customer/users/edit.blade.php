@extends('layouts.customer')

@section('title', 'Edytuj użytkownika')

@section('content')
<div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="md:flex md:items-center md:justify-between mb-6">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                Edytuj: {{ $user->full_name }}
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Zaktualizuj informacje o użytkowniku
            </p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4">
            <a href="{{ route('customer.users.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Powrót do listy
            </a>
        </div>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <form method="POST" action="{{ route('customer.users.update', $user) }}" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div class="px-4 py-5 sm:p-6">
                @if($user->is_primary)
                    <div class="mb-6 bg-amber-50 border border-amber-200 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-crown text-amber-400"></i>
                            </div>
                            <div class="ml-3">
                                <h4 class="text-sm font-medium text-amber-800">Konto główne</h4>
                                <p class="mt-1 text-sm text-amber-700">
                                    To jest główne konto firmowe. Niektóre ustawienia mogą być ograniczone.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <!-- First Name -->
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700">Imię</label>
                        <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $user->first_name) }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('first_name') border-red-500 @enderror">
                        @error('first_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Last Name -->
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700">Nazwisko</label>
                        <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $user->last_name) }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('last_name') border-red-500 @enderror">
                        @error('last_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="sm:col-span-2">
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('email') border-red-500 @enderror">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700">Telefon</label>
                        <input type="tel" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('phone') border-red-500 @enderror">
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Role -->
                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700">Rola</label>
                        <select name="role" id="role" 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('role') border-red-500 @enderror"
                                {{ $user->is_primary ? 'disabled' : '' }}>
                            <option value="">Wybierz rolę</option>
                            <option value="user" {{ old('role', $user->role) === 'user' ? 'selected' : '' }}>👤 Użytkownik</option>
                            <option value="magazynier" {{ old('role', $user->role) === 'magazynier' ? 'selected' : '' }}>📦 Magazynier</option>
                            <option value="ksiegowa" {{ old('role', $user->role) === 'ksiegowa' ? 'selected' : '' }}>🧮 Księgowa</option>
                            <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>⚙️ Administrator</option>
                        </select>
                        @if($user->is_primary)
                            <input type="hidden" name="role" value="{{ $user->role }}">
                            <p class="mt-1 text-xs text-gray-500">Rola konta głównego nie może być zmieniona</p>
                        @else
                            @error('role')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">
                                <strong>Administrator:</strong> Pełny dostęp, zarządzanie użytkownikami<br>
                                <strong>Magazynier:</strong> Zarządzanie przesyłkami i magazynem<br>
                                <strong>Księgowa:</strong> Dostęp do płatności i raportów finansowych<br>
                                <strong>Użytkownik:</strong> Podstawowy dostęp do funkcji
                            </p>
                        @endif
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Nowe hasło</label>
                        <input type="password" name="password" id="password" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('password') border-red-500 @enderror"
                               placeholder="Pozostaw puste aby nie zmieniać">
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Potwierdź hasło</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                               placeholder="Pozostaw puste aby nie zmieniać">
                    </div>

                    <!-- Status -->
                    @if(!$user->is_primary)
                    <div class="sm:col-span-2">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="is_active" name="is_active" type="checkbox" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                                       class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="is_active" class="font-medium text-gray-700">Aktywne konto</label>
                                <p class="text-gray-500">Użytkownik będzie mógł się zalogować i korzystać z systemu</p>
                            </div>
                        </div>
                    </div>
                    @else
                    <input type="hidden" name="is_active" value="1">
                    @endif
                </div>

                <!-- User Info -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Data utworzenia</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $user->created_at->format('d.m.Y H:i') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Ostatnie logowanie</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $user->last_login_at ? $user->last_login_at->format('d.m.Y H:i') : 'Nigdy' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Status email</dt>
                            <dd class="mt-1 text-sm">
                                @if($user->email_verified_at)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        Zweryfikowany
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        Niezweryfikowany
                                    </span>
                                @endif
                            </dd>
                        </div>
                        @if($user->is_primary)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Typ konta</dt>
                            <dd class="mt-1 text-sm">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                    <i class="fas fa-crown mr-1"></i>
                                    Konto główne
                                </span>
                            </dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>

            <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('customer.users.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Anuluj
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-save mr-2"></i>
                        Zapisz zmiany
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection