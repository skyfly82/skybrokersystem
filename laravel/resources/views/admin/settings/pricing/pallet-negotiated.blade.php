@extends('layouts.admin')

@section('title', 'Cennik negocjowany - Transport paletowy')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="md:flex md:items-center md:justify-between mb-6">
        <div class="flex-1 min-w-0">
            <nav class="flex mb-4" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('admin.settings.index') }}" class="text-gray-500 hover:text-gray-700">Ustawienia</a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <a href="{{ route('admin.settings.pricing.negotiated') }}" class="ml-1 text-gray-500 hover:text-gray-700 md:ml-2">Cenniki negocjowane</a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="ml-1 text-gray-700 md:ml-2">Transport paletowy</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate flex items-center">
                <div class="h-10 w-10 bg-red-600 rounded-lg flex items-center justify-center mr-3">
                    <span class="text-white font-bold text-sm">GEO</span>
                </div>
                Cennik negocjowany - Geodis Freight
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                <span class="font-medium">Magna Logistics Sp. z o.o.</span> • Transport paletowy do UE • Wolumen: 850 palet/mies.
            </p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.settings.pricing.pallet.negotiated.store') }}">
        @csrf
        
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-4">
            <!-- Main Form -->
            <div class="lg:col-span-3 space-y-6">
                <!-- Customer Analysis -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Profil klienta i analiza wolumenu</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                            <div class="bg-blue-50 p-4 rounded-lg">
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-blue-600">850</div>
                                    <div class="text-sm text-blue-700">Palet/miesiąc</div>
                                    <div class="text-xs text-blue-600 mt-1">↗ +15% vs poprzedni</div>
                                </div>
                            </div>
                            <div class="bg-green-50 p-4 rounded-lg">
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-green-600">340 kg</div>
                                    <div class="text-sm text-green-700">Średnia waga</div>
                                    <div class="text-xs text-green-600 mt-1">Standardowa</div>
                                </div>
                            </div>
                            <div class="bg-purple-50 p-4 rounded-lg">
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-purple-600">68%</div>
                                    <div class="text-sm text-purple-700">Eksport UE</div>
                                    <div class="text-xs text-purple-600 mt-1">32% krajowy</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Route Analysis -->
                        <div class="mt-6">
                            <h4 class="text-sm font-medium text-gray-900 mb-3">Analiza tras (ostatnie 6 miesięcy)</h4>
                            <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                                <div class="text-center p-3 border border-gray-200 rounded">
                                    <div class="text-lg font-bold text-gray-900">420</div>
                                    <div class="text-xs text-gray-500">Niemcy (2A)</div>
                                </div>
                                <div class="text-center p-3 border border-gray-200 rounded">
                                    <div class="text-lg font-bold text-gray-900">180</div>
                                    <div class="text-xs text-gray-500">Francja (2B)</div>
                                </div>
                                <div class="text-center p-3 border border-gray-200 rounded">
                                    <div class="text-lg font-bold text-gray-900">150</div>
                                    <div class="text-xs text-gray-500">Czechy (2A)</div>
                                </div>
                                <div class="text-center p-3 border border-gray-200 rounded">
                                    <div class="text-lg font-bold text-gray-900">100</div>
                                    <div class="text-xs text-gray-500">Krajowy</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Negotiation Matrix -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-medium text-gray-900">Macierz negocjacyjna</h3>
                            <div class="flex items-center space-x-4">
                                <select class="border-gray-300 rounded-md text-sm">
                                    <option value="volume">Rabat za wolumen</option>
                                    <option value="fixed">Ceny stałe</option>
                                    <option value="mixed">Model mieszany</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <!-- Zone-based Pricing -->
                        <div class="space-y-6">
                            <!-- Domestic Pricing -->
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 mb-3">Transport krajowy</h4>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full">
                                        <thead>
                                            <tr class="border-b border-gray-200">
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Waga</th>
                                                <th class="px-4 py-2 text-center text-xs font-medium text-gray-500">Cena bazowa</th>
                                                <th class="px-4 py-2 text-center text-xs font-medium text-gray-500">Proponowana</th>
                                                <th class="px-4 py-2 text-center text-xs font-medium text-gray-500">Negocjowana</th>
                                                <th class="px-4 py-2 text-center text-xs font-medium text-gray-500">Marża</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                            @foreach([
                                                ['weight' => 'do 100 kg', 'base' => 65.00, 'proposed' => 58.50, 'cost' => 52.00],
                                                ['weight' => '101-300 kg', 'base' => 120.00, 'proposed' => 108.00, 'cost' => 96.00],
                                                ['weight' => '301-500 kg', 'base' => 165.00, 'proposed' => 148.50, 'cost' => 132.00],
                                                ['weight' => '501-1000 kg', 'base' => 285.00, 'proposed' => 256.50, 'cost' => 228.00]
                                            ] as $i => $row)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $row['weight'] }}</td>
                                                <td class="px-4 py-3 text-center text-sm text-gray-500">{{ $row['base'] }} zł</td>
                                                <td class="px-4 py-3 text-center text-sm text-blue-600 font-medium">{{ $row['proposed'] }} zł</td>
                                                <td class="px-4 py-3 text-center">
                                                    <input type="number" name="domestic[{{ $i }}]" value="{{ $row['proposed'] }}" step="0.01" 
                                                           class="w-20 text-center border-gray-300 rounded text-sm negotiated-price" 
                                                           data-cost="{{ $row['cost'] }}">
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    <span class="margin-display text-sm font-medium text-green-600">
                                                        {{ number_format((($row['proposed'] - $row['cost']) / $row['proposed']) * 100, 1) }}%
                                                    </span>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Export Pricing -->
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 mb-3">Eksport UE - Główne trasy</h4>
                                <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                                    <!-- Germany -->
                                    <div class="border border-gray-200 rounded-lg p-4">
                                        <h5 class="text-sm font-medium text-gray-900 mb-3 flex items-center">
                                            <div class="h-5 w-5 bg-blue-500 rounded flex items-center justify-center mr-2">
                                                <span class="text-white text-xs">DE</span>
                                            </div>
                                            Niemcy (420 palet/mies.)
                                        </h5>
                                        <div class="space-y-2">
                                            @foreach([
                                                ['weight' => 'do 300kg', 'base' => 185.00, 'proposed' => 165.00, 'cost' => 148.00],
                                                ['weight' => '301-500kg', 'base' => 245.00, 'proposed' => 220.00, 'cost' => 196.00],
                                                ['weight' => '501kg+', 'base' => 385.00, 'proposed' => 345.00, 'cost' => 308.00]
                                            ] as $i => $row)
                                            <div class="grid grid-cols-4 gap-2 items-center text-sm">
                                                <span class="text-gray-600">{{ $row['weight'] }}</span>
                                                <span class="text-center text-gray-500">{{ $row['base'] }}</span>
                                                <input type="number" name="export[de][{{ $i }}]" value="{{ $row['proposed'] }}" step="0.01" 
                                                       class="w-full text-center border-gray-300 rounded text-sm negotiated-price" 
                                                       data-cost="{{ $row['cost'] }}">
                                                <span class="text-center margin-display text-green-600 font-medium">
                                                    {{ number_format((($row['proposed'] - $row['cost']) / $row['proposed']) * 100, 1) }}%
                                                </span>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <!-- France -->
                                    <div class="border border-gray-200 rounded-lg p-4">
                                        <h5 class="text-sm font-medium text-gray-900 mb-3 flex items-center">
                                            <div class="h-5 w-5 bg-green-500 rounded flex items-center justify-center mr-2">
                                                <span class="text-white text-xs">FR</span>
                                            </div>
                                            Francja (180 palet/mies.)
                                        </h5>
                                        <div class="space-y-2">
                                            @foreach([
                                                ['weight' => 'do 300kg', 'base' => 205.00, 'proposed' => 185.00, 'cost' => 164.00],
                                                ['weight' => '301-500kg', 'base' => 275.00, 'proposed' => 250.00, 'cost' => 220.00],
                                                ['weight' => '501kg+', 'base' => 425.00, 'proposed' => 385.00, 'cost' => 340.00]
                                            ] as $i => $row)
                                            <div class="grid grid-cols-4 gap-2 items-center text-sm">
                                                <span class="text-gray-600">{{ $row['weight'] }}</span>
                                                <span class="text-center text-gray-500">{{ $row['base'] }}</span>
                                                <input type="number" name="export[fr][{{ $i }}]" value="{{ $row['proposed'] }}" step="0.01" 
                                                       class="w-full text-center border-gray-300 rounded text-sm negotiated-price" 
                                                       data-cost="{{ $row['cost'] }}">
                                                <span class="text-center margin-display text-green-600 font-medium">
                                                    {{ number_format((($row['proposed'] - $row['cost']) / $row['proposed']) * 100, 1) }}%
                                                </span>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Volume Incentives -->
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                <h4 class="text-sm font-medium text-yellow-800 mb-3">Bonusy za wolumen</h4>
                                <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                                    <div class="flex items-center">
                                        <input type="checkbox" name="volume_bonus[1000]" value="2" checked class="h-4 w-4 text-yellow-600">
                                        <label class="ml-2 text-sm text-yellow-700">1000+ palet/mies. = -2%</label>
                                    </div>
                                    <div class="flex items-center">
                                        <input type="checkbox" name="volume_bonus[1500]" value="5" class="h-4 w-4 text-yellow-600">
                                        <label class="ml-2 text-sm text-yellow-700">1500+ palet/mies. = -5%</label>
                                    </div>
                                    <div class="flex items-center">
                                        <input type="checkbox" name="volume_bonus[2000]" value="8" class="h-4 w-4 text-yellow-600">
                                        <label class="ml-2 text-sm text-yellow-700">2000+ palet/mies. = -8%</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Special Conditions -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Warunki specjalne i dopłaty</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <!-- Surcharges Negotiation -->
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 mb-3">Negocjowane dopłaty</h4>
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                                        <div>
                                            <div class="text-sm font-medium">Opłata paliwowa</div>
                                            <div class="text-xs text-gray-500">Standardowo: 18.5%</div>
                                        </div>
                                        <div class="flex items-center">
                                            <input type="number" name="fuel_surcharge" value="15.0" step="0.1" 
                                                   class="w-16 text-center border-gray-300 rounded text-sm mr-2">
                                            <span class="text-sm text-gray-500">%</span>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                                        <div>
                                            <div class="text-sm font-medium">Handling</div>
                                            <div class="text-xs text-gray-500">Standardowo: 25 zł</div>
                                        </div>
                                        <div class="flex items-center">
                                            <input type="number" name="handling_fee" value="20.00" step="0.01" 
                                                   class="w-16 text-center border-gray-300 rounded text-sm mr-2">
                                            <span class="text-sm text-gray-500">zł</span>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                                        <div>
                                            <div class="text-sm font-medium">Odbiór/Dostawa</div>
                                            <div class="text-xs text-gray-500">Standardowo: 45 zł każda</div>
                                        </div>
                                        <div class="flex items-center">
                                            <input type="number" name="pickup_delivery_fee" value="35.00" step="0.01" 
                                                   class="w-16 text-center border-gray-300 rounded text-sm mr-2">
                                            <span class="text-sm text-gray-500">zł</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Payment & Contract Terms -->
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 mb-3">Warunki umowy</h4>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Warunki płatności</label>
                                        <select name="payment_terms" class="mt-1 block w-full border-gray-300 rounded-md text-sm">
                                            <option value="14_days">14 dni</option>
                                            <option value="21_days" selected>21 dni</option>
                                            <option value="30_days">30 dni</option>
                                            <option value="45_days">45 dni</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Minimalne zobowiązanie</label>
                                        <div class="mt-1 flex items-center">
                                            <input type="number" name="minimum_commitment" value="800" 
                                                   class="block w-full border-gray-300 rounded-l-md text-sm">
                                            <span class="px-3 py-2 bg-gray-50 border border-l-0 border-gray-300 rounded-r-md text-sm">palet/mies.</span>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Okres umowy</label>
                                        <select name="contract_period" class="mt-1 block w-full border-gray-300 rounded-md text-sm">
                                            <option value="6_months">6 miesięcy</option>
                                            <option value="12_months" selected>12 miesięcy</option>
                                            <option value="24_months">24 miesiące</option>
                                        </select>
                                    </div>

                                    <div class="flex items-center">
                                        <input type="checkbox" name="fuel_adjustment_clause" checked class="h-4 w-4 text-red-600">
                                        <label class="ml-2 text-sm text-gray-700">Klauzula dostosowania paliwowego</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar - Financial Analysis -->
            <div class="lg:col-span-1">
                <div class="space-y-6">
                    <!-- Profitability Overview -->
                    <div class="bg-white shadow rounded-lg p-6 sticky top-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Analiza rentowności</h3>
                        
                        <!-- Key Metrics -->
                        <div class="space-y-4">
                            <div class="bg-green-50 p-3 rounded-lg">
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-green-600" id="avg-margin">11.8%</div>
                                    <div class="text-sm text-green-700">Średnia marża</div>
                                </div>
                            </div>
                            
                            <div class="space-y-3">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Przewidywany przychód:</span>
                                    <span class="font-medium" id="projected-revenue">164,500 zł/mies.</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Koszt operacyjny:</span>
                                    <span class="font-medium" id="operational-cost">145,200 zł/mies.</span>
                                </div>
                                <div class="flex justify-between text-sm font-medium border-t pt-3">
                                    <span class="text-gray-900">Zysk brutto:</span>
                                    <span class="text-green-600" id="gross-profit">19,300 zł/mies.</span>
                                </div>
                            </div>
                        </div>

                        <!-- Risk Assessment -->
                        <div class="mt-6 p-3 bg-blue-50 rounded-lg">
                            <h4 class="text-sm font-medium text-blue-800 mb-2">Ocena ryzyka</h4>
                            <div class="space-y-2 text-xs">
                                <div class="flex items-center">
                                    <div class="h-2 w-2 bg-green-400 rounded-full mr-2"></div>
                                    <span class="text-blue-700">Marża powyżej 10%</span>
                                </div>
                                <div class="flex items-center">
                                    <div class="h-2 w-2 bg-green-400 rounded-full mr-2"></div>
                                    <span class="text-blue-700">Stabilny klient (2 lata)</span>
                                </div>
                                <div class="flex items-center">
                                    <div class="h-2 w-2 bg-yellow-400 rounded-full mr-2"></div>
                                    <span class="text-blue-700">Płatności 21 dni</span>
                                </div>
                                <div class="flex items-center">
                                    <div class="h-2 w-2 bg-green-400 rounded-full mr-2"></div>
                                    <span class="text-blue-700">Wysokie wolumeny</span>
                                </div>
                            </div>
                        </div>

                        <!-- Comparison -->
                        <div class="mt-6">
                            <h4 class="text-sm font-medium text-gray-900 mb-3">Porównanie z konkurencją</h4>
                            <div class="space-y-2 text-xs">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">DHL:</span>
                                    <span class="text-red-600">+8% drożej</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">DB Schenker:</span>
                                    <span class="text-green-600">-3% taniej</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">DSV:</span>
                                    <span class="text-yellow-600">Podobnie</span>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <div class="flex flex-col space-y-3">
                                <button type="submit" name="action" value="save_draft" 
                                        class="w-full px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                    Zapisz szkic
                                </button>
                                <button type="submit" name="action" value="send_offer" 
                                        class="w-full px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-red-600 hover:bg-red-700">
                                    Wyślij ofertę
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
// Real-time margin calculation
function updateMargins() {
    let totalRevenue = 0;
    let totalCost = 0;
    
    document.querySelectorAll('.negotiated-price').forEach(input => {
        const price = parseFloat(input.value) || 0;
        const cost = parseFloat(input.getAttribute('data-cost')) || 0;
        const marginSpan = input.closest('tr').querySelector('.margin-display');
        
        if (price > 0 && cost > 0) {
            const margin = ((price - cost) / price * 100);
            marginSpan.textContent = margin.toFixed(1) + '%';
            marginSpan.className = 'margin-display text-sm font-medium ' + 
                (margin < 8 ? 'text-red-600' : margin < 12 ? 'text-yellow-600' : 'text-green-600');
            
            // Estimate volumes for calculation (simplified)
            totalRevenue += price * 100; // Assume 100 units per price tier
            totalCost += cost * 100;
        }
    });
    
    const avgMargin = totalRevenue > 0 ? ((totalRevenue - totalCost) / totalRevenue * 100) : 0;
    document.getElementById('avg-margin').textContent = avgMargin.toFixed(1) + '%';
    
    document.getElementById('projected-revenue').textContent = (totalRevenue * 0.2).toLocaleString() + ' zł/mies.';
    document.getElementById('operational-cost').textContent = (totalCost * 0.2).toLocaleString() + ' zł/mies.';
    document.getElementById('gross-profit').textContent = ((totalRevenue - totalCost) * 0.2).toLocaleString() + ' zł/mies.';
}

// Listen for price changes
document.querySelectorAll('.negotiated-price').forEach(input => {
    input.addEventListener('input', updateMargins);
});

// Initialize calculations
updateMargins();
</script>
@endsection