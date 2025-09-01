@extends('layouts.admin')

@section('header')
    <h1 class="text-2xl font-semibold text-gray-900">Obsługa Klienta - Dashboard</h1>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-red-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    Otwarte reklamacje
                                </dt>
                                <dd class="text-lg font-medium text-gray-900">
                                    {{ $stats['open_complaints'] }}
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
                            <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    W trakcie
                                </dt>
                                <dd class="text-lg font-medium text-gray-900">
                                    {{ $stats['in_progress'] }}
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
                            <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    Czeka na klienta
                                </dt>
                                <dd class="text-lg font-medium text-gray-900">
                                    {{ $stats['waiting_customer'] }}
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
                            <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    Rozwiązane dzisiaj
                                </dt>
                                <dd class="text-lg font-medium text-gray-900">
                                    {{ $stats['resolved_today'] }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Szybkie akcje</h3>
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('admin.customer-service.complaints.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    Wszystkie reklamacje
                </a>
                <a href="{{ route('admin.customer-service.complaints.index', ['status' => 'urgent']) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700">
                    Pilne reklamacje
                </a>
                <a href="{{ route('admin.customer-service.topics.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Zarządzaj tematami
                </a>
            </div>
        </div>

        <!-- Recent Complaints -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Najnowsze reklamacje</h3>
            </div>
            <div class="divide-y divide-gray-200">
                @forelse($recent_complaints as $complaint)
                    <div class="px-6 py-4 hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $complaint->status_color }}">
                                        {{ ucfirst(str_replace('_', ' ', $complaint->status)) }}
                                    </span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-gray-900 truncate">
                                        {{ $complaint->subject }}
                                    </p>
                                    <div class="mt-1 flex items-center text-sm text-gray-500 space-x-4">
                                        <span>{{ $complaint->complaint_number }}</span>
                                        <span>{{ $complaint->customer->company_name }}</span>
                                        <span>{{ $complaint->topic->name }}</span>
                                        <span>{{ $complaint->created_at->format('d.m.Y H:i') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $complaint->priority_color }}">
                                    {{ ucfirst($complaint->priority) }}
                                </span>
                                <a href="{{ route('admin.customer-service.complaints.show', $complaint) }}" class="text-blue-600 hover:text-blue-500">
                                    Zobacz
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-12 text-center">
                        <p class="text-gray-500">Brak reklamacji</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Urgent Complaints -->
        @if($urgent_complaints->count() > 0)
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-red-600">⚠️ Pilne reklamacje</h3>
            </div>
            <div class="divide-y divide-gray-200">
                @foreach($urgent_complaints as $complaint)
                    <div class="px-6 py-4 bg-red-50 hover:bg-red-100">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        PILNE
                                    </span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-gray-900 truncate">
                                        {{ $complaint->subject }}
                                    </p>
                                    <div class="mt-1 flex items-center text-sm text-gray-500 space-x-4">
                                        <span>{{ $complaint->complaint_number }}</span>
                                        <span>{{ $complaint->customer->company_name }}</span>
                                        <span>{{ $complaint->created_at->format('d.m.Y H:i') }}</span>
                                    </div>
                                </div>
                            </div>
                            <a href="{{ route('admin.customer-service.complaints.show', $complaint) }}" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-700">
                                Obsłuż teraz
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection