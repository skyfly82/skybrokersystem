@extends('layouts.admin')

@section('title', 'Szczegóły przesyłki - Admin')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <nav class="flex mb-2" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2 text-sm text-gray-500">
                        <li><a href="{{ route('admin.dashboard') }}" class="hover:text-gray-700">Dashboard</a></li>
                        <li><i class="fas fa-chevron-right text-xs"></i></li>
                        <li><a href="{{ route('admin.shipments.index') }}" class="hover:text-gray-700">Przesyłki</a></li>
                        <li><i class="fas fa-chevron-right text-xs"></i></li>
                        <li class="text-gray-900">{{ $shipment->tracking_number ?? 'Przesyłka #'.$shipment->id }}</li>
                    </ol>
                </nav>
                <h1 class="text-2xl font-bold text-gray-900">
                    {{ $shipment->tracking_number ?? 'Przesyłka #'.$shipment->id }}
                </h1>
                <div class="mt-1 flex items-center space-x-4 text-sm text-gray-600">
                    <span>Klient: <strong>{{ $shipment->customer->company_name }}</strong></span>
                    <span>•</span>
                    <span>Utworzona: {{ $shipment->created_at->format('d.m.Y H:i') }}</span>
                    <span>•</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $shipment->status_color_class }}">
                        {{ $shipment->status_label }}
                    </span>
                </div>
            </div>
            <div class="flex space-x-3">
                <form method="POST" action="{{ route('admin.shipments.update-status', $shipment) }}" class="inline">
                    @csrf
                    <div class="flex items-center space-x-2">
                        <select name="status" class="border border-gray-300 rounded-md px-3 py-1 text-sm">
                            <option value="created" {{ $shipment->status === 'created' ? 'selected' : '' }}>Utworzona</option>
                            <option value="paid" {{ $shipment->status === 'paid' ? 'selected' : '' }}>Opłacona</option>
                            <option value="printed" {{ $shipment->status === 'printed' ? 'selected' : '' }}>Wydrukowana</option>
                            <option value="sent" {{ $shipment->status === 'sent' ? 'selected' : '' }}>Nadana</option>
                            <option value="in_transit" {{ $shipment->status === 'in_transit' ? 'selected' : '' }}>W drodze</option>
                            <option value="delivered" {{ $shipment->status === 'delivered' ? 'selected' : '' }}>Dostarczona</option>
                            <option value="cancelled" {{ $shipment->status === 'cancelled' ? 'selected' : '' }}>Anulowana</option>
                        </select>
                        <button type="submit" class="px-3 py-1 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700">
                            Aktualizuj
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        <!-- Left Column - Main Details -->
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
                            <div>{{ $shipment->sender_data['address'] }}</div>
                            <div>{{ $shipment->sender_data['postal_code'] }} {{ $shipment->sender_data['city'] }}</div>
                            <div class="pt-2 space-y-1">
                                <div><i class="fas fa-phone mr-1"></i> {{ $shipment->sender_data['phone'] }}</div>
                                <div><i class="fas fa-envelope mr-1"></i> {{ $shipment->sender_data['email'] }}</div>
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
                            <div>{{ $shipment->recipient_data['address'] }}</div>
                            <div>{{ $shipment->recipient_data['postal_code'] }} {{ $shipment->recipient_data['city'] }}</div>
                            <div class="pt-2 space-y-1">
                                <div><i class="fas fa-phone mr-1"></i> {{ $shipment->recipient_data['phone'] }}</div>
                                <div><i class="fas fa-envelope mr-1"></i> {{ $shipment->recipient_data['email'] }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- API History -->
            @if($shipment->courierApiLogs && $shipment->courierApiLogs->count() > 0)
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-code text-purple-600 mr-2"></i>
                    Historia API kuriera ({{ $shipment->courierApiLogs->count() }})
                </h2>
                
                <div class="space-y-4">
                    @foreach($shipment->courierApiLogs as $log)
                    <div class="border rounded-lg overflow-hidden">
                        <div class="bg-gray-50 px-4 py-3 border-b">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $log->status_color_class }}">
                                        {{ $log->response_status ?? 'N/A' }}
                                    </span>
                                    <span class="font-medium text-gray-900">{{ strtoupper($log->method) }}</span>
                                    <span class="text-sm text-gray-600">{{ $log->action }}</span>
                                    @if($log->response_time_ms)
                                        <span class="text-xs text-gray-500">{{ $log->response_time_ms }}ms</span>
                                    @endif
                                </div>
                                <span class="text-xs text-gray-500">{{ $log->created_at->format('d.m.Y H:i:s') }}</span>
                            </div>
                            <div class="mt-2 text-sm text-gray-700">
                                <strong>Endpoint:</strong> <code class="bg-gray-200 px-1 rounded">{{ $log->endpoint }}</code>
                            </div>
                        </div>
                        
                        <div class="p-4">
                            <div class="grid md:grid-cols-2 gap-4">
                                <!-- Request -->
                                <div>
                                    <h4 class="font-medium text-gray-900 mb-2 flex items-center">
                                        <i class="fas fa-upload text-blue-600 mr-1"></i>
                                        Request
                                    </h4>
                                    @if($log->request_body)
                                        <div class="bg-gray-100 rounded p-3 text-xs">
                                            <pre class="whitespace-pre-wrap font-mono text-gray-800">{{ $log->formatted_request_body }}</pre>
                                        </div>
                                    @else
                                        <p class="text-sm text-gray-500 italic">Brak danych request</p>
                                    @endif
                                </div>
                                
                                <!-- Response -->
                                <div>
                                    <h4 class="font-medium text-gray-900 mb-2 flex items-center">
                                        <i class="fas fa-download text-green-600 mr-1"></i>
                                        Response
                                    </h4>
                                    @if($log->response_body)
                                        <div class="bg-gray-100 rounded p-3 text-xs max-h-64 overflow-y-auto">
                                            <pre class="whitespace-pre-wrap font-mono text-gray-800">{{ $log->formatted_response_body }}</pre>
                                        </div>
                                    @elseif($log->error_message)
                                        <div class="bg-red-50 border border-red-200 rounded p-3 text-xs">
                                            <pre class="whitespace-pre-wrap text-red-800">{{ $log->error_message }}</pre>
                                        </div>
                                    @else
                                        <p class="text-sm text-gray-500 italic">Brak danych response</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Payments -->
            @if($shipment->payments && $shipment->payments->count() > 0)
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-credit-card text-green-600 mr-2"></i>
                    Historia płatności
                </h2>
                
                <div class="space-y-3">
                    @foreach($shipment->payments as $payment)
                        <div class="flex items-center justify-between p-4 border rounded-lg">
                            <div>
                                <div class="font-medium">{{ number_format($payment->amount, 2) }} PLN</div>
                                <div class="text-sm text-gray-600">
                                    {{ $payment->method }} • {{ $payment->created_at->format('d.m.Y H:i') }}
                                </div>
                                @if($payment->external_id)
                                    <div class="text-xs text-gray-500 font-mono">ID: {{ $payment->external_id }}</div>
                                @endif
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

        <!-- Right Column - Summary & Controls -->
        <div class="space-y-6">
            <!-- Customer Info -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Informacje o kliencie</h2>
                <div class="space-y-3">
                    <div>
                        <label class="text-sm font-medium text-gray-700">Firma:</label>
                        <div class="text-sm text-gray-900">{{ $shipment->customer->company_name }}</div>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700">Użytkownik:</label>
                        <div class="text-sm text-gray-900">{{ $shipment->customerUser->full_name ?? 'N/A' }}</div>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700">Saldo:</label>
                        <div class="text-sm text-gray-900">{{ number_format($shipment->customer->balance, 2) }} PLN</div>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700">Status klienta:</label>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $shipment->customer->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ ucfirst($shipment->customer->status) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Shipment Details -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Szczegóły przesyłki</h2>
                <div class="space-y-3">
                    <div>
                        <label class="text-sm font-medium text-gray-700">Kurier:</label>
                        <div class="text-sm text-gray-900">{{ $shipment->courierService->name ?? 'N/A' }}</div>
                    </div>
                    @if($shipment->tracking_number)
                        <div>
                            <label class="text-sm font-medium text-gray-700">Numer śledzenia:</label>
                            <div class="text-sm text-gray-900 font-mono">{{ $shipment->tracking_number }}</div>
                        </div>
                    @endif
                    @if($shipment->reference_number)
                        <div>
                            <label class="text-sm font-medium text-gray-700">Numer referencyjny:</label>
                            <div class="text-sm text-gray-900">{{ $shipment->reference_number }}</div>
                        </div>
                    @endif
                    <div>
                        <label class="text-sm font-medium text-gray-700">Typ usługi:</label>
                        <div class="text-sm text-gray-900">{{ $shipment->service_type }}</div>
                    </div>
                </div>
            </div>

            <!-- Package Info -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Informacje o paczce</h2>
                <div class="grid grid-cols-2 gap-3 text-center">
                    <div class="bg-gray-50 rounded p-2">
                        <div class="text-lg font-bold text-purple-600">{{ $shipment->package_data['length'] ?? 'N/A' }}</div>
                        <div class="text-xs text-gray-500">Długość (cm)</div>
                    </div>
                    <div class="bg-gray-50 rounded p-2">
                        <div class="text-lg font-bold text-purple-600">{{ $shipment->package_data['width'] ?? 'N/A' }}</div>
                        <div class="text-xs text-gray-500">Szerokość (cm)</div>
                    </div>
                    <div class="bg-gray-50 rounded p-2">
                        <div class="text-lg font-bold text-purple-600">{{ $shipment->package_data['height'] ?? 'N/A' }}</div>
                        <div class="text-xs text-gray-500">Wysokość (cm)</div>
                    </div>
                    <div class="bg-gray-50 rounded p-2">
                        <div class="text-lg font-bold text-purple-600">{{ $shipment->package_data['weight'] ?? 'N/A' }}</div>
                        <div class="text-xs text-gray-500">Waga (kg)</div>
                    </div>
                </div>
                
                @if(isset($shipment->package_data['description']))
                    <div class="mt-4 pt-4 border-t">
                        <label class="text-sm font-medium text-gray-700">Opis:</label>
                        <div class="text-sm text-gray-900 mt-1">{{ $shipment->package_data['description'] }}</div>
                    </div>
                @endif
            </div>

            <!-- Cost Summary -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Podsumowanie kosztów</h2>
                <div class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Koszt przesyłki:</span>
                        <span class="font-medium">{{ number_format($shipment->base_price, 2) }} PLN</span>
                    </div>
                    
                    @if($shipment->cod_amount)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Pobranie COD:</span>
                            <span class="font-medium">{{ number_format($shipment->cod_fee, 2) }} PLN</span>
                        </div>
                    @endif
                    
                    @if($shipment->insurance_amount)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Ubezpieczenie:</span>
                            <span class="font-medium">{{ number_format($shipment->insurance_fee, 2) }} PLN</span>
                        </div>
                    @endif
                    
                    <div class="border-t pt-3 flex justify-between font-semibold">
                        <span>Łącznie:</span>
                        <span class="text-blue-600">{{ number_format($shipment->total_price, 2) }} PLN</span>
                    </div>
                </div>
            </div>

            <!-- System Info -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Informacje systemowe</h2>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">UUID:</span>
                        <span class="font-mono text-xs">{{ $shipment->uuid }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Utworzona:</span>
                        <span>{{ $shipment->created_at->format('d.m.Y H:i:s') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Ostatnia aktualizacja:</span>
                        <span>{{ $shipment->updated_at->format('d.m.Y H:i:s') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection