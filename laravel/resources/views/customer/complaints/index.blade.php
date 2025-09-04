@extends('layouts.customer')

@section('header')
    <h1 class="text-2xl font-semibold text-gray-900">Reklamacje</h1>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <!-- Header -->
            <div class="border-b border-gray-200 px-6 py-4 flex justify-between items-center">
                <h2 class="text-lg font-medium text-gray-900">Twoje reklamacje</h2>
                <a href="{{ route('customer.complaints.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Nowa reklamacja
                </a>
            </div>

            <!-- Complaints List -->
            <div class="divide-y divide-gray-200">
                @forelse($complaints as $complaint)
                    <div class="px-6 py-4 hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $complaint->status_color }}">
                                        {{ ucfirst(str_replace('_', ' ', $complaint->status)) }}
                                    </span>
                                </div>
                                
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center space-x-2">
                                        <p class="text-sm font-medium text-gray-900 truncate">
                                            {{ $complaint->subject }}
                                        </p>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $complaint->priority_color }}">
                                            {{ ucfirst($complaint->priority) }}
                                        </span>
                                    </div>
                                    <div class="mt-1 flex items-center text-sm text-gray-500 space-x-4">
                                        <span>{{ $complaint->complaint_number }}</span>
                                        @if($complaint->shipment)
                                            <span>Paczka: {{ $complaint->shipment->tracking_number }}</span>
                                        @endif
                                        <span>{{ $complaint->topic->name }}</span>
                                        <span>{{ $complaint->created_at->format('d.m.Y H:i') }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-center space-x-2">
                                @if($complaint->assignedTo)
                                    <div class="text-sm text-gray-500">
                                        Przypisane do: {{ $complaint->assignedTo->name }}
                                    </div>
                                @endif
                                <a href="{{ route('customer.complaints.show', $complaint) }}" class="text-blue-600 hover:text-blue-500">
                                    Zobacz szczegóły
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Brak reklamacji</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Nie masz jeszcze żadnych reklamacji. Kliknij przycisk poniżej, aby utworzyć nową.
                        </p>
                        <div class="mt-6">
                            <a href="{{ route('customer.complaints.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent shadow-sm text-sm font-medium rounded-md text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Nowa reklamacja
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($complaints->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $complaints->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection