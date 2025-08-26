@extends('layouts.customer')

@section('title', 'Zarządzanie użytkownikami')

@section('header')
    <div class="flex items-center justify-between">
        <h2 class="font-heading font-semibold text-xl text-black-coal leading-tight">
            {{ __('Zarządzanie użytkownikami') }}
        </h2>
        <a href="{{ route('customer.users.create') }}" 
           class="inline-flex items-center px-4 py-2 bg-skywave border border-transparent rounded-md font-body text-xs text-white uppercase tracking-widest hover:bg-skywave/90 focus:bg-skywave/90 active:bg-skywave/90 focus:outline-none focus:ring-2 focus:ring-skywave focus:ring-offset-2 transition ease-in-out duration-150">
            <i class="fas fa-plus mr-2"></i>
            Dodaj użytkownika
        </a>
    </div>
@endsection

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="mb-6">
                    <h3 class="text-lg font-heading font-medium text-black-coal">Użytkownicy firmy</h3>
                    <p class="mt-1 font-body text-sm text-gray-600">
                        Zarządzaj użytkownikami mającymi dostęp do konta firmowego.
                    </p>
                </div>

                <!-- Users List -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-body font-medium text-gray-500 uppercase tracking-wider">
                                    Użytkownik
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-body font-medium text-gray-500 uppercase tracking-wider">
                                    Email
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-body font-medium text-gray-500 uppercase tracking-wider">
                                    Rola
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-body font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-body font-medium text-gray-500 uppercase tracking-wider">
                                    Ostatnie logowanie
                                </th>
                                <th class="relative px-6 py-3">
                                    <span class="sr-only">Akcje</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse(auth()->user()->customer->users as $user)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-skywave flex items-center justify-center">
                                                <span class="text-sm font-body font-medium text-white">
                                                    {{ strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1)) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-body font-medium text-gray-900">
                                                {{ $user->first_name }} {{ $user->last_name }}
                                                @if($user->is_primary)
                                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-body font-medium bg-amber-100 text-amber-800">
                                                        <i class="fas fa-crown mr-1"></i>
                                                        Konto główne
                                                    </span>
                                                @endif
                                            </div>
                                            @if($user->phone)
                                            <div class="text-sm font-body text-gray-500">
                                                {{ $user->phone }}
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-body text-gray-900">{{ $user->email }}</div>
                                    @if($user->email_verified_at)
                                        <div class="text-xs text-green-600">
                                            <i class="fas fa-check-circle mr-1"></i>Zweryfikowany
                                        </div>
                                    @else
                                        <div class="text-xs text-red-600">
                                            <i class="fas fa-exclamation-circle mr-1"></i>Niezweryfikowany
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-body font-medium 
                                        @switch($user->role)
                                            @case('admin')
                                                bg-purple-100 text-purple-800
                                                @break
                                            @case('magazynier')
                                                bg-green-100 text-green-800
                                                @break
                                            @case('ksiegowa')
                                                bg-indigo-100 text-indigo-800
                                                @break
                                            @default
                                                bg-blue-100 text-blue-800
                                        @endswitch">
                                        @switch($user->role)
                                            @case('admin')
                                                <i class="fas fa-user-cog mr-1"></i>
                                                Administrator
                                                @break
                                            @case('magazynier')
                                                <i class="fas fa-boxes mr-1"></i>
                                                Magazynier
                                                @break
                                            @case('ksiegowa')
                                                <i class="fas fa-calculator mr-1"></i>
                                                Księgowa
                                                @break
                                            @default
                                                <i class="fas fa-user mr-1"></i>
                                                Użytkownik
                                        @endswitch
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-body font-medium 
                                        {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $user->is_active ? 'Aktywny' : 'Nieaktywny' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-body text-gray-500">
                                    {{ $user->last_login_at ? $user->last_login_at->format('d.m.Y H:i') : 'Nigdy' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-body font-medium">
                                    @if(!$user->is_primary)
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('customer.users.edit', $user) }}" 
                                           class="text-skywave hover:text-skywave/80">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="{{ route('customer.users.destroy', $user) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    onclick="return confirm('Czy na pewno chcesz usunąć tego użytkownika?')"
                                                    class="text-red-600 hover:text-red-900">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                    @else
                                    <span class="text-gray-400 text-xs">Konto główne</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center">
                                    <div class="py-8">
                                        <i class="fas fa-users text-gray-300 text-4xl mb-4"></i>
                                        <h3 class="text-lg font-heading font-medium text-gray-900 mb-2">Brak użytkowników</h3>
                                        <p class="font-body text-gray-500 mb-4">
                                            Dodaj pierwszego użytkownika do swojego zespołu.
                                        </p>
                                        <a href="{{ route('customer.users.create') }}" 
                                           class="inline-flex items-center px-4 py-2 bg-skywave border border-transparent rounded-md font-body text-sm text-white hover:bg-skywave/90 transition">
                                            <i class="fas fa-plus mr-2"></i>
                                            Dodaj użytkownika
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Info Box -->
                <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle text-blue-400"></i>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-sm font-body font-medium text-blue-800">Informacje o rolach użytkowników</h4>
                            <div class="mt-2 text-sm font-body text-blue-700">
                                <ul class="list-disc list-inside space-y-1">
                                    <li><strong>Administrator:</strong> Pełny dostęp - zarządzanie użytkownikami, przesyłkami i płatnościami</li>
                                    <li><strong>Magazynier:</strong> Dostęp do przesyłek - tworzenie, zarządzanie i śledzenie przesyłek</li>
                                    <li><strong>Księgowa:</strong> Dostęp finansowy - płatności, faktury i raporty finansowe</li>
                                    <li><strong>Użytkownik:</strong> Podstawowy dostęp - przeglądanie przesyłek i podstawowe operacje</li>
                                    <li><strong><i class="fas fa-crown text-amber-500"></i> Konto główne:</strong> Nie można usuwać ani zmieniać roli konta głównego</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection