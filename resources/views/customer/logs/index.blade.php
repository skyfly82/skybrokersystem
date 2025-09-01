@extends('layouts.customer')

@section('title', 'Log systemowy')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Log systemowy</h2>
            <p class="mt-1 text-sm text-gray-600">
                Historia zmian w systemie - użytkownicy, dane firmy, finanse.
            </p>
        </div>
        <div class="flex items-center space-x-3">
            <div class="text-sm text-gray-500">
                <i class="fas fa-info-circle mr-1"></i>
                Ostatnie 20 wpisów na stronę
            </div>
        </div>
    </div>

    <!-- Logs List -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
            <h3 class="text-lg font-heading font-medium text-black-coal mb-6">
                <i class="fas fa-history text-skywave mr-2"></i>
                Historia zmian
            </h3>

            @forelse($logs as $log)
            <div class="border-l-4 pl-4 mb-6 {{ $log->event === 'created' ? 'border-green-400' : ($log->event === 'updated' ? 'border-blue-400' : 'border-red-400') }}">
                <div class="bg-gray-50 rounded-lg p-4">
                    <!-- Header -->
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0">
                                @if($log->event === 'created')
                                    <div class="h-8 w-8 rounded-full bg-green-100 flex items-center justify-center">
                                        <i class="fas fa-plus text-green-600 text-sm"></i>
                                    </div>
                                @elseif($log->event === 'updated')
                                    <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center">
                                        <i class="fas fa-edit text-blue-600 text-sm"></i>
                                    </div>
                                @else
                                    <div class="h-8 w-8 rounded-full bg-red-100 flex items-center justify-center">
                                        <i class="fas fa-trash text-red-600 text-sm"></i>
                                    </div>
                                @endif
                            </div>
                            
                            <div>
                                <div class="text-sm font-medium text-gray-900">
                                    @if($log->auditable_type === 'App\\Models\\Customer')
                                        <i class="fas fa-building mr-1 text-purple-500"></i>
                                        {{ $log->formatted_event }} danych firmy
                                    @elseif($log->auditable_type === 'App\\Models\\CustomerUser')
                                        <i class="fas fa-user mr-1 text-blue-500"></i>
                                        {{ $log->formatted_event }} użytkownika
                                    @else
                                        {{ $log->formatted_event }}
                                    @endif
                                </div>
                                
                                <div class="text-xs text-gray-500 mt-1">
                                    przez {{ $log->user_name }} ({{ $log->user_email }})
                                    @if($log->user_type === 'system_user')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-800 ml-2">
                                            Administrator SkyBroker
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-xs text-gray-500">
                            {{ $log->created_at->format('d.m.Y H:i:s') }}
                            <div class="text-xs text-gray-400 mt-1">
                                {{ $log->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    @if($log->description)
                        <div class="mb-3">
                            <p class="text-sm text-gray-600">{{ $log->description }}</p>
                        </div>
                    @endif

                    <!-- Changed Fields -->
                    @if($log->changed_fields && count($log->changed_fields) > 0)
                        <div class="space-y-2">
                            <h4 class="text-xs font-medium text-gray-700 uppercase tracking-wide">Zmienione pola:</h4>
                            <div class="grid gap-2">
                                @foreach($log->changed_fields as $field => $change)
                                    <div class="bg-white rounded border p-3">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-sm font-medium text-gray-900">
                                                @if($field === 'cod_return_account')
                                                    <i class="fas fa-undo text-orange-500 mr-1"></i>
                                                    Konto zwrotów COD
                                                @elseif($field === 'settlement_account')
                                                    <i class="fas fa-calculator text-blue-500 mr-1"></i>
                                                    Konto rozliczeniowe
                                                @elseif($field === 'email')
                                                    <i class="fas fa-envelope text-gray-500 mr-1"></i>
                                                    E-mail
                                                @elseif($field === 'phone')
                                                    <i class="fas fa-phone text-gray-500 mr-1"></i>
                                                    Telefon
                                                @elseif($field === 'address')
                                                    <i class="fas fa-map-marker-alt text-gray-500 mr-1"></i>
                                                    Adres
                                                @elseif($field === 'company_name')
                                                    <i class="fas fa-building text-purple-500 mr-1"></i>
                                                    Nazwa firmy
                                                @elseif($field === 'tax_number')
                                                    <i class="fas fa-hashtag text-gray-500 mr-1"></i>
                                                    NIP
                                                @elseif($field === 'first_name')
                                                    <i class="fas fa-user text-blue-500 mr-1"></i>
                                                    Imię
                                                @elseif($field === 'last_name')
                                                    <i class="fas fa-user text-blue-500 mr-1"></i>
                                                    Nazwisko
                                                @elseif($field === 'role')
                                                    <i class="fas fa-user-tag text-purple-500 mr-1"></i>
                                                    Rola
                                                @elseif($field === 'is_active')
                                                    <i class="fas fa-power-off text-red-500 mr-1"></i>
                                                    Status aktywności
                                                @else
                                                    {{ $log->getFieldLabelAttribute($field) }}
                                                @endif
                                            </span>
                                            @if(in_array($field, ['cod_return_account', 'settlement_account']))
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                                    Dane finansowe
                                                </span>
                                            @endif
                                        </div>
                                        
                                        @if(isset($change['old']) || isset($change['new']))
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                                                <div>
                                                    <div class="text-xs text-gray-500 mb-1">Stara wartość:</div>
                                                    <div class="bg-red-50 border border-red-200 rounded p-2 font-mono text-xs">
                                                        {{ $change['old'] ?? 'brak' }}
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="text-xs text-gray-500 mb-1">Nowa wartość:</div>
                                                    <div class="bg-green-50 border border-green-200 rounded p-2 font-mono text-xs">
                                                        {{ $change['new'] ?? 'brak' }}
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Additional Info -->
                    @if($log->ip_address || $log->user_agent)
                        <div class="mt-3 pt-3 border-t border-gray-200">
                            <div class="text-xs text-gray-500 space-y-1">
                                @if($log->ip_address)
                                    <div><i class="fas fa-globe mr-1"></i> IP: {{ $log->ip_address }}</div>
                                @endif
                                @if($log->user_agent)
                                    <div><i class="fas fa-desktop mr-1"></i> {{ $log->user_agent }}</div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            @empty
            <div class="text-center py-12">
                <div class="mx-auto h-12 w-12 text-gray-400">
                    <i class="fas fa-history text-4xl"></i>
                </div>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Brak logów systemowych</h3>
                <p class="mt-1 text-sm text-gray-500">
                    Nie ma jeszcze żadnych zmian w systemie do wyświetlenia.
                </p>
            </div>
            @endforelse

            <!-- Pagination -->
            @if($logs->hasPages())
                <div class="mt-6 border-t border-gray-200 pt-6">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Legend -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h3 class="text-lg font-heading font-medium text-black-coal mb-4">
                <i class="fas fa-info-circle text-skywave mr-2"></i>
                Legenda
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="flex items-center space-x-3">
                    <div class="h-6 w-6 rounded-full bg-green-100 flex items-center justify-center">
                        <i class="fas fa-plus text-green-600 text-xs"></i>
                    </div>
                    <span class="text-sm text-gray-700">Utworzenie nowego rekordu</span>
                </div>
                
                <div class="flex items-center space-x-3">
                    <div class="h-6 w-6 rounded-full bg-blue-100 flex items-center justify-center">
                        <i class="fas fa-edit text-blue-600 text-xs"></i>
                    </div>
                    <span class="text-sm text-gray-700">Modyfikacja istniejącego rekordu</span>
                </div>
                
                <div class="flex items-center space-x-3">
                    <div class="h-6 w-6 rounded-full bg-red-100 flex items-center justify-center">
                        <i class="fas fa-trash text-red-600 text-xs"></i>
                    </div>
                    <span class="text-sm text-gray-700">Usunięcie rekordu</span>
                </div>
            </div>
            
            <div class="mt-4 p-3 bg-yellow-50 rounded-lg border border-yellow-200">
                <div class="flex">
                    <i class="fas fa-shield-alt text-yellow-400 mt-0.5 mr-2"></i>
                    <div class="text-sm">
                        <p class="text-yellow-800 font-medium">Szczególna uwaga:</p>
                        <ul class="text-yellow-700 mt-1 space-y-1 text-xs">
                            <li>• Zmiany kont bankowych (COD i rozliczeniowe) są szczególnie monitorowane</li>
                            <li>• Dodawanie i usuwanie użytkowników wymaga dodatkowej autoryzacji</li>
                            <li>• Wszystkie zmiany danych finansowych pozostają w systemie na zawsze</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection