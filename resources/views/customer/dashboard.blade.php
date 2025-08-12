@extends('layouts.customer')

@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Welcome Banner -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-lg shadow-lg text-white p-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold">Witaj ponownie!</h2>
                <p class="text-blue-100 mt-1">{{ auth()->user()->customer->company_name }}</p>
            </div>
            <div class="text-right">
                <p class="text-sm text-blue-100">Twoje saldo</p>
                <p class="text-3xl font-bold">{{ number_format($stats['current_balance'], 2) }} PLN</p>
                @if($stats['current_balance'] < 100)
                <p class="text-sm text-yellow-300 mt-1">
                    <i class="fas fa-exclamation-triangle"></i> Niskie saldo
                </p>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <a href="{{ route('customer.shipments.create') }}" 
           class="bg-white p-6 rounded-lg shadow hover:shadow-md transition-shadow border-l-4 border-green-500">
            <div class="flex items-center">
                <i class="fas fa-plus-circle text-3xl text-green-500"></i>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">Nowa Przesyłka</h3>
                    <p class="text-sm text-gray-600">Utwórz nową przesyłkę</p>
                </div>
            </div>
        </a>

        <a href="{{ route('customer.payments.topup') }}" 
           class="bg-white p-6 rounded-lg shadow hover:shadow-md transition-shadow border-l-4 border-blue-500">
            <div class="flex items-center">
                <i class="fas fa-wallet text-3xl text-blue-500"></i>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">Doładuj Konto</h3>
                    <p class="text-sm text-gray-600">Zwiększ swoje saldo</p>
                </div>
            </div>
        </a>

        <a href="{{ route('customer.shipments.index') }}" 
           class="bg-white p-6 rounded-lg shadow hover:shadow-md transition-shadow border-l-4 border-purple-500">
            <div class="flex items-center">
                <i class="fas fa-search text-3xl text-purple-500"></i>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">Śledź Przesyłki</h3>
                    <p class="text-sm text-gray-600">Monitoruj swoje przesyłki</p>
                </div>
            </div>
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white overflow-hidden shadow rounded-lg p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-shipping-fast text-2xl text-blue-600"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Łączne przesyłki</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ $stats['total_shipments'] }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-calendar-day text-2xl text-green-600"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Dziś</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ $stats['today_shipments'] }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-clock text-2xl text-orange-600"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">W trakcie</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ $stats['pending_shipments'] }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-2xl text-purple-600"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Dostarczone</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ $stats['delivered_shipments'] }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Aktywność w ostatnich 30 dniach</h3>
            <canvas id="monthlyChart" height="300"></canvas>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Rozkład statusów przesyłek</h3>
            <canvas id="statusChart" height="300"></canvas>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Shipments -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Najnowsze przesyłki</h3>
                <div class="space-y-3">
                    @forelse($recent_shipments as $shipment)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center">
                                <i class="fas fa-box text-blue-600 text-sm"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">{{ $shipment->tracking_number }}</p>
                                <p class="text-sm text-gray-500">{{ $shipment->courierService->name }}</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-{{ $shipment->status_color }}-100 text-{{ $shipment->status_color }}-800">
                                {{ $shipment->status_label }}
                            </span>
                            <a href="{{ route('customer.shipments.show', $shipment) }}" class="text-indigo-600 hover:text-indigo-500">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    </div>
                    @empty
                    <p class="text-sm text-gray-500">Brak ostatnich przesyłek.</p>
                    @endforelse
                </div>
                <div class="mt-4">
                    <a href="{{ route('customer.shipments.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                        Zobacz wszystkie przesyłki →
                    </a>
                </div>
            </div>
        </div>

        <!-- Recent Payments -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Ostatnie płatności</h3>
                <div class="space-y-3">
                    @forelse($recent_payments as $payment)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-8 w-8 rounded-full bg-green-100 flex items-center justify-center">
                                <i class="fas fa-credit-card text-green-600 text-sm"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">{{ number_format($payment->amount, 2) }} PLN</p>
                                <p class="text-sm text-gray-500">{{ $payment->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-{{ $payment->status_color }}-100 text-{{ $payment->status_color }}-800">
                                {{ $payment->status_label }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <p class="text-sm text-gray-500">Brak ostatnich płatności.</p>
                    @endforelse
                </div>
                <div class="mt-4">
                    <a href="{{ route('customer.payments.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                        Zobacz wszystkie płatności →
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Account Info -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Informacje o koncie</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="text-center">
                    <p class="text-sm text-gray-500">Obecne saldo</p>
                    <p class="text-2xl font-bold text-green-600">{{ number_format($stats['current_balance'], 2) }} PLN</p>
                </div>
                <div class="text-center">
                    <p class="text-sm text-gray-500">Limit kredytu</p>
                    <p class="text-2xl font-bold text-blue-600">{{ number_format($stats['credit_limit'], 2) }} PLN</p>
                </div>
                <div class="text-center">
                    <p class="text-sm text-gray-500">Łącznie wydano</p>
                    <p class="text-2xl font-bold text-purple-600">{{ number_format($stats['total_spent'], 2) }} PLN</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Monthly Activity Chart
    const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
    const monthlyChart = new Chart(monthlyCtx, {
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
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // Status Distribution Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    const statusChart = new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: @json($status_distribution->pluck('status_label')),
            datasets: [{
                data: @json($status_distribution->pluck('count')),
                backgroundColor: @json($status_distribution->pluck('color_rgba'))
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
</script>
@endsection
