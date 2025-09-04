@extends('layouts.admin')

@section('title', 'Nowy cennik')

@section('content')
<div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="md:flex md:items-center md:justify-between mb-6">
        <div class="flex-1 min-w-0">
            <nav class="flex mb-4" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('admin.settings.index') }}" class="text-gray-500 hover:text-gray-700">
                            Ustawienia
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <a href="{{ route('admin.settings.pricing') }}" class="ml-1 text-gray-500 hover:text-gray-700 md:ml-2">
                                Cenniki
                            </a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="ml-1 text-gray-700 md:ml-2">Nowy cennik</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                Utwórz nowy cennik
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Skonfiguruj nowy cennik dla usług kurierskich
            </p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.settings.pricing.store') }}" class="space-y-6">
        @csrf
        
        <!-- Basic Information -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Informacje podstawowe</h3>
            </div>
            <div class="px-6 py-4 space-y-4">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Nazwa cennika *</label>
                        <input type="text" name="name" id="name" required 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                               placeholder="np. Cennik premium">
                    </div>
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Status początkowy</label>
                        <select name="status" id="status" 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="draft">Szkic</option>
                            <option value="inactive">Nieaktywny</option>
                            <option value="active">Aktywny</option>
                        </select>
                    </div>
                </div>
                
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Opis</label>
                    <textarea name="description" id="description" rows="3"
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Opis cennika..."></textarea>
                </div>
            </div>
        </div>

        <!-- Pricing Strategy -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Strategia cenowa</h3>
                <p class="mt-1 text-sm text-gray-500">Wybierz sposób tworzenia cennika</p>
            </div>
            <div class="px-6 py-4">
                <div class="space-y-4">
                    <label class="flex items-start">
                        <input type="radio" name="strategy" value="copy" checked
                               class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                        <div class="ml-3">
                            <div class="text-sm font-medium text-gray-900">Skopiuj z istniejącego cennika</div>
                            <div class="text-sm text-gray-500">Utwórz cennik na podstawie istniejącego z możliwością modyfikacji marż</div>
                            
                            <div class="mt-3 ml-0">
                                <label for="copy_from" class="block text-sm font-medium text-gray-700">Cennik źródłowy</label>
                                <select name="copy_from" id="copy_from"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="1">Cennik bazowy (152 usługi)</option>
                                    <option value="2">Cennik premium (98 usług)</option>
                                </select>
                            </div>
                            
                            <div class="mt-3 grid grid-cols-2 gap-4">
                                <div>
                                    <label for="margin_adjustment" class="block text-sm font-medium text-gray-700">Korekta marży (%)</label>
                                    <input type="number" name="margin_adjustment" id="margin_adjustment" value="0" step="0.1"
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                           placeholder="np. +2.5">
                                    <p class="mt-1 text-xs text-gray-500">Dodatkowa marża do zastosowania (może być ujemna)</p>
                                </div>
                                <div>
                                    <label for="minimum_margin" class="block text-sm font-medium text-gray-700">Minimalna marża (%)</label>
                                    <input type="number" name="minimum_margin" id="minimum_margin" value="5" min="0" step="0.1"
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>
                        </div>
                    </label>

                    <label class="flex items-start">
                        <input type="radio" name="strategy" value="blank"
                               class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                        <div class="ml-3">
                            <div class="text-sm font-medium text-gray-900">Utwórz pusty cennik</div>
                            <div class="text-sm text-gray-500">Rozpocznij z pustym cennikiem i dodawaj usługi ręcznie</div>
                            
                            <div class="mt-3 ml-0">
                                <label for="default_margin" class="block text-sm font-medium text-gray-700">Domyślna marża (%)</label>
                                <input type="number" name="default_margin" id="default_margin" value="15" min="0" step="0.1"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <p class="mt-1 text-xs text-gray-500">Marża stosowana automatycznie dla nowych usług</p>
                            </div>
                        </div>
                    </label>

                    <label class="flex items-start">
                        <input type="radio" name="strategy" value="import"
                               class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                        <div class="ml-3">
                            <div class="text-sm font-medium text-gray-900">Importuj z pliku</div>
                            <div class="text-sm text-gray-500">Wczytaj cennik z pliku Excel lub CSV</div>
                            
                            <div class="mt-3 ml-0">
                                <label for="import_file" class="block text-sm font-medium text-gray-700">Plik do importu</label>
                                <input type="file" name="import_file" id="import_file" accept=".xlsx,.xls,.csv"
                                       class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                <p class="mt-1 text-xs text-gray-500">
                                    <a href="#" class="text-blue-600 hover:text-blue-500">Pobierz szablon</a> | 
                                    Obsługiwane formaty: Excel (.xlsx, .xls), CSV
                                </p>
                            </div>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <!-- Courier Selection -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Kurierzy</h3>
                <p class="mt-1 text-sm text-gray-500">Wybierz kurierów, których usługi mają być uwzględnione w cenniku</p>
            </div>
            <div class="px-6 py-4">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <label class="flex items-center">
                        <input type="checkbox" name="couriers[]" value="inpost" checked
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <div class="ml-3 flex items-center">
                            <div class="h-6 w-6 bg-yellow-500 rounded flex items-center justify-center mr-2">
                                <span class="text-white text-xs font-bold">InP</span>
                            </div>
                            <span class="text-sm font-medium text-gray-900">InPost</span>
                            <span class="ml-2 text-xs text-gray-500">(89 usług)</span>
                        </div>
                    </label>
                    
                    <label class="flex items-center">
                        <input type="checkbox" name="couriers[]" value="dpd"
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <div class="ml-3 flex items-center">
                            <div class="h-6 w-6 bg-red-500 rounded flex items-center justify-center mr-2">
                                <span class="text-white text-xs font-bold">DPD</span>
                            </div>
                            <span class="text-sm font-medium text-gray-900">DPD</span>
                            <span class="ml-2 text-xs text-gray-500">(41 usług)</span>
                        </div>
                    </label>
                    
                    <label class="flex items-center">
                        <input type="checkbox" name="couriers[]" value="ups"
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <div class="ml-3 flex items-center">
                            <div class="h-6 w-6 bg-yellow-600 rounded flex items-center justify-center mr-2">
                                <span class="text-white text-xs font-bold">UPS</span>
                            </div>
                            <span class="text-sm font-medium text-gray-900">UPS</span>
                            <span class="ml-2 text-xs text-gray-500">(22 usługi)</span>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <!-- Advanced Options -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Opcje zaawansowane</h3>
            </div>
            <div class="px-6 py-4 space-y-4">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="valid_from" class="block text-sm font-medium text-gray-700">Ważny od</label>
                        <input type="date" name="valid_from" id="valid_from"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="valid_to" class="block text-sm font-medium text-gray-700">Ważny do</label>
                        <input type="date" name="valid_to" id="valid_to"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" name="auto_update_prices" id="auto_update_prices"
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="auto_update_prices" class="ml-2 text-sm text-gray-900">
                        Automatycznie aktualizuj ceny bazowe z API kurierów
                    </label>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" name="round_prices" id="round_prices" checked
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="round_prices" class="ml-2 text-sm text-gray-900">
                        Zaokrąglij ceny końcowe do najbliższych groszy
                    </label>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex justify-end space-x-3">
            <a href="{{ route('admin.settings.pricing') }}" 
               class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Anuluj
            </a>
            <button type="submit" name="action" value="save_draft"
                    class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Zapisz jako szkic
            </button>
            <button type="submit" name="action" value="create"
                    class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                Utwórz cennik
            </button>
        </div>
    </form>
</div>

<script>
// Toggle form fields based on strategy selection
document.querySelectorAll('input[name="strategy"]').forEach(radio => {
    radio.addEventListener('change', function() {
        // Hide all strategy-specific sections
        document.querySelectorAll('[id$="_section"]').forEach(section => {
            section.style.display = 'none';
        });
        
        // Show relevant section
        const selectedSection = document.getElementById(this.value + '_section');
        if (selectedSection) {
            selectedSection.style.display = 'block';
        }
    });
});
</script>
@endsection