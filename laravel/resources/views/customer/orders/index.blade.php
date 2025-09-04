@extends('layouts.customer')

@section('title', 'Moje zamówienia')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Header -->
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-black-coal">Moje zamówienia</h2>
                <p class="mt-1 text-sm font-body text-gray-500">
                    Przeglądaj wszystkie swoje zamówienia i ich status
                </p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('customer.shipments.create') }}" 
                   class="bg-skywave hover:bg-skywave/90 text-white px-4 py-2 rounded-lg font-body font-medium transition inline-flex items-center">
                    <i class="fas fa-plus mr-2"></i>
                    Nowa przesyłka
                </a>
                <a href="{{ route('customer.shipments.cart') }}" 
                   class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-body font-medium transition inline-flex items-center">
                    <i class="fas fa-shopping-cart mr-2"></i>
                    Koszyk
                </a>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white shadow rounded-lg p-4">
        <form method="GET" class="flex items-center space-x-4">
            <div class="flex-1">
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-skywave focus:border-transparent">
                    <option value="">Wszystkie statusy</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Oczekujące</option>
                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Opłacone</option>
                    <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>W realizacji</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Zakończone</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Anulowane</option>
                </select>
            </div>
            <button type="submit" class="bg-skywave hover:bg-skywave/90 text-white px-4 py-2 rounded-lg font-body font-medium transition">
                <i class="fas fa-search mr-2"></i>
                Filtruj
            </button>
            @if(request()->hasAny(['status']))
                <a href="{{ route('customer.orders.index') }}" 
                   class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-body font-medium transition">
                    <i class="fas fa-times mr-2"></i>
                    Wyczyść
                </a>
            @endif
        </form>
    </div>

    <!-- Orders List -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        @if($orders->count() > 0)
            <div class="divide-y divide-gray-200">
                @foreach($orders as $order)
                    <div class="p-6 hover:bg-gray-50 transition">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <!-- Order Icon -->
                                <div class="w-12 h-12 bg-skywave/10 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-receipt text-skywave text-xl"></i>
                                </div>
                                
                                <div>
                                    <h3 class="text-lg font-heading font-medium text-black-coal">
                                        {{ $order->order_number }}
                                    </h3>
                                    <p class="text-sm text-gray-500">
                                        {{ $order->created_at->format('d.m.Y H:i') }} • 
                                        {{ $order->shipments->count() }} przesyłek
                                    </p>
                                    @if($order->notes)
                                        <p class="text-sm text-gray-600 mt-1">
                                            <i class="fas fa-sticky-note mr-1"></i>
                                            {{ Str::limit($order->notes, 50) }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="flex items-center space-x-4">
                                <!-- Status Badge -->
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $order->status_color_class }}">
                                    {{ $order->status_label }}
                                </span>
                                
                                <!-- Amount -->
                                <div class="text-right">
                                    <p class="text-lg font-bold text-black-coal">{{ number_format($order->total_amount, 2) }} PLN</p>
                                    <p class="text-sm text-gray-500">łącznie</p>
                                </div>
                                
                                <!-- Actions -->
                                <div class="flex space-x-2">
                                    <a href="{{ route('customer.orders.show', $order) }}" 
                                       class="bg-skywave hover:bg-skywave/90 text-white px-3 py-1 rounded-md text-sm font-medium transition">
                                        Szczegóły
                                    </a>
                                    
                                    @if($order->canBePaid())
                                        <a href="{{ route('customer.orders.pay', $order) }}" 
                                           class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-md text-sm font-medium transition">
                                            Zapłać
                                        </a>
                                    @endif
                                    
                                    @if($order->canBeCancelled())
                                        <form method="POST" action="{{ route('customer.orders.cancel', $order) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" 
                                                    onclick="return confirm('Czy na pewno chcesz anulować to zamówienie?')"
                                                    class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-md text-sm font-medium transition">
                                                Anuluj
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $orders->withQueryString()->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <div class="mx-auto h-12 w-12 text-gray-400">
                    <i class="fas fa-receipt text-4xl"></i>
                </div>
                <h3 class="mt-2 text-sm font-heading font-medium text-gray-900">Brak zamówień</h3>
                <p class="mt-1 text-sm font-body text-gray-500">
                    Nie masz jeszcze żadnych zamówień. Utwórz pierwszą przesyłkę!
                </p>
                <div class="mt-6">
                    <a href="{{ route('customer.shipments.create') }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-skywave hover:bg-skywave/90">
                        <i class="fas fa-plus mr-2"></i>
                        Utwórz pierwszą przesyłkę
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection