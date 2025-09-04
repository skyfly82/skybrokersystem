@extends('layouts.admin')

@section('title', 'Zarządzanie klientami')

@section('content')
<div class="space-y-6" x-data="customersIndex">
    <!-- Header -->
    <div class="bg-white shadow rounded-lg p-6">
        <div class="md:flex md:items-center md:justify-between">
            <div class="flex-1 min-w-0">
                <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                    Zarządzanie klientami
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Przeglądaj i zarządzaj kontami klientów
                </p>
            </div>
            <div class="mt-4 flex space-x-3 md:mt-0 md:ml-4">
                <button @click="showBulkActions = !showBulkActions" 
                        x-show="selectedCustomers.length > 0"
                        class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium inline-flex items-center">
                    <i class="fas fa-list mr-2"></i>
                    Akcje grupowe (<span x-text="selectedCustomers.length"></span>)
                </button>
                <a href="{{ route('admin.customers.create') }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium inline-flex items-center">
                    <i class="fas fa-plus mr-2"></i>
                    Dodaj klienta
                </a>
            </div>
        </div>
    </div>

    <!-- Status Cards -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <div class="bg-white overflow-hidden shadow rounded-lg border-l-4 border-blue-400">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-users text-white text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Wszyscy klienci</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $statusCounts['all'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-5 py-3">
                <div class="text-sm">
                    <a href="{{ route('admin.customers.index') }}" class="text-blue-600 hover:text-blue-500">
                        Zobacz wszystkich
                    </a>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg border-l-4 border-green-400">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-check text-white text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Aktywni</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $statusCounts['active'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-5 py-3">
                <div class="text-sm">
                    <a href="{{ route('admin.customers.index', ['status' => 'active']) }}" class="text-green-600 hover:text-green-500">
                        Zobacz aktywnych
                    </a>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg border-l-4 border-yellow-400">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-clock text-white text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Oczekujący</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $statusCounts['pending'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-5 py-3">
                <div class="text-sm">
                    <a href="{{ route('admin.customers.index', ['status' => 'pending']) }}" class="text-yellow-600 hover:text-yellow-500">
                        Wymagają akcji
                    </a>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg border-l-4 border-red-400">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-ban text-white text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Zawieszeni</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $statusCounts['suspended'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-5 py-3">
                <div class="text-sm">
                    <a href="{{ route('admin.customers.index', ['status' => 'suspended']) }}" class="text-red-600 hover:text-red-500">
                        Zobacz zawieszonych
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="bg-white shadow rounded-lg p-6">
        <form method="GET" action="{{ route('admin.customers.index') }}" class="space-y-4 md:space-y-0 md:flex md:items-center md:space-x-4">
            <!-- Search -->
            <div class="flex-1">
                <label for="search" class="sr-only">Szukaj</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" 
                           name="search" 
                           id="search"
                           value="{{ request('search') }}"
                           placeholder="Szukaj po nazwie firmy, NIP, email..."
                           class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <!-- Status Filter -->
            <div class="min-w-0 flex-1 md:max-w-xs">
                <label for="status" class="sr-only">Status</label>
                <select name="status" 
                        id="status"
                        class="block w-full border border-gray-300 rounded-md py-2 pl-3 pr-10 text-base focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Wszystkie statusy</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktywni</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Oczekujący</option>
                    <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Zawieszeni</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nieaktywni</option>
                </select>
            </div>

            <!-- Date Range -->
            <div class="min-w-0 flex-1 md:max-w-xs">
                <label for="date_from" class="sr-only">Data od</label>
                <input type="date" 
                       name="date_from" 
                       id="date_from"
                       value="{{ request('date_from') }}"
                       class="block w-full border border-gray-300 rounded-md py-2 pl-3 pr-3 text-base focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Filter Buttons -->
            <div class="flex space-x-2">
                <button type="submit" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                    <i class="fas fa-filter mr-2"></i>
                    Filtruj
                </button>
                @if(request()->anyFilled(['search', 'status', 'date_from']))
                <a href="{{ route('admin.customers.index') }}" 
                   class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-md text-sm font-medium">
                    <i class="fas fa-times mr-2"></i>
                    Wyczyść
                </a>
                @endif
                <button type="button" 
                        @click="exportCustomers()"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                    <i class="fas fa-download mr-2"></i>
                    Eksport
                </button>
            </div>
        </form>
    </div>

    <!-- Bulk Actions Panel -->
    <div x-show="showBulkActions" 
         x-transition
         class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-sm font-medium text-yellow-800">
                    Akcje grupowe (<span x-text="selectedCustomers.length"></span> zaznaczonych)
                </h3>
            </div>
            <div class="flex space-x-2">
                <button @click="bulkApprove()" 
                        class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm">
                    <i class="fas fa-check mr-1"></i>
                    Zatwierdź
                </button>
                <button @click="bulkSuspend()" 
                        class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm">
                    <i class="fas fa-ban mr-1"></i>
                    Zawieś
                </button>
                <button @click="bulkExport()" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm">
                    <i class="fas fa-download mr-1"></i>
                    Eksportuj
                </button>
                <button @click="clearSelection()" 
                        class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-3 py-1 rounded text-sm">
                    <i class="fas fa-times mr-1"></i>
                    Anuluj
                </button>
            </div>
        </div>
    </div>

    <!-- Results Count -->
    @if($customers->total() > 0)
    <div class="text-sm text-gray-500">
        Znaleziono {{ $customers->total() }} klientów
        @if(request()->anyFilled(['search', 'status', 'date_from']))
            (filtrowane)
        @endif
    </div>
    @endif

    <!-- Customers Table -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        @if($customers->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left">
                            <input type="checkbox" 
                                   @change="toggleSelectAll()"
                                   :checked="isAllSelected"
                                   class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Firma
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Kontakt
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Saldo
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Przesyłki
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Data rejestracji
                        </th>
                        <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only">Akcje</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($customers as $customer)
                    <tr class="hover:bg-gray-50" 
                        :class="{ 'bg-blue-50': selectedCustomers.includes({{ $customer->id }}) }">
                        <!-- Checkbox -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="checkbox" 
                                   value="{{ $customer->id }}"
                                   x-model="selectedCustomers"
                                   class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                        </td>

                        <!-- Company Info -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                        <span class="text-sm font-medium text-gray-700">
                                            {{ substr($customer->company_name, 0, 2) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $customer->company_name }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        @if($customer->company_short_name)
                                            {{ $customer->company_short_name }} •
                                        @endif
                                        NIP: {{ $customer->nip ?: 'Brak' }}
                                    </div>
                                </div>
                            </div>
                        </td>

                        <!-- Contact Info -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $customer->email }}</div>
                            <div class="text-sm text-gray-500">{{ $customer->phone }}</div>
                            @if($customer->primaryUser)
                            <div class="text-xs text-gray-400">
                                {{ $customer->primaryUser->full_name }}
                            </div>
                            @endif
                        </td>

                        <!-- Status -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @switch($customer->status)
                                    @case('active')
                                        bg-green-100 text-green-800
                                        @break
                                    @case('pending')
                                        bg-yellow-100 text-yellow-800
                                        @break
                                    @case('suspended')
                                        bg-red-100 text-red-800
                                        @break
                                    @case('inactive')
                                        bg-gray-100 text-gray-800
                                        @break
                                    @default
                                        bg-gray-100 text-gray-800
                                @endswitch">
                                <div class="w-2 h-2 rounded-full mr-1.5
                                    @switch($customer->status)
                                        @case('active')
                                            bg-green-400
                                            @break
                                        @case('pending')
                                            bg-yellow-400
                                            @break
                                        @case('suspended')
                                            bg-red-400
                                            @break
                                        @case('inactive')
                                            bg-gray-400
                                            @break
                                        @default
                                            bg-gray-400
                                    @endswitch"></div>
                                @switch($customer->status)
                                    @case('active')
                                        Aktywny
                                        @break
                                    @case('pending')
                                        Oczekujący
                                        @break
                                    @case('suspended')
                                        Zawieszony
                                        @break
                                    @case('inactive')
                                        Nieaktywny
                                        @break
                                    @default
                                        {{ ucfirst($customer->status) }}
                                @endswitch
                            </span>
                            @if($customer->verified_at)
                            <div class="text-xs text-green-600 mt-1">
                                <i class="fas fa-check-circle mr-1"></i>
                                Zweryfikowany
                            </div>
                            @endif
                        </td>

                        <!-- Balance -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium
                                @if($customer->current_balance < 0)
                                    text-red-600
                                @elseif($customer->current_balance < 100)
                                    text-yellow-600
                                @else
                                    text-green-600
                                @endif">
                                {{ number_format($customer->current_balance, 2) }} PLN
                            </div>
                            @if($customer->credit_limit > 0)
                            <div class="text-xs text-gray-500">
                                Limit: {{ number_format($customer->credit_limit, 2) }} PLN
                            </div>
                            @endif
                        </td>

                        <!-- Shipments Stats -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <div class="flex items-center space-x-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $customer->getTotalShipmentsCount() }}
                                </span>
                                <span class="text-xs text-gray-400">
                                    ({{ $customer->getMonthlyShipmentsCount() }} w tym mies.)
                                </span>
                            </div>
                        </td>

                        <!-- Registration Date -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <div>{{ $customer->created_at->format('d.m.Y') }}</div>
                            <div class="text-xs">{{ $customer->created_at->format('H:i') }}</div>
                        </td>

                        <!-- Actions -->
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end space-x-2" x-data="{ open: false }">
                                <!-- Quick Actions -->
                                <a href="{{ route('admin.customers.show', $customer) }}" 
                                   class="text-blue-600 hover:text-blue-900 p-1"
                                   title="Zobacz szczegóły">
                                    <i class="fas fa-eye"></i>
                                </a>

                                @if($customer->status === 'pending')
                                <form action="{{ route('admin.customers.approve', $customer) }}" 
                                      method="POST" 
                                      class="inline">
                                    @csrf
                                    <button type="submit" 
                                            class="text-green-600 hover:text-green-900 p-1"
                                            title="Zatwierdź klienta (tylko po weryfikacji email)"
                                            onclick="return confirm('Zatwierdź klienta {{ $customer->company_name }}?')">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                                @endif

                                <!-- More Actions Dropdown -->
                                <div class="relative">
                                    <button @click="open = !open" 
                                            class="text-gray-400 hover:text-gray-600 p-1"
                                            title="Więcej opcji">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    
                                    <div x-show="open" 
                                         @click.away="open = false"
                                         x-transition:enter="transition ease-out duration-100"
                                         x-transition:enter-start="transform opacity-0 scale-95"
                                         x-transition:enter-end="transform opacity-100 scale-100"
                                         x-transition:leave="transition ease-in duration-75"
                                         x-transition:leave-start="transform opacity-100 scale-100"
                                         x-transition:leave-end="transform opacity-0 scale-95"
                                         class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 z-10">
                                        <div class="py-1">
                                            <a href="{{ route('admin.customers.edit', $customer) }}" 
                                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                <i class="fas fa-edit mr-2"></i>
                                                Edytuj dane
                                            </a>
                                            
                                            <a href="#" 
                                               @click="showAddBalanceModal({{ $customer->id }})"
                                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                <i class="fas fa-wallet mr-2"></i>
                                                Dodaj saldo
                                            </a>
                                            
                                            <form action="{{ route('admin.customers.regenerate-api-key', $customer) }}" 
                                                  method="POST" 
                                                  onsubmit="return confirm('Czy na pewno chcesz wygenerować nowy klucz API?')">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" 
                                                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                    <i class="fas fa-key mr-2"></i>
                                                    Regeneruj API key
                                                </button>
                                            </form>
                                            
                                            @if($customer->status !== 'suspended')
                                            <form action="{{ route('admin.customers.suspend', $customer) }}" 
                                                  method="POST" 
                                                  onsubmit="return confirm('Czy na pewno chcesz zawiesić tego klienta?')">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" 
                                                        class="block w-full text-left px-4 py-2 text-sm text-red-700 hover:bg-gray-100">
                                                    <i class="fas fa-ban mr-2"></i>
                                                    Zawieś klienta
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $customers->appends(request()->query())->links() }}
        </div>

        @else
        <!-- Empty State -->
        <div class="text-center py-12">
            <div class="mx-auto h-12 w-12 text-gray-400">
                <i class="fas fa-users text-4xl"></i>
            </div>
            <h3 class="mt-2 text-sm font-medium text-gray-900">
                @if(request()->anyFilled(['search', 'status', 'date_from']))
                    Brak klientów spełniających kryteria
                @else
                    Brak klientów
                @endif
            </h3>
            <p class="mt-1 text-sm text-gray-500">
                @if(request()->anyFilled(['search', 'status', 'date_from']))
                    Spróbuj zmienić filtry wyszukiwania.
                @else
                    Rozpocznij od dodania pierwszego klienta.
                @endif
            </p>
            <div class="mt-6">
                @if(request()->anyFilled(['search', 'status', 'date_from']))
                <a href="{{ route('admin.customers.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    <i class="fas fa-times mr-2"></i>
                    Wyczyść filtry
                </a>
                @else
                <a href="{{ route('admin.customers.create') }}" 
                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    <i class="fas fa-plus mr-2"></i>
                    Dodaj klienta
                </a>
                @endif
            </div>
        </div>
        @endif
    </div>

    <!-- Add Balance Modal -->
    <div x-show="showBalanceModal" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeBalanceModal()"></div>
            
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form method="POST" :action="`/admin/customers/${selectedCustomerId}/add-balance`">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                                <i class="fas fa-wallet text-green-600"></i>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">
                                    Dodaj saldo do konta
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        Wprowadź kwotę do dodania na konto klienta.
                                    </p>
                                </div>
                                <div class="mt-4 space-y-4">
                                    <div>
                                        <label for="amount" class="block text-sm font-medium text-gray-700">Kwota (PLN)</label>
                                        <input type="number" 
                                               name="amount" 
                                               id="amount"
                                               step="0.01"
                                               min="0.01"
                                               required
                                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                                    </div>
                                    <div>
                                        <label for="description" class="block text-sm font-medium text-gray-700">Opis (opcjonalnie)</label>
                                        <input type="text" 
                                               name="description" 
                                               id="description"
                                               placeholder="Powód dodania salda..."
                                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" 
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Dodaj saldo
                        </button>
                        <button type="button" 
                                @click="closeBalanceModal()"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Anuluj
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('customersIndex', () => ({
        selectedCustomers: [],
        showBulkActions: false,
        showBalanceModal: false,
        selectedCustomerId: null,
        
        get isAllSelected() {
            const currentPageIds = Array.from(document.querySelectorAll('input[type="checkbox"][value]'))
                .map(cb => parseInt(cb.value));
            return currentPageIds.length > 0 && currentPageIds.every(id => this.selectedCustomers.includes(id));
        },
        
        toggleSelectAll() {
            const currentPageIds = Array.from(document.querySelectorAll('input[type="checkbox"][value]'))
                .map(cb => parseInt(cb.value));
            
            if (this.isAllSelected) {
                this.selectedCustomers = this.selectedCustomers.filter(id => !currentPageIds.includes(id));
            } else {
                currentPageIds.forEach(id => {
                    if (!this.selectedCustomers.includes(id)) {
                        this.selectedCustomers.push(id);
                    }
                });
            }
            
            this.showBulkActions = this.selectedCustomers.length > 0;
        },
        
        clearSelection() {
            this.selectedCustomers = [];
            this.showBulkActions = false;
        },
        
        showAddBalanceModal(customerId) {
            this.selectedCustomerId = customerId;
            this.showBalanceModal = true;
        },
        
        closeBalanceModal() {
            this.showBalanceModal = false;
            this.selectedCustomerId = null;
        },
        
        async bulkApprove() {
            if (this.selectedCustomers.length === 0) return;
            
            if (!confirm(`Czy na pewno chcesz zatwierdzić ${this.selectedCustomers.length} klientów?`)) {
                return;
            }
            
            try {
                const response = await fetch('/admin/customers/bulk-approve', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        customer_ids: this.selectedCustomers
                    })
                });
                
                if (response.ok) {
                    location.reload();
                } else {
                    alert('Błąd podczas zatwierdzania klientów');
                }
            } catch (error) {
                console.error('Bulk approve error:', error);
                alert('Błąd podczas zatwierdzania klientów');
            }
        },
        
        async bulkSuspend() {
            if (this.selectedCustomers.length === 0) return;
            
            if (!confirm(`Czy na pewno chcesz zawiesić ${this.selectedCustomers.length} klientów?`)) {
                return;
            }
            
            try {
                const response = await fetch('/admin/customers/bulk-suspend', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        customer_ids: this.selectedCustomers
                    })
                });
                
                if (response.ok) {
                    location.reload();
                } else {
                    alert('Błąd podczas zawieszania klientów');
                }
            } catch (error) {
                console.error('Bulk suspend error:', error);
                alert('Błąd podczas zawieszania klientów');
            }
        },
        
        async bulkExport() {
            if (this.selectedCustomers.length === 0) return;
            
            try {
                const response = await fetch('/admin/customers/export', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        customer_ids: this.selectedCustomers
                    })
                });
                
                if (response.ok) {
                    const blob = await response.blob();
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = `klienci-${new Date().toISOString().slice(0, 10)}.csv`;
                    a.click();
                    window.URL.revokeObjectURL(url);
                } else {
                    alert('Błąd podczas eksportu');
                }
            } catch (error) {
                console.error('Export error:', error);
                alert('Błąd podczas eksportu');
            }
        },
        
        async exportCustomers() {
            try {
                const currentParams = new URLSearchParams(window.location.search);
                const response = await fetch(`/admin/customers/export?${currentParams.toString()}`);
                
                if (response.ok) {
                    const blob = await response.blob();
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = `wszyscy-klienci-${new Date().toISOString().slice(0, 10)}.csv`;
                    a.click();
                    window.URL.revokeObjectURL(url);
                } else {
                    alert('Błąd podczas eksportu');
                }
            } catch (error) {
                console.error('Export error:', error);
                alert('Błąd podczas eksportu');
            }
        }
    }));
});

// Auto-submit form on filter change
document.getElementById('status')?.addEventListener('change', function() {
    this.form.submit();
});

// Watch for changes in selection to update bulk actions visibility
document.addEventListener('change', function(e) {
    if (e.target.type === 'checkbox' && e.target.hasAttribute('x-model')) {
        const customersIndex = Alpine.$data(document.querySelector('[x-data="customersIndex"]'));
        customersIndex.showBulkActions = customersIndex.selectedCustomers.length > 0;
    }
});

</script>
@endpush
@endsection