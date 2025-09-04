@extends('layouts.admin')

@section('header')
    <h1 class="text-2xl font-semibold text-gray-900">Nowy temat reklamacji</h1>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <form action="{{ route('admin.customer-service.topics.store') }}" method="POST">
                    @csrf
                    
                    <!-- Name -->
                    <div class="mb-6">
                        <label for="name" class="block text-sm font-medium text-gray-700">
                            Nazwa tematu <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" required value="{{ old('name') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="np. Uszkodzona paczka">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="mb-6">
                        <label for="description" class="block text-sm font-medium text-gray-700">
                            Opis
                        </label>
                        <textarea name="description" id="description" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Szczegółowy opis tematu reklamacji...">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Default Priority -->
                    <div class="mb-6">
                        <label for="default_priority" class="block text-sm font-medium text-gray-700">
                            Domyślny priorytet <span class="text-red-500">*</span>
                        </label>
                        <select name="default_priority" id="default_priority" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">-- Wybierz priorytet --</option>
                            <option value="low" {{ old('default_priority') == 'low' ? 'selected' : '' }}>Niski</option>
                            <option value="medium" {{ old('default_priority', 'medium') == 'medium' ? 'selected' : '' }}>Średni</option>
                            <option value="high" {{ old('default_priority') == 'high' ? 'selected' : '' }}>Wysoki</option>
                            <option value="urgent" {{ old('default_priority') == 'urgent' ? 'selected' : '' }}>Pilny</option>
                        </select>
                        @error('default_priority')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Sort Order -->
                    <div class="mb-6">
                        <label for="sort_order" class="block text-sm font-medium text-gray-700">
                            Kolejność sortowania
                        </label>
                        <input type="number" name="sort_order" id="sort_order" min="0" value="{{ old('sort_order') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Pozostaw puste dla automatycznego przypisania">
                        <p class="mt-1 text-sm text-gray-500">Tematy z niższą liczbą będą wyświetlane wcześniej</p>
                        @error('sort_order')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Estimated Resolution Hours -->
                    <div class="mb-6">
                        <label for="estimated_resolution_hours" class="block text-sm font-medium text-gray-700">
                            Szacowany czas rozwiązania (godziny)
                        </label>
                        <input type="number" name="estimated_resolution_hours" id="estimated_resolution_hours" min="1" max="720" value="{{ old('estimated_resolution_hours', 24) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <p class="mt-1 text-sm text-gray-500">Używane do automatycznych SLA</p>
                        @error('estimated_resolution_hours')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Checkboxes -->
                    <div class="space-y-4 mb-6">
                        <div class="flex items-center">
                            <input type="checkbox" name="is_active" id="is_active" {{ old('is_active', true) ? 'checked' : '' }} class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                            <label for="is_active" class="ml-2 block text-sm text-gray-900">
                                Aktywny
                            </label>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" name="customer_visible" id="customer_visible" {{ old('customer_visible', true) ? 'checked' : '' }} class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                            <label for="customer_visible" class="ml-2 block text-sm text-gray-900">
                                Widoczny dla klientów
                            </label>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" name="requires_attachment" id="requires_attachment" {{ old('requires_attachment') ? 'checked' : '' }} class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                            <label for="requires_attachment" class="ml-2 block text-sm text-gray-900">
                                Wymaga załącznika
                            </label>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex items-center justify-end space-x-4">
                        <a href="{{ route('admin.customer-service.topics.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Anuluj
                        </a>
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Utwórz temat
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection