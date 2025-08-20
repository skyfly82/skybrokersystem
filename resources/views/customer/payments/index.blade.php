@extends('layouts.customer')

@section('title', 'Płatności')

@section('content')
<div class="space-y-6" x-data="paymentsIndex">
    <!-- Header -->
    <div class="bg-white shadow rounded-lg p-6">
        <div class="md:flex md:items-center md:justify-between">
            <div class="flex-1 min-w-0">
                <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                    Płatności
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Historia płatności i zarządzanie saldem konta
                </p>
            </div>
            <div class="mt-4 flex space-x-3 md:mt-0 md:ml-4">
                <a href="{{ route('customer.payments.topup') }}" 
                   class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium inline-flex items-center">
                    <i class="fas fa-plus mr-2"></i>
                    Doładuj konto
                </a>
            </div>
        </div>
    </div>

    <!-- Balance Card -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 shadow rounded-lg p-6 text-white">
        <div class="md:flex md:items-center md:justify-between">
            <div class="flex-1">
                <h3 class="text-lg font-medium text-blue-100">Aktualne saldo</h3>
                <p class="text-3xl font-bold">{{ number_format(auth()->user()->customer->current_balance, 2) }} PLN</p>
                <p class="mt-1 text-sm text-blue-200">
                    Limit kredytowy: {{ number_format(auth()->user()->customer->credit_limit, 2) }} PLN
                </p>
            </div>
            <div class="mt-4 md:mt-0">
                <div class="bg-blue-500 rounded-lg p-4">
                    <div class="text-sm text-blue-200">Dostępne środki</div>
                    <div class="text-xl font-semibold">
                        {{ number_format(auth()->user()->customer->current_balance + auth()->user()->customer->credit_limit, 2) }} PLN
                    </div>
                </div>
            </div>
        </div>
        
        @if(auth()->user()->customer->current_balance < 100)
        <div class="mt-4 bg-yellow-500 bg-opacity-20 border border-yellow-300 rounded-lg p-3">
            <div class="flex items-center">
                <i class="fas fa-exclamation-triangle text-yellow-200 mr-2"></i>
                <span class="text-sm">
                    Niskie saldo konta. Rozważ doładowanie aby uniknąć opóźnień w realizacji przesyłek.
                </span>
            </div>
        </div>
        @endif
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-check text-white text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Płatności zakończone</dt>
                            <dd class="text-lg font-medium text-gray-900">
                                {{ auth()->user()->customer->payments()->where('status', 'completed')->count() }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-clock text-white text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Płatności oczekujące</dt>
                            <dd class="text-lg font-medium text-gray-900">
                                {{ auth()->user()->customer->payments()->where('status', 'pending')->count() }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-calendar text-white text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">W tym miesiącu</dt>
                            <dd class="text-lg font-medium text-gray-900">
                                {{ number_format(auth()->user()->customer->payments()->where('status', 'completed')->whereMonth('created_at', now()->month)->sum('amount'), 2) }} PLN
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-chart-line text-white text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Łącznie wydano</dt>
                            <dd class="text-lg font-medium text-gray-900">
                                {{ number_format(auth()->user()->customer->payments()->where('status', 'completed')->sum('amount'), 2) }} PLN
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="bg-white shadow rounded-lg p-6">
        <form method="GET" action="{{ route('customer.payments.index') }}" class="space-y-4 md:space-y-0 md:flex md:items-center md:space-x-4">
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
                           placeholder="Szukaj po UUID, opisie..."
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
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Oczekujące</option>
                    <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Przetwarzane</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Zakończone</option>
                    <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Nieudane</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Anulowane</option>
                </select>
            </div>

            <!-- Type Filter -->
            <div class="min-w-0 flex-1 md:max-w-xs">
                <label for="type" class="sr-only">Typ</label>
                <select name="type" 
                        id="type"
                        class="block w-full border border-gray-300 rounded-md py-2 pl-3 pr-10 text-base focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Wszystkie typy</option>
                    <option value="topup" {{ request('type') === 'topup' ? 'selected' : '' }}>Doładowanie</option>
                    <option value="shipment" {{ request('type') === 'shipment' ? 'selected' : '' }}>Przesyłka</option>
                    <option value="refund" {{ request('type') === 'refund' ? 'selected' : '' }}>Zwrot</option>
                </select>
            </div>

            <!-- Filter Button -->
            <div class="flex space-x-2">
                <button type="submit" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                    <i class="fas fa-filter mr-2"></i>
                    Filtruj
                </button>
                @if(request()->anyFilled(['search', 'status', 'type']))
                <a href="{{ route('customer.payments.index') }}" 
                   class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-md text-sm font-medium">
                    <i class="fas fa-times mr-2"></i>
                    Wyczyść
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Results Count -->
    @if($payments->total() > 0)
    <div class="text-sm text-gray-500">
        Znaleziono {{ $payments->total() }} płatności
        @if(request()->anyFilled(['search', 'status', 'type']))
            (filtrowane)
        @endif
    </div>
    @endif

    <!-- Payments List -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        @if($payments->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Płatność
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Typ
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Kwota
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Metoda
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Data
                        </th>
                        <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only">Akcje</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($payments as $payment)
                    <tr class="hover:bg-gray-50">
                        <!-- Payment Info -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex flex-col">
                                <div class="text-sm font-medium text-gray-900">
                                    #{{ substr($payment->uuid, 0, 8) }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $payment->description }}
                                </div>
                            </div>
                        </td>

                        <!-- Type -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @switch($payment->type)
                                    @case('topup')
                                        bg-green-100 text-green-800
                                        @break
                                    @case('shipment')
                                        bg-blue-100 text-blue-800
                                        @break
                                    @case('refund')
                                        bg-purple-100 text-purple-800
                                        @break
                                    @default
                                        bg-gray-100 text-gray-800
                                @endswitch">
                                <i class="mr-1 fas 
                                    @switch($payment->type)
                                        @case('topup')
                                            fa-plus
                                            @break
                                        @case('shipment')
                                            fa-box
                                            @break
                                        @case('refund')
                                            fa-undo
                                            @break
                                        @default
                                            fa-credit-card
                                    @endswitch"></i>
                                @switch($payment->type)
                                    @case('topup')
                                        Doładowanie
                                        @break
                                    @case('shipment')
                                        Przesyłka
                                        @break
                                    @case('refund')
                                        Zwrot
                                        @break
                                    @default
                                        {{ ucfirst($payment->type) }}
                                @endswitch
                            </span>
                        </td>

                        <!-- Amount -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium 
                                @if($payment->type === 'refund')
                                    text-red-600
                                @elseif($payment->type === 'topup')
                                    text-green-600
                                @else
                                    text-gray-900
                                @endif">
                                @if($payment->type === 'refund')-@endif{{ number_format($payment->amount, 2) }} {{ $payment->currency }}
                            </div>
                        </td>

                        <!-- Method -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <div class="flex items-center">
                                <i class="mr-2 fas 
                                    @switch($payment->method)
                                        @case('card')
                                            fa-credit-card
                                            @break
                                        @case('bank_transfer')
                                            fa-university
                                            @break
                                        @case('blik')
                                            fa-mobile-alt
                                            @break
                                        @case('paypal')
                                            fa-paypal
                                            @break
                                        @case('simulation')
                                            fa-flask
                                            @break
                                        @default
                                            fa-money-bill
                                    @endswitch"></i>
                                @switch($payment->method)
                                    @case('card')
                                        Karta płatnicza
                                        @break
                                    @case('bank_transfer')
                                        Przelew bankowy
                                        @break
                                    @case('blik')
                                        BLIK
                                        @break
                                    @case('paypal')
                                        PayPal
                                        @break
                                    @case('simulation')
                                        Symulacja
                                        @break
                                    @default
                                        {{ ucfirst($payment->method) }}
                                @endswitch
                            </div>
                            @if($payment->provider)
                            <div class="text-xs text-gray-400">
                                {{ ucfirst($payment->provider) }}
                            </div>
                            @endif
                        </td>

                        <!-- Status -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @switch($payment->status)
                                    @case('completed')
                                        bg-green-100 text-green-800
                                        @break
                                    @case('pending')
                                        bg-yellow-100 text-yellow-800
                                        @break
                                    @case('processing')
                                        bg-blue-100 text-blue-800
                                        @break
                                    @case('failed')
                                        bg-red-100 text-red-800
                                        @break
                                    @case('cancelled')
                                        bg-gray-100 text-gray-800
                                        @break
                                    @default
                                        bg-gray-100 text-gray-800
                                @endswitch">
                                <div class="w-2 h-2 rounded-full mr-1.5
                                    @switch($payment->status)
                                        @case('completed')
                                            bg-green-400
                                            @break
                                        @case('pending')
                                            bg-yellow-400
                                            @break
                                        @case('processing')
                                            bg-blue-400
                                            @break
                                        @case('failed')
                                            bg-red-400
                                            @break
                                        @case('cancelled')
                                            bg-gray-400
                                            @break
                                        @default
                                            bg-gray-400
                                    @endswitch"></div>
                                {{ $payment->status_label }}
                            </span>
                        </td>

                        <!-- Date -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <div>{{ $payment->created_at->format('d.m.Y') }}</div>
                            <div class="text-xs">{{ $payment->created_at->format('H:i') }}</div>
                            @if($payment->paid_at)
                            <div class="text-xs text-green-600">
                                Opłacono: {{ $payment->paid_at->format('d.m H:i') }}
                            </div>
                            @endif
                        </td>

                        <!-- Actions -->
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('customer.payments.show', $payment) }}" 
                                   class="text-blue-600 hover:text-blue-900 p-1"
                                   title="Zobacz szczegóły">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                @if($payment->status === 'pending' && $payment->provider_data && isset($payment->provider_data['payment_url']))
                                <a href="{{ $payment->provider_data['payment_url'] }}" 
                                   class="text-green-600 hover:text-green-900 p-1"
                                   title="Opłać teraz"
                                   target="_blank">
                                    <i class="fas fa-credit-card"></i>
                                </a>
                                @endif
                                
                                @if($payment->status === 'completed' && $payment->type === 'topup')
                                <button @click="downloadReceipt('{{ $payment->id }}')" 
                                        class="text-gray-600 hover:text-gray-900 p-1"
                                        title="Pobierz potwierdzenie">
                                    <i class="fas fa-download"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $payments->appends(request()->query())->links() }}
        </div>

        @else
        <!-- Empty State -->
        <div class="text-center py-12">
            <div class="mx-auto h-12 w-12 text-gray-400">
                <i class="fas fa-credit-card text-4xl"></i>
            </div>
            <h3 class="mt-2 text-sm font-medium text-gray-900">
                @if(request()->anyFilled(['search', 'status', 'type']))
                    Brak płatności spełniających kryteria
                @else
                    Brak płatności
                @endif
            </h3>
            <p class="mt-1 text-sm text-gray-500">
                @if(request()->anyFilled(['search', 'status', 'type']))
                    Spróbuj zmienić filtry wyszukiwania.
                @else
                    Rozpocznij od doładowania konta.
                @endif
            </p>
            <div class="mt-6">
                @if(request()->anyFilled(['search', 'status', 'type']))
                <a href="{{ route('customer.payments.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    <i class="fas fa-times mr-2"></i>
                    Wyczyść filtry
                </a>
                @else
                <a href="{{ route('customer.payments.topup') }}" 
                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                    <i class="fas fa-plus mr-2"></i>
                    Doładuj konto
                </a>
                @endif
            </div>
        </div>
        @endif
    </div>

    <!-- Payment Methods Info -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-400"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">
                    Dostępne metody płatności
                </h3>
                <div class="mt-2 text-sm text-blue-700">
                    <p>Akceptujemy płatności kartą, przelewem bankowym, BLIK oraz PayPal. Wszystkie transakcje są zabezpieczone SSL.</p>
                </div>
                <div class="mt-3 flex space-x-3">
                    <i class="fab fa-cc-visa text-2xl text-blue-600"></i>
                    <i class="fab fa-cc-mastercard text-2xl text-blue-600"></i>
                    <i class="fas fa-university text-2xl text-blue-600"></i>
                    <i class="fab fa-paypal text-2xl text-blue-600"></i>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('paymentsIndex', () => ({
        selectedPayments: [],
        
        async downloadReceipt(paymentId) {
            try {
                const response = await fetch(`/customer/payments/${paymentId}/receipt`);
                if (response.ok) {
                    const blob = await response.blob();
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = `potwierdzenie-platnosci-${paymentId}.pdf`;
                    a.click();
                    window.URL.revokeObjectURL(url);
                } else {
                    alert('Błąd podczas pobierania potwierdzenia');
                }
            } catch (error) {
                console.error('Download error:', error);
                alert('Błąd podczas pobierania potwierdzenia');
            }
        },
        
        async exportPayments() {
            if (this.selectedPayments.length === 0) return;
            
            try {
                const response = await fetch('{{ route("customer.payments.export") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        payment_ids: this.selectedPayments
                    })
                });
                
                if (response.ok) {
                    const blob = await response.blob();
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = `platnosci-${new Date().toISOString().slice(0, 10)}.csv`;
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

document.getElementById('type')?.addEventListener('change', function() {
    this.form.submit();
});
</script>
@endpush
@endsection