@extends('layouts.customer')

@section('title', 'Zamówienie ' . $order->order_number)

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Header -->
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('customer.orders.index') }}" 
                       class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <h2 class="text-2xl font-heading font-bold text-black-coal">
                        Zamówienie {{ $order->order_number }}
                    </h2>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $order->status_color_class }}">
                        {{ $order->status_label }}
                    </span>
                </div>
                <p class="mt-1 text-sm font-body text-gray-500">
                    Utworzone {{ $order->created_at->format('d.m.Y H:i') }} przez {{ $order->customerUser->name }}
                </p>
            </div>
            <div class="flex space-x-3">
                @if($order->canBePaid())
                    <a href="{{ route('customer.orders.pay', $order) }}" 
                       class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-body font-medium transition inline-flex items-center">
                        <i class="fas fa-credit-card mr-2"></i>
                        Zapłać teraz
                    </a>
                @endif
                
                @if($order->canBeCancelled())
                    <form method="POST" action="{{ route('customer.orders.cancel', $order) }}" class="inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" 
                                onclick="return confirm('Czy na pewno chcesz anulować to zamówienie? Przesyłki wrócą do koszyka.')"
                                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-body font-medium transition inline-flex items-center">
                            <i class="fas fa-times mr-2"></i>
                            Anuluj zamówienie
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <!-- Order Summary -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Order Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Shipments -->
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-heading font-medium text-black-coal">
                        Przesyłki w zamówieniu ({{ $order->shipments->count() }})
                    </h3>
                </div>
                
                <div class="divide-y divide-gray-200">
                    @foreach($order->shipments as $shipment)
                        <div class="p-6">
                            <div class="flex items-start space-x-4">
                                <!-- Courier Logo -->
                                <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-truck text-gray-400"></i>
                                </div>
                                
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            <div>
                                                <h4 class="text-sm font-heading font-medium text-black-coal">
                                                    {{ $shipment->courierService->name ?? 'InPost' }}
                                                </h4>
                                                <p class="text-sm text-gray-500">{{ $shipment->service_type }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-3">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $shipment->status_color_class }}">
                                                {{ $shipment->status_label }}
                                            </span>
                                            <div class="text-right">
                                                <p class="text-sm font-bold text-black-coal">{{ number_format($shipment->total_price, 2) }} PLN</p>
                                                <p class="text-xs text-gray-500">brutto</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Shipment Details -->
                                    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                        <div>
                                            <p class="font-medium text-gray-700">Nadawca:</p>
                                            <p class="text-gray-600">{{ $shipment->sender_data['name'] ?? 'Brak danych' }}</p>
                                            <p class="text-gray-500">{{ $shipment->sender_data['city'] ?? '' }}</p>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-700">Odbiorca:</p>
                                            <p class="text-gray-600">{{ $shipment->recipient_data['name'] ?? 'Brak danych' }}</p>
                                            <p class="text-gray-500">{{ $shipment->recipient_data['city'] ?? '' }}</p>
                                        </div>
                                    </div>

                                    @if($shipment->tracking_number)
                                        <div class="mt-3 flex items-center text-sm">
                                            <i class="fas fa-barcode text-gray-400 mr-2"></i>
                                            <span class="text-gray-600">Numer śledzenia:</span>
                                            <code class="ml-2 text-skywave">{{ $shipment->tracking_number }}</code>
                                        </div>
                                    @endif

                                    <!-- Actions -->
                                    <div class="mt-4 flex items-center space-x-3">
                                        <a href="{{ route('customer.shipments.show', $shipment) }}" 
                                           class="text-sm text-skywave hover:text-skywave/80 font-medium">
                                            <i class="fas fa-eye mr-1"></i>
                                            Zobacz szczegóły
                                        </a>
                                        @if($shipment->tracking_number)
                                            <a href="{{ route('customer.shipments.track', $shipment) }}" 
                                               class="text-sm text-gray-600 hover:text-gray-800 font-medium">
                                                <i class="fas fa-route mr-1"></i>
                                                Śledź przesyłkę
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Order Notes -->
            @if($order->notes)
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-heading font-medium text-black-coal mb-3">Uwagi do zamówienia</h3>
                    <p class="text-gray-700">{{ $order->notes }}</p>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Payment Summary -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-heading font-medium text-black-coal mb-4">Podsumowanie płatności</h3>
                
                <div class="space-y-3">
                    @foreach($order->shipments as $shipment)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Przesyłka {{ $loop->iteration }}</span>
                            <span class="font-medium">{{ number_format($shipment->total_price, 2) }} PLN</span>
                        </div>
                    @endforeach
                    
                    <div class="border-t border-gray-200 pt-3">
                        <div class="flex justify-between">
                            <span class="font-medium text-black-coal">Razem do zapłaty:</span>
                            <span class="text-xl font-bold text-black-coal">{{ number_format($order->total_amount, 2) }} PLN</span>
                        </div>
                    </div>
                </div>

                @if($order->canBePaid())
                    <div class="mt-6">
                        <a href="{{ route('customer.orders.pay', $order) }}" 
                           class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-body font-medium transition inline-flex items-center justify-center">
                            <i class="fas fa-credit-card mr-2"></i>
                            Zapłać {{ number_format($order->total_amount, 2) }} PLN
                        </a>
                    </div>
                @endif
            </div>

            <!-- Payments History -->
            @if($order->payments->count() > 0)
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-heading font-medium text-black-coal mb-4">Historia płatności</h3>
                    
                    <div class="space-y-3">
                        @foreach($order->payments as $payment)
                            <div class="border border-gray-200 rounded-lg p-3">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="text-sm font-medium text-black-coal">{{ number_format($payment->amount, 2) }} PLN</p>
                                        <p class="text-xs text-gray-500">{{ $payment->created_at->format('d.m.Y H:i') }}</p>
                                        <p class="text-xs text-gray-600">{{ $payment->method_label }}</p>
                                    </div>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $payment->status_color_class }}">
                                        {{ $payment->status_label }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Order Timeline -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-heading font-medium text-black-coal mb-4">Historia zamówienia</h3>
                
                <div class="space-y-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                        <div>
                            <p class="text-sm font-medium text-black-coal">Zamówienie utworzone</p>
                            <p class="text-xs text-gray-500">{{ $order->created_at->format('d.m.Y H:i') }}</p>
                        </div>
                    </div>
                    
                    @if($order->paid_at)
                        <div class="flex items-center space-x-3">
                            <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                            <div>
                                <p class="text-sm font-medium text-black-coal">Zamówienie opłacone</p>
                                <p class="text-xs text-gray-500">{{ $order->paid_at->format('d.m.Y H:i') }}</p>
                            </div>
                        </div>
                    @endif
                    
                    @if($order->completed_at)
                        <div class="flex items-center space-x-3">
                            <div class="w-2 h-2 bg-purple-500 rounded-full"></div>
                            <div>
                                <p class="text-sm font-medium text-black-coal">Zamówienie zakończone</p>
                                <p class="text-xs text-gray-500">{{ $order->completed_at->format('d.m.Y H:i') }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection