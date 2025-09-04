@extends('layouts.customer')

@section('header')
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-semibold text-gray-900">Reklamacja {{ $complaint->complaint_number }}</h1>
        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $complaint->status_color }}">
            {{ ucfirst(str_replace('_', ' ', $complaint->status)) }}
        </span>
    </div>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
        
        <!-- Complaint Details -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Szczegóły reklamacji</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Temat</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $complaint->subject }}</dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Kategoria</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $complaint->topic->name }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Priorytet</dt>
                        <dd class="mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $complaint->priority_color }}">
                                {{ ucfirst($complaint->priority) }}
                            </span>
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Data utworzenia</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $complaint->created_at->format('d.m.Y H:i') }}</dd>
                    </div>

                    @if($complaint->shipment)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Paczka</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $complaint->shipment->tracking_number }}</dd>
                        </div>
                    @endif

                    @if($complaint->assignedTo)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Przypisane do</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $complaint->assignedTo->name }}</dd>
                        </div>
                    @endif
                </div>

                <div class="mt-6">
                    <dt class="text-sm font-medium text-gray-500">Opis problemu</dt>
                    <dd class="mt-2 text-sm text-gray-900 whitespace-pre-line">{{ $complaint->description }}</dd>
                </div>

                @if($complaint->resolution)
                    <div class="mt-6 p-4 bg-green-50 rounded-lg">
                        <dt class="text-sm font-medium text-green-800">Rozwiązanie</dt>
                        <dd class="mt-2 text-sm text-green-700 whitespace-pre-line">{{ $complaint->resolution }}</dd>
                        @if($complaint->resolved_at)
                            <dd class="mt-2 text-xs text-green-600">
                                Rozwiązano {{ $complaint->resolved_at->format('d.m.Y H:i') }}
                                @if($complaint->resolvedBy)
                                    przez {{ $complaint->resolvedBy->name }}
                                @endif
                            </dd>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <!-- Messages -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Wiadomości</h2>
            </div>
            <div class="divide-y divide-gray-200">
                @forelse($complaint->messages as $message)
                    <div class="p-6 {{ $message->sender_type == 'customer' ? 'bg-blue-50' : 'bg-gray-50' }}">
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex items-center">
                                <span class="text-sm font-medium text-gray-900">
                                    @if($message->sender_type == 'customer')
                                        {{ $message->sender->name ?? 'Klient' }}
                                    @else
                                        {{ $message->sender->name ?? 'Obsługa klienta' }}
                                    @endif
                                </span>
                                @if($message->sender_type == 'customer')
                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Klient
                                    </span>
                                @else
                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Obsługa
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
                    <form action="{{ route('customer.complaints.add-message', $complaint) }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="message" class="block text-sm font-medium text-gray-700">Dodaj wiadomość</label>
                            <textarea name="message" id="message" rows="4" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Wpisz swoją wiadomość..."></textarea>
                            @error('message')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Wyślij wiadomość
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        </div>

        <!-- Files -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Załączniki</h2>
            </div>
            <div class="divide-y divide-gray-200">
                @forelse($complaint->files as $file)
                    <div class="p-6 flex items-center justify-between">
                        <div class="flex items-center">
                            <svg class="h-8 w-8 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                            </svg>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900">{{ $file->original_name }}</p>
                                <p class="text-sm text-gray-500">{{ number_format($file->size / 1024, 1) }} KB</p>
                            </div>
                        </div>
                        <div class="text-sm text-gray-500">
                            {{ $file->created_at->format('d.m.Y H:i') }}
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-gray-500">
                        Brak załączników
                    </div>
                @endforelse
            </div>

            <!-- Upload File Form -->
            @if(!$complaint->isResolved())
                <div class="border-t border-gray-200 p-6">
                    <form action="{{ route('customer.complaints.upload-file', $complaint) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-4">
                            <label for="file" class="block text-sm font-medium text-gray-700">Dodaj załącznik</label>
                            <input type="file" name="file" id="file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            <p class="mt-1 text-xs text-gray-500">Dozwolone formaty: PDF, DOC, DOCX, JPG, PNG, GIF. Maksymalny rozmiar: 10MB</p>
                            @error('file')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Dodaj załącznik
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        </div>

        <!-- Back Button -->
        <div class="flex justify-start">
            <a href="{{ route('customer.complaints.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                ← Powrót do listy reklamacji
            </a>
        </div>
    </div>
</div>
@endsection