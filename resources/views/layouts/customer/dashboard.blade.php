@extends('layouts.customer')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6" x-data="dashboard">
    <!-- Welcome Header -->
    <div class="bg-white shadow rounded-lg p-6">
        <div class="md:flex md:items-center md:justify-between">
            <div class="flex-1 min-w-0">
                <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                    Witaj ponownie, {{ auth()->user()->first_name }}!
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Firma: {{ auth()->user()->customer->company_name }}
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

    <!-- Balance Alert (if low) -->
    @if($stats['current_balance'] < 100)
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-yellow-400"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-yellow-700">
                    <strong>Niskie saldo konta!</strong> 
                    Aktualne saldo: <span class="font-semibold">{{ number_format($stats['current_balance'], 2) }} PLN</span>
                    <a href="{{ route('customer.payments.topup') }}" class="underline ml-2">Doładuj konto</a>
                </p>
            </div>
        </div>
    </div>
    @endif

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Total Shipments -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-box text-white text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Wszystkie przesyłki</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['total_shipments']) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-5 py-3">
                <div class="text-sm">
                    <a href="{{ route('customer.shipments.index') }}" class="text-blue-600 hover:text-blue-500">
                        Zobacz wszystkie
                    </a>
                </div>
            </div>
        </div>

        <!-- This Month Shipments -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-calendar-alt text-white text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">W tym miesiącu</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['this_month_shipments']) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-5 py-3">
                <div class="text-sm text-gray-500">
                    +{{ $stats['today_shipments'] }} dzisiaj
                </div>
            </div>
        </div>

        <!-- Pending Shipments -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-truck text-white text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">W transporcie</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['pending_shipments']) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-5 py-3">
                <div class="text-sm">
                    <span class="text-green-600">{{ $stats['delivered_shipments'] }} dostarczonych</span>
                </div>
            </div>
        </div>

        <!-- Account Balance -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-wallet text-white text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Saldo konta</dt>
                            <dd class="text-lg font-medium text-gray-900">
                                {{ number_format($stats['current_balance'], 2) }} PLN
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-5 py-3">
                <div class="text-sm">
                    <a href="{{ route('customer.payments.topup') }}" class="text-blue-600 hover:text-blue-500">
                        Doładuj konto
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Monthly Shipments Chart -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-5 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Przesyłki w ostatnich 30 dniach</h3>
            </div>
            <div class="p-6">
                <canvas id="monthlyChart" height="200"></canvas>
            </div>
        </div>

        <!-- Status Distribution Chart -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-5 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Rozkład statusów</h3>
            </div>
            <div class="p-6">
                <canvas id="statusChart" height="200"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Shipments -->
        <div class="lg:col-span-2 bg-white shadow rounded-lg">
            <div class="px-6 py-5 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900">Ostatnie przesyłki</h3>
                    <a href="{{ route('customer.shipments.index') }}" class="text-sm text-blue-600 hover:text-blue-500">
                        Zobacz wszystkie
                    </a>
                </div>
            </div>
            <div class="divide-y divide-gray-200">
                @forelse($recent_shipments as $shipment)
                <div class="px-6 py-4 hover:bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($shipment->status === 'delivered') bg-green-100 text-green-800
                                        @elseif($shipment->status === 'in_transit') bg-blue-100 text-blue-800
                                        @elseif($shipment->status === 'cancelled') bg-red-100 text-red-800
                                        @else bg-yellow-100 text-yellow-800
                                        @endif">
                                        {{ $shipment->status_label }}
                                    </span>
                                </div>
                                <div class="ml-4 flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">
                                        {{ $shipment->tracking_number }}
                                    </p>
                                    <p class="text-sm text-gray-500 truncate">
                                        {{ $shipment->recipient_data['name'] ?? 'Brak danych odbiorcy' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="text-right">
                                <p class="text-sm text-gray-500">{{ $shipment->courierService->name }}</p>
                                <p class="text-xs text-gray-400">{{ $shipment->created_at->format('d.m.Y H:i') }}</p>
                            </div>
                            <a href="{{ route('customer.shipments.show', $shipment) }}" 
                               class="text-gray-400 hover:text-gray-500">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="px-6 py-8 text-center">
                    <i class="fas fa-box text-4xl text-gray-300"></i>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Brak przesyłek</h3>
                    <p class="mt-1 text-sm text-gray-500">Rozpocznij wysyłanie pierwszej przesyłki.</p>
                    <div class="mt-6">
                        <a href="{{ route('customer.shipments.create') }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            <i class="fas fa-plus mr-2"></i>
                            Utwórz przesyłkę
                        </a>
                    </div>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Recent Payments & Quick Actions -->
        <div class="space-y-6">
            <!-- Recent Payments -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-5 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900">Ostatnie płatności</h3>
                        <a href="{{ route('customer.payments.index') }}" class="text-sm text-blue-600 hover:text-blue-500">
                            Zobacz wszystkie
                        </a>
                    </div>
                </div>
                <div class="divide-y divide-gray-200">
                    @forelse($recent_payments->take(5) as $payment)
                    <div class="px-6 py-3">
                        <div class="flex items-center justify-between">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900">
                                    {{ ucfirst($payment->type) }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    {{ $payment->created_at->format('d.m.Y H:i') }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium 
                                    @if($payment->status === 'completed') text-green-600
                                    @elseif($payment->status === 'failed') text-red-600
                                    @else text-yellow-600
                                    @endif">
                                    {{ number_format($payment->amount, 2) }} PLN
                                </p>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                    @if($payment->status === 'completed') bg-green-100 text-green-800
                                    @elseif($payment->status === 'failed') bg-red-100 text-red-800
                                    @else bg-yellow-100 text-yellow-800
                                    @endif">
                                    {{ $payment->status_label }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="px-6 py-8 text-center">
                        <p class="text-sm text-gray-500">Brak płatności</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-5 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Szybkie akcje</h3>
                </div>
                <div class="p-6 space-y-3">
                    <a href="{{ route('customer.shipments.create') }}" 
                       class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium inline-flex items-center justify-center">
                        <i class="fas fa-plus mr-2"></i>
                        Nowa przesyłka
                    </a>
                    <a href="{{ route('customer.payments.topup') }}" 
                       class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium inline-flex items-center justify-center">
                        <i class="fas fa-wallet mr-2"></i>
                        Doładuj konto
                    </a>
                    <a href="{{ route('customer.shipments.index', ['status' => 'in_transit']) }}" 
                       class="w-full bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-md text-sm font-medium inline-flex items-center justify-center">
                        <i class="fas fa-truck mr-2"></i>
                        Śledź przesyłki
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Monthly Shipments Chart
    const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
    new Chart(monthlyCtx, {
        type: 'line',
        data: {
            labels: @json($monthly_shipments->pluck('date')),
            datasets: [{
                label: 'Przesyłki',
                data: @json($monthly_shipments->pluck('count')),
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Status Distribution Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: @json($status_distribution->pluck('status')),
            datasets: [{
                data: @json($status_distribution->pluck('count')),
                backgroundColor: [
                    '#10B981', // delivered - green
                    '#3B82F6', // in_transit - blue
                    '#F59E0B', // created - yellow
                    '#EF4444', // cancelled - red
                    '#8B5CF6', // returned - purple
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
});

// Alpine.js dashboard data
document.addEventListener('alpine:init', () => {
    Alpine.data('dashboard', () => ({
        stats: @json($stats),
        
        init() {
            // Refresh stats every 5 minutes
            setInterval(() => {
                this.refreshStats();
            }, 300000);
        },
        
        async refreshStats() {
            try {
                const response = await fetch('{{ route("customer.dashboard.stats") }}');
                const data = await response.json();
                this.stats = data;
            } catch (error) {
                console.error('Failed to refresh stats:', error);
            }
        }
    }));
});
</script>
@endpush
@endsection