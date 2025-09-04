@extends('layouts.admin')

@section('title', 'Dashboard')

@section('header')
    <h1 class="text-3xl font-bold leading-tight text-gray-900">Dashboard</h1>
    <p class="mt-2 text-sm text-gray-600">Welcome back! Here's what's happening with your courier system today.</p>
@endsection

@section('content')
    <!-- Stats Overview -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
        <!-- Total Customers -->
        <div class="stats-card">
            <div class="stats-card-body">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex h-8 w-8 items-center justify-center rounded-md bg-primary-500 text-white">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="stats-label">Total Customers</dt>
                            <dd class="stats-value">{{ number_format($stats['total_customers'] ?? 0) }}</dd>
                        </dl>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="stats-change positive">
                        +{{ $stats['active_customers'] ?? 0 }} active
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Shipments -->
        <div class="stats-card">
            <div class="stats-card-body">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex h-8 w-8 items-center justify-center rounded-md bg-success-500 text-white">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0V8.25a1.5 1.5 0 013 0v10.5zM12 12.75a1.5 1.5 0 01-3 0V8.25a1.5 1.5 0 013 0v4.5zm3.75 6a1.5 1.5 0 01-3 0V8.25a1.5 1.5 0 013 0v10.5z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="stats-label">Total Shipments</dt>
                            <dd class="stats-value">{{ number_format($stats['total_shipments'] ?? 0) }}</dd>
                        </dl>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="stats-change positive">
                        +{{ $stats['today_shipments'] ?? 0 }} today
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue -->
        <div class="stats-card">
            <div class="stats-card-body">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex h-8 w-8 items-center justify-center rounded-md bg-warning-500 text-white">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="stats-label">Total Revenue</dt>
                            <dd class="stats-value">{{ number_format($stats['total_revenue'] ?? 0, 2) }} PLN</dd>
                        </dl>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="stats-change positive">
                        +12.5% vs last month
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Payments -->
        <div class="stats-card">
            <div class="stats-card-body">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex h-8 w-8 items-center justify-center rounded-md bg-danger-500 text-white">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="stats-label">Pending Payments</dt>
                            <dd class="stats-value">{{ number_format($stats['pending_payments'] ?? 0) }}</dd>
                        </dl>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="stats-change negative">
                        Requires attention
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Tables Grid -->
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
        <!-- Recent Shipments Chart -->
        <div class="card">
            <div class="card-header">
                <h3 class="text-lg font-semibold text-gray-900">Shipments Overview</h3>
                <p class="text-sm text-gray-600">Last 30 days activity</p>
            </div>
            <div class="card-body">
                <canvas id="shipmentsChart" width="400" height="200"></canvas>
            </div>
        </div>

        <!-- Revenue Chart -->
        <div class="card">
            <div class="card-header">
                <h3 class="text-lg font-semibold text-gray-900">Revenue Trend</h3>
                <p class="text-sm text-gray-600">Daily revenue for the last 30 days</p>
            </div>
            <div class="card-body">
                <canvas id="revenueChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Activity Section -->
    <div class="mt-8 grid grid-cols-1 gap-8 lg:grid-cols-2">
        <!-- Recent Customers -->
        <div class="card">
            <div class="card-header">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Customers</h3>
                    <a href="{{ route('admin.customers.index') }}" class="text-sm text-primary-600 hover:text-primary-700">View all</a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="flow-root">
                    <ul role="list" class="divide-y divide-gray-200">
                        @forelse($recent_customers ?? [] as $customer)
                            <li class="px-6 py-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-300">
                                                <span class="text-sm font-medium text-gray-700">
                                                    {{ substr($customer->company_name ?? 'N/A', 0, 2) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $customer->company_name ?? 'N/A' }}</div>
                                            <div class="text-sm text-gray-500">{{ $customer->email ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="badge badge-{{ ($customer->status ?? 'pending') === 'active' ? 'success' : (($customer->status ?? 'pending') === 'pending' ? 'warning' : 'danger') }}">
                                            {{ ucfirst($customer->status ?? 'pending') }}
                                        </span>
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li class="px-6 py-4 text-center text-gray-500">
                                No recent customers
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        <!-- Recent Shipments -->
        <div class="card">
            <div class="card-header">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Shipments</h3>
                    <a href="{{ route('admin.shipments.index') }}" class="text-sm text-primary-600 hover:text-primary-700">View all</a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="flow-root">
                    <ul role="list" class="divide-y divide-gray-200">
                        @forelse($recent_shipments ?? [] as $shipment)
                            <li class="px-6 py-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0V8.25a1.5 1.5 0 013 0v10.5zM12 12.75a1.5 1.5 0 01-3 0V8.25a1.5 1.5 0 013 0v4.5zm3.75 6a1.5 1.5 0 01-3 0V8.25a1.5 1.5 0 013 0v10.5z" />
                                            </svg>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $shipment->tracking_number ?: 'N/A' }}</div>
                                            <div class="text-sm text-gray-500">{{ $shipment->customer->company_name ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="badge badge-{{ $shipment->status === 'delivered' ? 'success' : ($shipment->status === 'in_transit' ? 'primary' : 'warning') }}">
                                            {{ ucfirst(str_replace('_', ' ', $shipment->status)) }}
                                        </span>
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li class="px-6 py-4 text-center text-gray-500">
                                No recent shipments
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-8">
        <div class="card">
            <div class="card-header">
                <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
            </div>
            <div class="card-body">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <a href="{{ route('admin.customers.create') }}" class="btn btn-outline hover-lift">
                        <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-3.75-5.25a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                        </svg>
                        Add Customer
                    </a>
                    
                    <a href="{{ route('admin.shipments.create') }}" class="btn btn-outline hover-lift">
                        <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Create Shipment
                    </a>
                    
                    <a href="{{ route('admin.reports.shipments') }}" class="btn btn-outline hover-lift">
                        <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                        </svg>
                        View Reports
                    </a>
                    
                    <a href="{{ route('admin.settings.general') }}" class="btn btn-outline hover-lift">
                        <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Settings
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Shipments Chart
    const shipmentsCtx = document.getElementById('shipmentsChart').getContext('2d');
    new Chart(shipmentsCtx, {
        type: 'line',
        data: {
            labels: @json($monthly_shipments->pluck('date') ?? []),
            datasets: [{
                label: 'Shipments',
                data: @json($monthly_shipments->pluck('count') ?? []),
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
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

    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    new Chart(revenueCtx, {
        type: 'bar',
        data: {
            labels: @json($revenue_chart->pluck('date') ?? []),
            datasets: [{
                label: 'Revenue (PLN)',
                data: @json($revenue_chart->pluck('total') ?? []),
                backgroundColor: 'rgba(34, 197, 94, 0.8)',
                borderColor: 'rgb(34, 197, 94)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
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
});
</script>
@endpush