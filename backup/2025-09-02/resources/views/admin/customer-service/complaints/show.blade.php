@extends('layouts.admin')

@section('header')
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Reklamacja {{ $complaint->complaint_number }}</h1>
            <p class="mt-1 text-sm text-gray-500">{{ $complaint->customer->company_name }} • {{ $complaint->created_at->format('d.m.Y H:i') }}</p>
        </div>
        <div class="flex items-center space-x-2">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $complaint->status_color }}">
                {{ ucfirst(str_replace('_', ' ', $complaint->status)) }}
            </span>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $complaint->priority_color }}">
                {{ ucfirst($complaint->priority) }}
            </span>
        </div>
    </div>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
        
        <!-- Complaint Details -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Szczegóły reklamacji</h3>
            </div>
            <div class="p-6">
                <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Temat</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $complaint->subject }}</dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Kategoria</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $complaint->topic->name }}</dd>
                    </div>

                    @if($complaint->shipment)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Paczka</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $complaint->shipment->tracking_number }}</dd>
                        </div>
                    @endif

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Kontakt</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $complaint->contact_email }}
                            @if($complaint->contact_phone)
                                <br>{{ $complaint->contact_phone }}
                            @endif
                        </dd>
                    </div>

                    @if($complaint->assignedTo)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Przypisane do</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $complaint->assignedTo->name }}</dd>
                        </div>
                    @endif

                    @if($complaint->resolved_at)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Rozwiązano</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $complaint->resolved_at->format('d.m.Y H:i') }}
                                @if($complaint->resolvedBy)
                                    przez {{ $complaint->resolvedBy->name }}
                                @endif
                            </dd>
                        </div>
                    @endif
                </dl>

                <div class="mt-6">
                    <dt class="text-sm font-medium text-gray-500">Opis problemu</dt>
                    <dd class="mt-2 text-sm text-gray-900 whitespace-pre-line">{{ $complaint->description }}</dd>
                </div>

                @if($complaint->resolution)
                    <div class="mt-6 p-4 bg-green-50 rounded-lg">
                        <dt class="text-sm font-medium text-green-800">Rozwiązanie</dt>
                        <dd class="mt-2 text-sm text-green-700 whitespace-pre-line">{{ $complaint->resolution }}</dd>
                    </div>
                @endif
            </div>
        </div>

        <!-- Quick Actions -->
        @if(!$complaint->isResolved())
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Szybkie akcje</h3>
            </div>
            <div class="p-6 space-y-4">
                <!-- Assignment -->
                @if(!$complaint->assignedTo)
                <form method="POST" action="{{ route('admin.customer-service.complaints.assign', $complaint) }}" class="flex items-center space-x-2">
                    @csrf
                    <select name="assigned_to" required class="flex-1 border-gray-300 rounded-md shadow-sm">
                        <option value="">Przypisz do...</option>
                        @foreach($agents as $agent)
                            <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700">
                        Przypisz
                    </button>
                </form>
                @endif

                <!-- Status Update -->
                <form method="POST" action="{{ route('admin.customer-service.complaints.update-status', $complaint) }}" class="flex items-center space-x-2">
                    @csrf
                    @method('PATCH')
                    <select name="status" class="flex-1 border-gray-300 rounded-md shadow-sm">
                        <option value="open" {{ $complaint->status == 'open' ? 'selected' : '' }}>Otwarte</option>
                        <option value="in_progress" {{ $complaint->status == 'in_progress' ? 'selected' : '' }}>W trakcie</option>
                        <option value="waiting_customer" {{ $complaint->status == 'waiting_customer' ? 'selected' : '' }}>Czeka na klienta</option>
                        <option value="resolved" {{ $complaint->status == 'resolved' ? 'selected' : '' }}>Rozwiązane</option>
                        <option value="closed" {{ $complaint->status == 'closed' ? 'selected' : '' }}>Zamknięte</option>
                    </select>
                    <button type="submit" class="px-4 py-2 bg-gray-600 text-white text-sm rounded-md hover:bg-gray-700">
                        Zmień status
                    </button>
                </form>

                <!-- Quick Resolve -->
                <form method="POST" action="{{ route('admin.customer-service.complaints.resolve', $complaint) }}" class="space-y-2">
                    @csrf
                    <textarea name="resolution" rows="3" placeholder="Opisz rozwiązanie..." required class="w-full border-gray-300 rounded-md shadow-sm"></textarea>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white text-sm rounded-md hover:bg-green-700">
                        Rozwiąż reklamację
                    </button>
                </form>
            </div>
        </div>
        @endif

        <!-- Messages -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Wiadomości</h3>
            </div>
            <div class="max-h-96 overflow-y-auto">
                @forelse($complaint->messages as $message)
                    <div class="p-6 {{ $message->sender_type == 'customer' ? 'bg-blue-50' : 'bg-gray-50' }} {{ !$loop->last ? 'border-b border-gray-200' : '' }}">
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex items-center">
                                <span class="text-sm font-medium text-gray-900">
                                    @if($message->sender_type == 'customer')
                                        {{ $message->sender->name ?? 'Klient' }} 
                                    @else
                                        {{ $message->sender->name ?? 'Obsługa' }}
                                    @endif
                                </span>
                                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $message->sender_type == 'customer' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $message->sender_type == 'customer' ? 'Klient' : 'Obsługa' }}
                                </span>
                                @if($message->is_internal)
                                    <span class="ml-1 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Wewnętrzna
                                    </span>
                                @endif
                            </div>
                            <span class="text-xs text-gray-500">{{ $message->created_at->format('d.m.Y H:i') }}</span>
                        </div>
                        <div class="text-sm text-gray-700 whitespace-pre-line">{{ $message->message }}</div>
                    </div>
                @empty
                    <div class="p-6 text-center text-gray-500">
                        Brak wiadomości
                    </div>
                @endforelse
            </div>

            <!-- Add Message Form -->
            @if(!$complaint->isResolved())
                <div class="border-t border-gray-200 p-6">
                    <form method="POST" action="{{ route('admin.customer-service.complaints.add-message', $complaint) }}">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label for="message" class="block text-sm font-medium text-gray-700">Dodaj wiadomość</label>
                                <textarea name="message" id="message" rows="4" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <input type="checkbox" name="is_internal" id="is_internal" class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                                    <label for="is_internal" class="ml-2 block text-sm text-gray-900">
                                        Wiadomość wewnętrzna (niewidoczna dla klienta)
                                    </label>
                                </div>
                                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                    Wyślij wiadomość
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            @endif
        </div>

        <!-- Files -->
        @if($complaint->files->count() > 0)
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Załączniki</h3>
            </div>
            <div class="divide-y divide-gray-200">
                @foreach($complaint->files as $file)
                    <div class="p-6 flex items-center justify-between">
                        <div class="flex items-center">
                            <svg class="h-8 w-8 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                            </svg>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900">{{ $file->original_name }}</p>
                                <p class="text-sm text-gray-500">{{ number_format($file->size / 1024, 1) }} KB • {{ $file->created_at->format('d.m.Y H:i') }}</p>
                            </div>
                        </div>
                        <button class="text-blue-600 hover:text-blue-500 text-sm font-medium">
                            Pobierz
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Back Button -->
        <div class="flex justify-start">
            <a href="{{ route('admin.customer-service.complaints.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                ← Powrót do listy
            </a>
        </div>
    </div>
</div>
@endsection