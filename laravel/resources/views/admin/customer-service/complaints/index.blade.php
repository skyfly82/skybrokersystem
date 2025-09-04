@extends('layouts.admin')

@section('header')
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-semibold text-gray-900">Reklamacje klientów</h1>
        <div class="flex items-center space-x-4">
            <!-- Filters -->
            <form method="GET" class="flex items-center space-x-2">
                <select name="status" class="text-sm border-gray-300 rounded-md">
                    <option value="">Wszystkie statusy</option>
                    <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Otwarte</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>W trakcie</option>
                    <option value="waiting_customer" {{ request('status') == 'waiting_customer' ? 'selected' : '' }}>Czeka na klienta</option>
                    <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Rozwiązane</option>
                </select>
                
                <select name="priority" class="text-sm border-gray-300 rounded-md">
                    <option value="">Wszystkie priorytety</option>
                    <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Pilne</option>
                    <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>Wysokie</option>
                    <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Średnie</option>
                    <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Niskie</option>
                </select>
                
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Szukaj..." class="text-sm border-gray-300 rounded-md">
                
                <button type="submit" class="px-3 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700">
                    Filtruj
                </button>
                
                @if(request()->anyFilled(['status', 'priority', 'search']))
                    <a href="{{ route('admin.customer-service.complaints.index') }}" class="px-3 py-2 bg-gray-600 text-white text-sm rounded-md hover:bg-gray-700">
                        Wyczyść
                    </a>
                @endif
            </form>
        </div>
    </div>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reklamacja</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Klient</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priorytet</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Przypisane</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Akcje</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($complaints as $complaint)
                            <tr class="hover:bg-gray-50 {{ $complaint->priority === 'urgent' ? 'bg-red-50' : '' }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $complaint->subject }}</div>
                                    <div class="text-sm text-gray-500">{{ $complaint->complaint_number }}</div>
                                    <div class="text-xs text-gray-400">{{ $complaint->topic->name }}</div>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $complaint->customer->company_name }}</div>
                                    <div class="text-sm text-gray-500">{{ $complaint->customerUser->name }}</div>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $complaint->status_color }}">
                                        {{ ucfirst(str_replace('_', ' ', $complaint->status)) }}
                                    </span>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $complaint->priority_color }}">
                                        {{ ucfirst($complaint->priority) }}
                                    </span>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($complaint->assignedTo)
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-8 w-8">
                                                <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                                                    <span class="text-xs font-medium text-gray-700">
                                                        {{ substr($complaint->assignedTo->name, 0, 2) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="ml-2">
                                                <div class="text-sm font-medium text-gray-900">{{ $complaint->assignedTo->name }}</div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-gray-400">Nieprzypisane</span>
                                    @endif
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $complaint->created_at->format('d.m.Y H:i') }}
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                    <a href="{{ route('admin.customer-service.complaints.show', $complaint) }}" class="text-indigo-600 hover:text-indigo-900">Zobacz</a>
                                    
                                    @if(!$complaint->assignedTo)
                                        <form method="POST" action="{{ route('admin.customer-service.complaints.assign', $complaint) }}" class="inline">
                                            @csrf
                                            <input type="hidden" name="assigned_to" value="{{ auth('system_user')->id() }}">
                                            <button type="submit" class="text-blue-600 hover:text-blue-900">Przypisz do mnie</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="text-gray-500">
                                        <p>Brak reklamacji do wyświetlenia</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($complaints->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $complaints->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection