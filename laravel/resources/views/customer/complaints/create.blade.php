@extends('layouts.customer')

@section('header')
    <h1 class="text-2xl font-semibold text-gray-900">Nowa reklamacja</h1>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <form action="{{ route('customer.complaints.store') }}" method="POST">
                    @csrf
                    
                    <!-- Shipment Selection -->
                    <div class="mb-6">
                        <label for="shipment_id" class="block text-sm font-medium text-gray-700">
                            Wybierz paczkę (opcjonalne)
                        </label>
                        <select name="shipment_id" id="shipment_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">-- Wybierz paczkę --</option>
                            @foreach($shipments as $shipment)
                                <option value="{{ $shipment->id }}" {{ $selectedShipment && $selectedShipment->id == $shipment->id ? 'selected' : '' }}>
                                    {{ $shipment->tracking_number }} - {{ $shipment->recipient_name }} ({{ $shipment->created_at->format('d.m.Y') }})
                                </option>
                            @endforeach
                        </select>
                        @error('shipment_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Complaint Topic -->
                    <div class="mb-6">
                        <label for="complaint_topic_id" class="block text-sm font-medium text-gray-700">
                            Temat reklamacji <span class="text-red-500">*</span>
                        </label>
                        <select name="complaint_topic_id" id="complaint_topic_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">-- Wybierz temat --</option>
                            @foreach($topics as $topic)
                                <option value="{{ $topic->id }}" {{ old('complaint_topic_id') == $topic->id ? 'selected' : '' }}>
                                    {{ $topic->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('complaint_topic_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Subject -->
                    <div class="mb-6">
                        <label for="subject" class="block text-sm font-medium text-gray-700">
                            Temat <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="subject" id="subject" required value="{{ old('subject') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Krótki opis problemu">
                        @error('subject')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="mb-6">
                        <label for="description" class="block text-sm font-medium text-gray-700">
                            Opis problemu <span class="text-red-500">*</span>
                        </label>
                        <textarea name="description" id="description" rows="6" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Opisz szczegółowo problem...">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Priority -->
                    <div class="mb-6">
                        <label for="priority" class="block text-sm font-medium text-gray-700">
                            Priorytet <span class="text-red-500">*</span>
                        </label>
                        <select name="priority" id="priority" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">-- Wybierz priorytet --</option>
                            <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Niski</option>
                            <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>Średni</option>
                            <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>Wysoki</option>
                            <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Pilny</option>
                        </select>
                        @error('priority')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Contact Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="contact_email" class="block text-sm font-medium text-gray-700">
                                Email kontaktowy
                            </label>
                            <input type="email" name="contact_email" id="contact_email" value="{{ old('contact_email', auth()->user()->email) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            @error('contact_email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="contact_phone" class="block text-sm font-medium text-gray-700">
                                Telefon kontaktowy
                            </label>
                            <input type="tel" name="contact_phone" id="contact_phone" value="{{ old('contact_phone') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            @error('contact_phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Preferred Contact Method -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            Preferowana forma kontaktu <span class="text-red-500">*</span>
                        </label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="radio" name="preferred_contact_method" value="email" {{ old('preferred_contact_method', 'email') == 'email' ? 'checked' : '' }} class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300">
                                <span class="ml-2 text-sm text-gray-700">Email</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="preferred_contact_method" value="phone" {{ old('preferred_contact_method') == 'phone' ? 'checked' : '' }} class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300">
                                <span class="ml-2 text-sm text-gray-700">Telefon</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="preferred_contact_method" value="both" {{ old('preferred_contact_method') == 'both' ? 'checked' : '' }} class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300">
                                <span class="ml-2 text-sm text-gray-700">Email i telefon</span>
                            </label>
                        </div>
                        @error('preferred_contact_method')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex items-center justify-end space-x-4">
                        <a href="{{ route('customer.complaints.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Anuluj
                        </a>
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Wyślij reklamację
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection