@extends('layouts.customer')

@section('title', 'Przesyłki')

@section('content')
<div class="space-y-6" x-data="shipmentsIndex">
    <!-- Header -->
    <div class="bg-white shadow rounded-lg p-6">
        <div class="md:flex md:items-center md:justify-between">
            <div class="flex-1 min-w-0">
                <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                    Przesyłki
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Zarządzaj i śledź swoje przesyłki
                </p>
            </div>
            <div class="mt-4 flex md:mt-0 md:ml-4">
                <a href="{{ route('customer.shipments.create') }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium inline-flex items-center">
                    <i class="fas fa-plus mr-2"></i>
                    Nowa przesyłka
                </a>
            </div>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="bg-white shadow rounded-lg p-6">
        <form method="GET" action="{{ route('customer.shipments.index') }}" class="space-y-4 md:space-y-0 md:flex md:items-center md:space-x-4">
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
                           placeholder="Szukaj po numerze śledzenia, referencji..."
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
                    <option value="created" {{ request('status') === 'created' ? 'selected' : '' }}>Utworzona</option>
                    <option value="printed" {{ request('status') === 'printed' ? 'selected' : '' }}>Wydrukowana</option>
                    <option value="dispatched" {{ request('status') === 'dispatched' ? 'selected' : '' }}>Nadana</option>
                    <option value="in_transit" {{ request('status') === 'in_transit' ? 'selected' : '' }}>W transporcie</option>
                    <option value="out_for_delivery" {{ request('status') === 'out_for_delivery' ? 'selected' : '' }}>W doręczeniu</option>
                    <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Dostarczona</option>
                    <option value="returned" {{ request('status') === 'returned' ? 'selected' : '' }}>Zwrócona</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Anulowana</option>
                </select>
            </div>

            <!-- Filter Button -->
            <div class="flex space-x-2">
                <button type="submit" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                    <i class="fas fa-filter mr-2"></i>
                    Filtruj
                </button>
                @if(request()->anyFilled(['search', 'status']))
                <a href="{{ route('customer.shipments.index') }}" 
                   class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-md text-sm font-medium">
                    <i class="fas fa-times mr-2"></i>
                    Wyczyść
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Results Count -->
    @if($shipments->total() > 0)
    <div class="text-sm text-gray-500">
        Znaleziono {{ $shipments->total() }} przesyłek
        @if(request()->anyFilled(['search', 'status']))
            (filtrowane)
        @endif
    </div>
    @endif

    <!-- Shipments Table -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        @if($shipments->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Przesyłka
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Odbiorca
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Kurier
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Koszt
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Data utworzenia
                        </th>
                        <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only">Akcje</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($shipments as $shipment)
                    <tr class="hover:bg-gray-50">
                        <!-- Shipment Info -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex flex-col">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $shipment->tracking_number ?: 'Brak numeru' }}
                                </div>
                                @if($shipment->reference_number)
                                <div class="text-sm text-gray-500">
                                    Ref: {{ $shipment->reference_number }}
                                </div>
                                @endif
                            </div>
                        </td>

                        <!-- Recipient -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                {{ $shipment->recipient_data['name'] ?? 'Brak danych' }}
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ $shipment->recipient_data['city'] ?? '' }}
                            </div>
                        </td>

                        <!-- Courier -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                @if($shipment->courierService->logo_url)
                                <img class="h-8 w-8 rounded-full mr-2" 
                                     src="{{ $shipment->courierService->logo_url }}" 
                                     alt="{{ $shipment->courierService->name }}">
                                @endif
                                <div>
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $shipment->courierService->name }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $shipment->service_type }}
                                    </div>
                                </div>
                            </div>
                        </td>

                        <!-- Status -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @switch($shipment->status)
                                    @case('delivered')
                                        bg-green-100 text-green-800
                                        @break
                                    @case('in_transit')
                                    @case('out_for_delivery')
                                        bg-blue-100 text-blue-800
                                        @break
                                    @case('cancelled')
                                    @case('returned')
                                        bg-red-100 text-red-800
                                        @break
                                    @case('created')
                                    @case('printed')
                                        bg-yellow-100 text-yellow-800
                                        @break
                                    @default
                                        bg-gray-100 text-gray-800
                                @endswitch">
                                <div class="w-2 h-2 rounded-full mr-1.5
                                    @switch($shipment->status)
                                        @case('delivered')
                                            bg-green-400
                                            @break
                                        @case('in_transit')
                                        @case('out_for_delivery')
                                            bg-blue-400
                                            @break
                                        @case('cancelled')
                                        @case('returned')
                                            bg-red-400
                                            @break
                                        @case('created')
                                        @case('printed')
                                            bg-yellow-400
                                            @break
                                        @default
                                            bg-gray-400
                                    @endswitch"></div>
                                {{ $shipment->status_label }}
                            </span>
                        </td>

                        <!-- Cost -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @if($shipment->cost_data)
                                {{ number_format($shipment->cost_data['gross'] ?? 0, 2) }} PLN
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>

                        <!-- Date -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <div>{{ $shipment->created_at->format('d.m.Y') }}</div>
                            <div class="text-xs">{{ $shipment->created_at->format('H:i') }}</div>
                        </td>

                        <!-- Actions -->
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end space-x-2" x-data="{ open: false }">
                                <!-- Quick Actions -->
                                @if($shipment->tracking_number)
                                <a href="{{ route('customer.shipments.track', $shipment) }}" 
                                   class="text-blue-600 hover:text-blue-900 p-1"
                                   title="Śledź przesyłkę">
                                    <i class="fas fa-truck"></i>
                                </a>
                                @endif

                                @if($shipment->label_url || $shipment->tracking_number)
                                <a href="{{ route('customer.shipments.label', $shipment) }}" 
                                   class="text-green-600 hover:text-green-900 p-1"
                                   title="Pobierz etykietę"
                                   target="_blank">
                                    <i class="fas fa-download"></i>
                                </a>
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
                                            <a href="{{ route('customer.shipments.show', $shipment) }}" 
                                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                <i class="fas fa-eye mr-2"></i>
                                                Zobacz szczegóły
                                            </a>
                                            
                                            @if($shipment->canBeCancelled())
                                            <form action="{{ route('customer.shipments.cancel', $shipment) }}" 
                                                  method="POST" 
                                                  onsubmit="return confirm('Czy na pewno chcesz anulować tę przesyłkę?')">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" 
                                                        class="block w-full text-left px-4 py-2 text-sm text-red-700 hover:bg-gray-100">
                                                    <i class="fas fa-times mr-2"></i>
                                                    Anuluj przesyłkę
                                                </button>
                                            </form>
                                            @endif
                                            
                                            @if($shipment->isEditable())
                                            <a href="{{ route('customer.shipments.edit', $shipment) }}" 
                                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                <i class="fas fa-edit mr-2"></i>
                                                Edytuj
                                            </a>
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
            {{ $shipments->appends(request()->query())->links() }}
        </div>

        @else
        <!-- Empty State -->
        <div class="text-center py-12">
            <div class="mx-auto h-12 w-12 text-gray-400">
                <i class="fas fa-box text-4xl"></i>
            </div>
            <h3 class="mt-2 text-sm font-medium text-gray-900">
                @if(request()->anyFilled(['search', 'status']))
                    Brak przesyłek spełniających kryteria
                @else
                    Brak przesyłek
                @endif
            </h3>
            <p class="mt-1 text-sm text-gray-500">
                @if(request()->anyFilled(['search', 'status']))
                    Spróbuj zmienić filtry wyszukiwania.
                @else
                    Rozpocznij wysyłanie pierwszej przesyłki.
                @endif
            </p>
            <div class="mt-6">
                @if(request()->anyFilled(['search', 'status']))
                <a href="{{ route('customer.shipments.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    <i class="fas fa-times mr-2"></i>
                    Wyczyść filtry
                </a>
                @else
                <a href="{{ route('customer.shipments.create') }}" 
                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    <i class="fas fa-plus mr-2"></i>
                    Utwórz przesyłkę
                </a>
                @endif
            </div>
        </div>
        @endif
    </div>

    <!-- Bulk Actions (for future implementation) -->
    <div x-show="selectedShipments.length > 0" 
         x-transition
         class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 px-4 py-3 sm:px-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <span class="text-sm text-gray-700" x-text="`Wybrano ${selectedShipments.length} przesyłek`"></span>
            </div>
            <div class="flex items-center space-x-2">
                <button @click="exportSelected()" 
                        class="bg-white border border-gray-300 rounded-md px-3 py-1 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    <i class="fas fa-download mr-1"></i>
                    Eksportuj
                </button>
                <button @click="selectedShipments = []" 
                        class="bg-white border border-gray-300 rounded-md px-3 py-1 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    <i class="fas fa-times mr-1"></i>
                    Anuluj
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('shipmentsIndex', () => ({
        selectedShipments: [],
        
        toggleSelection(shipmentId) {
            if (this.selectedShipments.includes(shipmentId)) {
                this.selectedShipments = this.selectedShipments.filter(id => id !== shipmentId);
            } else {
                this.selectedShipments.push(shipmentId);
            }
        },
        
        selectAll() {
            // Get all shipment IDs from current page
            const shipmentIds = Array.from(document.querySelectorAll('[data-shipment-id]'))
                .map(el => parseInt(el.dataset.shipmentId));
            
            if (this.selectedShipments.length === shipmentIds.length) {
                this.selectedShipments = [];
            } else {
                this.selectedShipments = [...shipmentIds];
            }
        },
        
        async exportSelected() {
            if (this.selectedShipments.length === 0) return;
            
            try {
                const response = await fetch('{{ route("customer.shipments.export") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        shipment_ids: this.selectedShipments
                    })
                });
                
                if (response.ok) {
                    const blob = await response.blob();
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = `przesylki-${new Date().toISOString().slice(0, 10)}.csv`;
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

// Auto-submit search form on status change
document.getElementById('status')?.addEventListener('change', function() {
    this.form.submit();
});
</script>
@endpush
@endsection