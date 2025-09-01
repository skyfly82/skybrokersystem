@extends('layouts.customer')

@section('title', 'Płatność za przesyłki')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex items-center space-x-2 text-sm text-gray-500 mb-2">
            <a href="{{ route('customer.dashboard') }}" class="hover:text-gray-700">Dashboard</a>
            <i class="fas fa-chevron-right text-xs"></i>
            <a href="{{ route('customer.shipments.index') }}" class="hover:text-gray-700">Przesyłki</a>
            <i class="fas fa-chevron-right text-xs"></i>
            <span class="text-gray-900">Płatność</span>
        </div>
        <h1 class="text-3xl font-bold text-gray-900">Płatność za przesyłki</h1>
        <p class="mt-1 text-gray-600">Dokończ proces nadawania poprzez uiszczenie płatności.</p>
    </div>

    <div class="grid lg:grid-cols-3 gap-8">
        <!-- Left Side - Shipment Details -->
        <div class="lg:col-span-2 space-y-6">
            @isset($shipment)
            <!-- Single Shipment -->
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-box text-blue-600 mr-3"></i>
                    Szczegóły przesyłki
                </h2>
                
                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Sender -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h3 class="font-medium text-gray-900 mb-2">
                                <i class="fas fa-paper-plane text-green-600 mr-2"></i>
                                Nadawca
                            </h3>
                            <div class="text-sm space-y-1">
                                <div>{{ $shipment->sender_data['name'] }}</div>
                                @if(isset($shipment->sender_data['company']) && $shipment->sender_data['company'])
                                    <div class="text-gray-600">{{ $shipment->sender_data['company'] }}</div>
                                @endif
                                <div>{{ $shipment->sender_data['address'] }}</div>
                                <div>{{ $shipment->sender_data['postal_code'] }} {{ $shipment->sender_data['city'] }}</div>
                            </div>
                        </div>

                        <!-- Recipient -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h3 class="font-medium text-gray-900 mb-2">
                                <i class="fas fa-map-marker-alt text-blue-600 mr-2"></i>
                                Odbiorca
                            </h3>
                            <div class="text-sm space-y-1">
                                <div>{{ $shipment->recipient_data['name'] }}</div>
                                @if(isset($shipment->recipient_data['company']) && $shipment->recipient_data['company'])
                                    <div class="text-gray-600">{{ $shipment->recipient_data['company'] }}</div>
                                @endif
                                <div>{{ $shipment->recipient_data['address'] }}</div>
                                <div>{{ $shipment->recipient_data['postal_code'] }} {{ $shipment->recipient_data['city'] }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Services -->
                    @if($shipment->cod_amount || $shipment->insurance_amount)
                    <div class="border-t pt-4">
                        <h4 class="font-medium text-gray-900 mb-2">Usługi dodatkowe:</h4>
                        <div class="flex flex-wrap gap-2">
                            @if($shipment->cod_amount)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-money-bill-wave mr-1"></i>
                                    COD: {{ number_format($shipment->cod_amount, 2) }} PLN
                                </span>
                            @endif
                            @if($shipment->insurance_amount)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <i class="fas fa-shield-alt mr-1"></i>
                                    Ubezpieczenie: {{ number_format($shipment->insurance_amount, 2) }} PLN
                                </span>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endisset

            @isset($order)
            <!-- Order Payment -->
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-receipt text-blue-600 mr-3"></i>
                    Zamówienie {{ $order->order_number }}
                </h2>
                
                <div class="space-y-4">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex justify-between items-center mb-3">
                            <h3 class="font-medium text-gray-900">Przesyłki w zamówieniu:</h3>
                            <span class="text-sm text-gray-600">{{ $order->shipments->count() }} przesyłek</span>
                        </div>
                        
                        @foreach($order->shipments as $shipment)
                            <div class="border-b border-gray-200 pb-3 mb-3 last:border-b-0 last:pb-0 last:mb-0">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-2 mb-1">
                                            <span class="font-medium text-gray-900">{{ $shipment->courierService->name ?? 'InPost' }}</span>
                                            <span class="text-xs px-2 py-1 bg-gray-100 text-gray-600 rounded">{{ $shipment->service_type }}</span>
                                        </div>
                                        <div class="text-sm text-gray-600">
                                            <div>{{ $shipment->sender_data['name'] ?? 'Brak danych' }} → {{ $shipment->recipient_data['name'] ?? 'Brak danych' }}</div>
                                            <div class="mt-1">
                                                {{ $shipment->sender_data['city'] ?? '' }} → {{ $shipment->recipient_data['city'] ?? '' }}
                                            </div>
                                        </div>
                                        
                                        <!-- Package details -->
                                        @if(isset($shipment->package_data))
                                        <div class="text-xs text-gray-500 mt-1">
                                            Waga: {{ $shipment->package_data['weight'] ?? 'N/A' }}kg, 
                                            Wymiary: {{ $shipment->package_data['length'] ?? 'N/A' }}×{{ $shipment->package_data['width'] ?? 'N/A' }}×{{ $shipment->package_data['height'] ?? 'N/A' }}cm
                                        </div>
                                        @endif
                                        
                                        <!-- Additional services -->
                                        @if($shipment->additional_services && count($shipment->additional_services) > 0)
                                        <div class="text-xs text-blue-600 mt-1">
                                            <i class="fas fa-plus-circle mr-1"></i>
                                            @foreach($shipment->additional_services as $service => $value)
                                                @if($value)
                                                    <span class="inline-block mr-2">{{ ucfirst(str_replace('_', ' ', $service)) }}</span>
                                                @endif
                                            @endforeach
                                        </div>
                                        @endif
                                    </div>
                                    <div class="text-right">
                                        <div class="font-semibold text-gray-900">{{ number_format($shipment->total_price, 2) }} PLN</div>
                                        <div class="text-xs text-gray-500">brutto</div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    @if($order->notes)
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                        <div class="flex items-start">
                            <i class="fas fa-sticky-note text-yellow-600 mt-0.5 mr-2"></i>
                            <div>
                                <div class="font-medium text-yellow-800 text-sm">Uwagi do zamówienia:</div>
                                <div class="text-yellow-700 text-sm mt-1">{{ $order->notes }}</div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endisset

            @isset($shipments)
            <!-- Multiple Shipments -->
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-boxes text-blue-600 mr-3"></i>
                    Przesyłki do płatności ({{ count($shipments) }})
                </h2>

                <div class="space-y-4">
                    @foreach($shipments as $shipment)
                    <div class="border rounded-lg p-4 bg-gray-50">
                        <div class="flex items-center justify-between mb-2">
                            <div class="font-medium">{{ $shipment->tracking_number ?? 'Przesyłka #' . $shipment->id }}</div>
                            <div class="text-lg font-bold text-blue-600">{{ number_format($shipment->total_price, 2) }} PLN</div>
                        </div>
                        <div class="text-sm text-gray-600">
                            {{ $shipment->sender_data['city'] }} → {{ $shipment->recipient_data['city'] }}
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endisset
        </div>

        <!-- Right Side - Payment Summary -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border p-6 sticky top-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Podsumowanie płatności</h2>

                <div class="space-y-3 mb-6">
                    @isset($shipment)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Koszt przesyłki:</span>
                            <span class="font-medium">{{ number_format($shipment->total_price, 2) }} PLN</span>
                        </div>
                        @php $totalAmount = $shipment->total_price; @endphp
                    @endisset

                    @isset($order)
                        @php $totalAmount = $order->total_amount; @endphp
                        @foreach($order->shipments as $shipment)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">{{ $shipment->courierService->name ?? 'InPost' }} ({{ $shipment->service_type }}):</span>
                            <span>{{ number_format($shipment->total_price, 2) }} PLN</span>
                        </div>
                        @endforeach
                    @endisset

                    @isset($shipments)
                        @php $totalAmount = $shipments->sum('total_price'); @endphp
                        @foreach($shipments as $shipment)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Przesyłka #{{ $shipment->id }}:</span>
                            <span>{{ number_format($shipment->total_price, 2) }} PLN</span>
                        </div>
                        @endforeach
                    @endisset

                    <div class="border-t pt-3 flex justify-between font-semibold text-lg">
                        <span>Razem do zapłaty:</span>
                        <span class="text-blue-600">{{ number_format($totalAmount, 2) }} PLN</span>
                    </div>
                </div>

                <!-- Payment Methods -->
                <div class="space-y-4">
                    <h3 class="font-medium text-gray-900">Wybierz sposób płatności:</h3>

                    @if(auth()->user()->customer->current_balance >= $totalAmount)
                    <label class="block">
                        <div class="border-2 border-gray-200 rounded-lg p-4 cursor-pointer hover:border-blue-300 transition-colors">
                            <div class="flex items-center">
                                <input type="radio" name="payment_method" value="balance" class="mr-3" checked>
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-wallet text-green-600"></i>
                                    </div>
                                    <div>
                                        <div class="font-medium">Saldo konta</div>
                                        <div class="text-sm text-gray-500">Dostępne: {{ number_format(auth()->user()->customer->current_balance, 2) }} PLN</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </label>
                    @endif

                    @if(auth()->user()->customer->credit_limit)
                    <label class="block">
                        <div class="border-2 border-gray-200 rounded-lg p-4 cursor-pointer hover:border-blue-300 transition-colors">
                            <div class="flex items-center">
                                <input type="radio" name="payment_method" value="deferred" class="mr-3" {{ !auth()->user()->customer->balance >= $totalAmount ? 'checked' : '' }}>
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-clock text-orange-600"></i>
                                    </div>
                                    <div>
                                        <div class="font-medium">Płatność odroczona</div>
                                        <div class="text-sm text-gray-500">Limit: {{ number_format(auth()->user()->customer->credit_limit, 2) }} PLN</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </label>
                    @endif

                    <label class="block">
                        <div class="border-2 border-gray-200 rounded-lg p-4 cursor-pointer hover:border-blue-300 transition-colors">
                            <div class="flex items-center">
                                <input type="radio" name="payment_method" value="online" class="mr-3">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-credit-card text-blue-600"></i>
                                    </div>
                                    <div>
                                        <div class="font-medium">Szybka płatność online</div>
                                        <div class="text-sm text-gray-500">BLIK, karty, przelewy</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </label>
                </div>

                <!-- Payment Button -->
                <button type="button" 
                        onclick="processPayment()"
                        class="w-full mt-6 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-4 px-6 rounded-xl transition-colors flex items-center justify-center">
                    <i class="fas fa-credit-card mr-2"></i>
                    Zapłać {{ number_format($totalAmount, 2) }} PLN
                </button>

                <div class="mt-4 text-center">
                    <a href="{{ route('customer.shipments.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
                        ← Powrót do listy przesyłek
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function processPayment() {
    const selectedMethod = document.querySelector('input[name="payment_method"]:checked');
    
    if (!selectedMethod) {
        alert('Wybierz sposób płatności');
        return;
    }

    const method = selectedMethod.value;
    const totalAmount = {{ $totalAmount }};

    // Show loading state
    const button = event.target;
    const originalText = button.innerHTML;
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Przetwarzanie...';

    // Create form and submit
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("customer.payments.process") }}';
    
    // CSRF token
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = '{{ csrf_token() }}';
    form.appendChild(csrfInput);
    
    // Payment method
    const methodInput = document.createElement('input');
    methodInput.type = 'hidden';
    methodInput.name = 'payment_method';
    methodInput.value = method;
    form.appendChild(methodInput);
    
    // Order/Shipment data
    @isset($order)
        const orderIdInput = document.createElement('input');
        orderIdInput.type = 'hidden';
        orderIdInput.name = 'order_id';
        orderIdInput.value = '{{ $order->id }}';
        form.appendChild(orderIdInput);
    @endisset
    
    @isset($shipment)
        const shipmentIdInput = document.createElement('input');
        shipmentIdInput.type = 'hidden';
        shipmentIdInput.name = 'shipment_id';
        shipmentIdInput.value = '{{ $shipment->id }}';
        form.appendChild(shipmentIdInput);
    @endisset
    
    @isset($shipments)
        const shipmentIdsInput = document.createElement('input');
        shipmentIdsInput.type = 'hidden';
        shipmentIdsInput.name = 'shipment_ids';
        shipmentIdsInput.value = '{{ $shipments->pluck("id")->implode(",") }}';
        form.appendChild(shipmentIdsInput);
    @endisset
    
    document.body.appendChild(form);
    form.submit();
}

// Auto-select appropriate payment method based on balance
document.addEventListener('DOMContentLoaded', function() {
    const balanceMethod = document.querySelector('input[value="balance"]');
    const deferredMethod = document.querySelector('input[value="deferred"]');
    const onlineMethod = document.querySelector('input[value="online"]');
    
    @if(auth()->user()->customer->current_balance >= $totalAmount)
        if (balanceMethod) balanceMethod.checked = true;
    @elseif(auth()->user()->customer->credit_limit)
        if (deferredMethod) deferredMethod.checked = true;
    @else
        if (onlineMethod) onlineMethod.checked = true;
    @endif
});
</script>
@endsection