@extends('layouts.customer')

@section('title', 'Szczegóły przesyłki')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Breadcrumb -->
    <div class="mb-6">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-2 text-sm text-gray-500">
                <li><a href="{{ route('customer.dashboard') }}" class="hover:text-gray-700">Dashboard</a></li>
                <li><i class="fas fa-chevron-right text-xs"></i></li>
                <li><a href="{{ route('customer.shipments.index') }}" class="hover:text-gray-700">Przesyłki</a></li>
                <li><i class="fas fa-chevron-right text-xs"></i></li>
                <li class="text-gray-900">{{ $shipment->tracking_number ?? 'Przesyłka #'.$shipment->id }}</li>
            </ol>
        </nav>
    </div>

    <!-- Header with Actions -->
    <div class="bg-white rounded-lg shadow-sm border p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center space-x-3">
                    <h1 class="text-2xl font-bold text-gray-900">
                        {{ $shipment->tracking_number ?? 'Przesyłka #'.$shipment->id }}
                    </h1>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $shipment->status_color_class }}">
                        <span class="w-2 h-2 bg-current rounded-full mr-2"></span>
                        {{ $shipment->status_label }}
                    </span>
                </div>
                <div class="mt-2 flex items-center space-x-4 text-sm text-gray-600">
                    <span><i class="fas fa-calendar-alt mr-1"></i>Utworzono: {{ $shipment->created_at->format('d.m.Y H:i') }}</span>
                    @if($shipment->courierService)
                        <span><i class="fas fa-truck mr-1"></i>{{ $shipment->courierService->name }}</span>
                    @endif
                    <span><i class="fas fa-route mr-1"></i>{{ $shipment->sender_data['city'] }} → {{ $shipment->recipient_data['city'] }}</span>
                </div>
            </div>
            
            <div class="flex items-center space-x-3">
                @if($shipment->isEditable())
                    <a href="{{ route('customer.shipments.edit', $shipment) }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        <i class="fas fa-edit mr-2"></i>
                        Edytuj
                    </a>
                @endif
                
                @if($shipment->tracking_number && in_array($shipment->status, ['sent', 'in_transit', 'delivered']))
                    <a href="{{ route('customer.shipments.track', $shipment) }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700">
                        <i class="fas fa-map-marker-alt mr-2"></i>
                        Śledź
                    </a>
                @endif
                
                @if($shipment->canBeCancelled())
                    <form method="POST" action="{{ route('customer.shipments.cancel', $shipment) }}" class="inline"
                          onsubmit="return confirm('Czy na pewno chcesz anulować tę przesyłkę?')">
                        @csrf
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 border border-red-300 rounded-md shadow-sm text-sm font-medium text-red-700 bg-white hover:bg-red-50">
                            <i class="fas fa-times mr-2"></i>
                            Anuluj
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        <!-- Left Column - Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Addresses -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Adresy</h2>
                <div class="grid md:grid-cols-2 gap-6">
                    <!-- Sender -->
                    <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                        <h3 class="font-medium text-green-900 mb-3 flex items-center">
                            <i class="fas fa-paper-plane text-green-600 mr-2"></i>
                            Nadawca
                        </h3>
                        <div class="text-sm space-y-1 text-green-800">
                            <div class="font-semibold">{{ $shipment->sender_data['name'] }}</div>
                            @if(isset($shipment->sender_data['company']) && $shipment->sender_data['company'])
                                <div class="text-green-600">{{ $shipment->sender_data['company'] }}</div>
                            @endif
                            @if(isset($shipment->sender_data['address']) && $shipment->sender_data['address'])
                                <div>{{ $shipment->sender_data['address'] }}</div>
                            @endif
                            <div>{{ isset($shipment->sender_data['postal_code']) ? $shipment->sender_data['postal_code'] . ' ' : '' }}{{ $shipment->sender_data['city'] }}</div>
                            <div class="pt-2 space-y-1">
                                @if(isset($shipment->sender_data['phone']) && $shipment->sender_data['phone'])
                                    <div><i class="fas fa-phone mr-1"></i> {{ $shipment->sender_data['phone'] }}</div>
                                @endif
                                @if(isset($shipment->sender_data['email']) && $shipment->sender_data['email'])
                                    <div><i class="fas fa-envelope mr-1"></i> {{ $shipment->sender_data['email'] }}</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Recipient -->
                    <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                        <h3 class="font-medium text-blue-900 mb-3 flex items-center">
                            <i class="fas fa-map-marker-alt text-blue-600 mr-2"></i>
                            Odbiorca
                        </h3>
                        <div class="text-sm space-y-1 text-blue-800">
                            <div class="font-semibold">{{ $shipment->recipient_data['name'] }}</div>
                            @if(isset($shipment->recipient_data['company']) && $shipment->recipient_data['company'])
                                <div class="text-blue-600">{{ $shipment->recipient_data['company'] }}</div>
                            @endif
                            @if(isset($shipment->recipient_data['address']) && $shipment->recipient_data['address'])
                                <div>{{ $shipment->recipient_data['address'] }}</div>
                            @endif
                            <div>{{ isset($shipment->recipient_data['postal_code']) ? $shipment->recipient_data['postal_code'] . ' ' : '' }}{{ $shipment->recipient_data['city'] }}</div>
                            <div class="pt-2 space-y-1">
                                @if(isset($shipment->recipient_data['phone']) && $shipment->recipient_data['phone'])
                                    <div><i class="fas fa-phone mr-1"></i> {{ $shipment->recipient_data['phone'] }}</div>
                                @endif
                                @if(isset($shipment->recipient_data['email']) && $shipment->recipient_data['email'])
                                    <div><i class="fas fa-envelope mr-1"></i> {{ $shipment->recipient_data['email'] }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Package Details -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-box text-purple-600 mr-2"></i>
                    Szczegóły paczki
                </h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                    <div class="text-center bg-gray-50 rounded-lg p-3">
                        <div class="text-2xl font-bold text-purple-600">{{ $shipment->package_data['length'] ?? 'N/A' }}</div>
                        <div class="text-xs text-gray-500">Długość (cm)</div>
                    </div>
                    <div class="text-center bg-gray-50 rounded-lg p-3">
                        <div class="text-2xl font-bold text-purple-600">{{ $shipment->package_data['width'] ?? 'N/A' }}</div>
                        <div class="text-xs text-gray-500">Szerokość (cm)</div>
                    </div>
                    <div class="text-center bg-gray-50 rounded-lg p-3">
                        <div class="text-2xl font-bold text-purple-600">{{ $shipment->package_data['height'] ?? 'N/A' }}</div>
                        <div class="text-xs text-gray-500">Wysokość (cm)</div>
                    </div>
                    <div class="text-center bg-gray-50 rounded-lg p-3">
                        <div class="text-2xl font-bold text-purple-600">{{ $shipment->package_data['weight'] ?? 'N/A' }}</div>
                        <div class="text-xs text-gray-500">Waga (kg)</div>
                    </div>
                </div>
                
                @if(isset($shipment->package_data['description']) && $shipment->package_data['description'])
                    <div class="border-t pt-4">
                        <h4 class="font-medium text-gray-900 mb-2">Opis zawartości:</h4>
                        <p class="text-sm text-gray-600">{{ $shipment->package_data['description'] }}</p>
                    </div>
                @endif
            </div>

            <!-- Additional Services -->
            @if($shipment->cod_amount || $shipment->insurance_amount || $shipment->notes)
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-plus-circle text-orange-600 mr-2"></i>
                    Usługi dodatkowe
                </h2>
                
                <div class="space-y-3">
                    @if($shipment->cod_amount)
                        <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg border border-yellow-200">
                            <div class="flex items-center">
                                <i class="fas fa-money-bill-wave text-yellow-600 mr-3"></i>
                                <div>
                                    <div class="font-medium text-yellow-900">Pobranie (COD)</div>
                                    <div class="text-sm text-yellow-700">Odbierz płatność przy dostawie</div>
                                </div>
                            </div>
                            <div class="font-bold text-yellow-900">{{ number_format($shipment->cod_amount, 2) }} PLN</div>
                        </div>
                    @endif

                    @if($shipment->insurance_amount)
                        <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg border border-blue-200">
                            <div class="flex items-center">
                                <i class="fas fa-shield-alt text-blue-600 mr-3"></i>
                                <div>
                                    <div class="font-medium text-blue-900">Ubezpieczenie</div>
                                    <div class="text-sm text-blue-700">Dodatkowa ochrona przesyłki</div>
                                </div>
                            </div>
                            <div class="font-bold text-blue-900">{{ number_format($shipment->insurance_amount, 2) }} PLN</div>
                        </div>
                    @endif
                </div>

                @if($shipment->notes)
                    <div class="mt-4 pt-4 border-t">
                        <h4 class="font-medium text-gray-900 mb-2 flex items-center">
                            <i class="fas fa-sticky-note text-yellow-600 mr-2"></i>
                            Uwagi dodatkowe:
                        </h4>
                        <p class="text-sm text-gray-600 bg-gray-50 p-3 rounded-lg">{{ $shipment->notes }}</p>
                    </div>
                @endif
            </div>
            @endif

            <!-- Payments -->
            @if($shipment->payments && $shipment->payments->count() > 0)
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-credit-card text-green-600 mr-2"></i>
                    Płatności
                </h2>
                
                <div class="space-y-3">
                    @foreach($shipment->payments as $payment)
                        <div class="flex items-center justify-between p-3 border rounded-lg">
                            <div>
                                <div class="font-medium">{{ number_format($payment->amount, 2) }} PLN</div>
                                <div class="text-sm text-gray-600">{{ $payment->created_at->format('d.m.Y H:i') }}</div>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $payment->status_color_class }}">
                                {{ $payment->status_label }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Right Column - Status History & Summary -->
        <div class="space-y-6">
            <!-- Status History -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-history text-blue-600 mr-2"></i>
                    Historia statusów
                </h2>
                
                <div class="flow-root">
                    <ul class="-mb-8">
                        <!-- Current Status -->
                        <li>
                            <div class="relative pb-8">
                                <div class="absolute top-5 left-5 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></div>
                                <div class="relative flex items-start space-x-3">
                                    <div class="relative">
                                        <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                                            <i class="fas fa-circle text-white text-xs"></i>
                                        </div>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div>
                                            <div class="text-sm">
                                                <span class="font-medium text-gray-900">Status aktualny</span>
                                            </div>
                                            <p class="mt-0.5 text-sm text-gray-500">
                                                {{ $shipment->status_label }}
                                            </p>
                                        </div>
                                        <div class="mt-2 text-sm text-gray-700">
                                            <p>Zaktualizowano: {{ $shipment->updated_at->format('d.m.Y H:i:s') }}</p>
                                            <p class="text-xs text-gray-500">{{ $shipment->updated_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        
                        <!-- Creation -->
                        <li>
                            <div class="relative pb-8">
                                <div class="relative flex items-start space-x-3">
                                    <div class="relative">
                                        <div class="h-10 w-10 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white">
                                            <i class="fas fa-plus text-white text-xs"></i>
                                        </div>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div>
                                            <div class="text-sm">
                                                <span class="font-medium text-gray-900">Przesyłka utworzona</span>
                                            </div>
                                            <p class="mt-0.5 text-sm text-gray-500">
                                                Przez: {{ $shipment->customerUser->full_name ?? 'System' }}
                                            </p>
                                        </div>
                                        <div class="mt-2 text-sm text-gray-700">
                                            <p>{{ $shipment->created_at->format('d.m.Y H:i:s') }}</p>
                                            <p class="text-xs text-gray-500">{{ $shipment->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Cost Summary -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-calculator text-green-600 mr-2"></i>
                    Podsumowanie kosztów
                </h2>
                
                <div class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Koszt przesyłki:</span>
                        <span class="font-medium">{{ number_format($shipment->base_price ?? 0, 2) }} PLN</span>
                    </div>
                    
                    @if($shipment->cod_amount)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Pobranie COD:</span>
                            <span class="font-medium">{{ number_format($shipment->cod_fee ?? 0, 2) }} PLN</span>
                        </div>
                    @endif
                    
                    @if($shipment->insurance_amount)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Ubezpieczenie:</span>
                            <span class="font-medium">{{ number_format($shipment->insurance_fee ?? 0, 2) }} PLN</span>
                        </div>
                    @endif
                    
                    <div class="border-t pt-3 flex justify-between font-semibold">
                        <span>Łącznie:</span>
                        <span class="text-blue-600">{{ number_format($shipment->total_price ?? 0, 2) }} PLN</span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Szybkie akcje</h2>
                
                <div class="space-y-3">
                    @if($shipment->status === 'created' && !$shipment->payments()->where('status', 'completed')->exists())
                        <a href="{{ route('customer.payments.create', ['shipment_id' => $shipment->id]) }}" 
                           class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                            <i class="fas fa-credit-card mr-2"></i>
                            Opłać przesyłkę
                        </a>
                    @endif
                    
                    @if($shipment->tracking_number)
                        <button onclick="copyTrackingNumber()" 
                                class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            <i class="fas fa-copy mr-2"></i>
                            Kopiuj numer śledzenia
                        </button>
                    @endif
                    
                    @if(in_array($shipment->status, ['printed', 'sent']) && $shipment->service_type !== 'inpost_no_label')
                        <div class="space-y-2">
                            <!-- Default PDF A4 -->
                            <a href="{{ route('customer.shipments.label', $shipment) }}" 
                               class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                               target="_blank">
                                <i class="fas fa-print mr-2"></i>
                                Pobierz etykietę (PDF A4)
                            </a>
                            
                            <!-- Format Options -->
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" 
                                        type="button"
                                        class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                    <i class="fas fa-caret-down mr-2"></i>
                                    Inne formaty
                                </button>
                                
                                <div x-show="open" 
                                     @click.away="open = false"
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="transform opacity-0 scale-95"
                                     x-transition:enter-end="transform opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="transform opacity-100 scale-100"
                                     x-transition:leave-end="transform opacity-0 scale-95"
                                     class="absolute z-10 mt-1 w-full bg-white shadow-lg rounded-md border border-gray-200">
                                    <div class="py-1">
                                        <a href="{{ route('customer.shipments.label', $shipment) }}?format=pdf&size=A6" 
                                           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                           target="_blank">
                                            <i class="fas fa-file-pdf mr-2"></i>
                                            PDF A6 (kompaktowy)
                                        </a>
                                        <a href="{{ route('customer.shipments.label', $shipment) }}?format=zpl" 
                                           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                           target="_blank">
                                            <i class="fas fa-code mr-2"></i>
                                            ZPL (drukarki termiczne)
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(in_array($shipment->status, ['created', 'failed']) && !$shipment->payments()->where('status', 'completed')->exists())
                        <form method="POST" action="{{ route('customer.shipments.destroy', $shipment) }}" 
                              onsubmit="return confirm('Czy na pewno chcesz usunąć tę przesyłkę? Ta akcja jest nieodwracalna.')"
                              class="w-full">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="w-full inline-flex justify-center items-center px-4 py-2 border border-red-300 rounded-md shadow-sm text-sm font-medium text-red-700 bg-red-50 hover:bg-red-100">
                                <i class="fas fa-trash mr-2"></i>
                                Usuń przesyłkę
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyTrackingNumber() {
    const trackingNumber = "{{ $shipment->tracking_number }}";
    if (trackingNumber) {
        navigator.clipboard.writeText(trackingNumber).then(function() {
            // Show success message
            const button = event.target.closest('button');
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-check mr-2"></i>Skopiowano!';
            button.classList.remove('text-gray-700', 'hover:bg-gray-50');
            button.classList.add('text-green-700', 'bg-green-50');
            
            setTimeout(() => {
                button.innerHTML = originalText;
                button.classList.add('text-gray-700', 'hover:bg-gray-50');
                button.classList.remove('text-green-700', 'bg-green-50');
            }, 2000);
        }).catch(function(err) {
            alert('Nie udało się skopiować numeru: ' + trackingNumber);
        });
    }
}
</script>
@endsection