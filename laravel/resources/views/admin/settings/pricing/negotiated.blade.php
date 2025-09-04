@extends('layouts.admin')

@section('title', 'Cenniki negocjowane')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="md:flex md:items-center md:justify-between mb-6">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                Cenniki negocjowane
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Indywidualne umowy cenowe z klientami i analiza rentowności
            </p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3">
            <button type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                Raport rentowności
            </button>
            <a href="{{ route('admin.settings.pricing.negotiated.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Nowy cennik
            </a>
        </div>
    </div>

    <!-- Alert dla niskiej rentowności -->
    <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">Ostrzeżenie o rentowności</h3>
                <div class="mt-2 text-sm text-red-700">
                    <p>3 klientów ma cenniki z marżą poniżej 5%. Sprawdź szczegóły w tabeli poniżej.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-8">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="h-8 w-8 bg-blue-500 rounded flex items-center justify-center">
                            <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Aktywne cenniki</dt>
                            <dd class="text-lg font-medium text-gray-900">24</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="h-8 w-8 bg-green-500 rounded flex items-center justify-center">
                            <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Średnia marża</dt>
                            <dd class="text-lg font-medium text-gray-900">12.4%</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="h-8 w-8 bg-yellow-500 rounded flex items-center justify-center">
                            <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Przychód/miesiąc</dt>
                            <dd class="text-lg font-medium text-gray-900">284,500 zł</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="h-8 w-8 bg-red-500 rounded flex items-center justify-center">
                            <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.982 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Zagrożone marżą</dt>
                            <dd class="text-lg font-medium text-gray-900">3 klientów</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Negotiated Pricing Table -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">Aktywne cenniki negocjowane</h3>
                <div class="flex items-center space-x-4">
                    <select class="border-gray-300 rounded-md shadow-sm text-sm">
                        <option value="">Wszystkie segmenty</option>
                        <option value="premium">Premium (>1000 paczek)</option>
                        <option value="corporate">Corporate (500-1000)</option>
                        <option value="sme">SME (<500)</option>
                    </select>
                    <select class="border-gray-300 rounded-md shadow-sm text-sm">
                        <option value="">Sortuj według</option>
                        <option value="margin_asc">Marża rosnąco</option>
                        <option value="margin_desc">Marża malejąco</option>
                        <option value="revenue_desc">Przychód malejąco</option>
                        <option value="volume_desc">Wolumen malejąco</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Klient
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Segment
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Wolumen/mies.
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Średnia marża
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Przychód/mies.
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status cennika
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Ważność do
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Akcje
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <!-- Premium Client -->
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 bg-blue-500 rounded-full flex items-center justify-center">
                                        <span class="text-white font-bold text-sm">AL</span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">Allegro Sp. z o.o.</div>
                                    <div class="text-sm text-gray-500">ID: 1001 • Klient od 2022</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                Premium
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div>2,840 paczek</div>
                            <div class="text-xs text-green-600">↗ +12% vs poprzedni</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <div class="flex items-center">
                                <span class="text-green-600 font-medium">15.8%</span>
                                <div class="ml-2 w-16 bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-500 h-2 rounded-full" style="width: 79%"></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div>52,420 zł</div>
                            <div class="text-xs text-gray-500">Koszt: 44,140 zł</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Aktywny
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            31.12.2024
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="#" class="text-blue-600 hover:text-blue-900">Edytuj</a>
                                <a href="#" class="text-green-600 hover:text-green-900">Raport</a>
                                <a href="#" class="text-gray-600 hover:text-gray-900">Analiza</a>
                            </div>
                        </td>
                    </tr>

                    <!-- Corporate Client with Low Margin Warning -->
                    <tr class="hover:bg-gray-50 bg-red-25">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 bg-orange-500 rounded-full flex items-center justify-center">
                                        <span class="text-white font-bold text-sm">EM</span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        E-Merchant Solutions
                                        <svg class="inline ml-1 h-4 w-4 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="text-sm text-gray-500">ID: 1045 • Klient od 2023</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                Corporate
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div>1,245 paczek</div>
                            <div class="text-xs text-red-600">↘ -8% vs poprzedni</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <div class="flex items-center">
                                <span class="text-red-600 font-medium">3.2%</span>
                                <div class="ml-2 w-16 bg-gray-200 rounded-full h-2">
                                    <div class="bg-red-500 h-2 rounded-full" style="width: 16%"></div>
                                </div>
                            </div>
                            <div class="text-xs text-red-600">Poniżej minimum!</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div>23,180 zł</div>
                            <div class="text-xs text-gray-500">Koszt: 22,440 zł</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                Do negocjacji
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            30.06.2024
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="#" class="text-red-600 hover:text-red-900 font-medium">Renegocjuj</a>
                                <a href="#" class="text-green-600 hover:text-green-900">Raport</a>
                                <a href="#" class="text-gray-600 hover:text-gray-900">Analiza</a>
                            </div>
                        </td>
                    </tr>

                    <!-- SME Client -->
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 bg-green-500 rounded-full flex items-center justify-center">
                                        <span class="text-white font-bold text-sm">BS</span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">BestShop Online</div>
                                    <div class="text-sm text-gray-500">ID: 1078 • Klient od 2024</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                SME
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div>420 paczek</div>
                            <div class="text-xs text-green-600">↗ +25% vs poprzedni</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <div class="flex items-center">
                                <span class="text-yellow-600 font-medium">8.5%</span>
                                <div class="ml-2 w-16 bg-gray-200 rounded-full h-2">
                                    <div class="bg-yellow-500 h-2 rounded-full" style="width: 42%"></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div>7,980 zł</div>
                            <div class="text-xs text-gray-500">Koszt: 7,300 zł</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Aktywny
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            15.08.2025
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="#" class="text-blue-600 hover:text-blue-900">Edytuj</a>
                                <a href="#" class="text-green-600 hover:text-green-900">Raport</a>
                                <a href="#" class="text-gray-600 hover:text-gray-900">Analiza</a>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="bg-white px-6 py-3 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Wyświetlane <span class="font-medium">1</span> do <span class="font-medium">3</span> z <span class="font-medium">24</span> cenników
                </div>
                <nav class="flex space-x-2">
                    <button class="px-3 py-1 text-sm text-gray-500 border border-gray-300 rounded hover:bg-gray-50">Poprzednia</button>
                    <button class="px-3 py-1 text-sm text-white bg-blue-600 border border-blue-600 rounded">1</button>
                    <button class="px-3 py-1 text-sm text-gray-500 border border-gray-300 rounded hover:bg-gray-50">2</button>
                    <button class="px-3 py-1 text-sm text-gray-500 border border-gray-300 rounded hover:bg-gray-50">Następna</button>
                </nav>
            </div>
        </div>
    </div>

    <!-- Profitability Analysis Panel -->
    <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Profitability Trends -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Trendy rentowności</h3>
                <p class="mt-1 text-sm text-gray-500">Analiza marż w czasie</p>
            </div>
            <div class="p-6">
                <div class="h-64 bg-gray-100 rounded-lg flex items-center justify-center">
                    <div class="text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Wykres rentowności</h3>
                        <p class="mt-1 text-sm text-gray-500">Integracja z Chart.js</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Risk Analysis -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Analiza ryzyka</h3>
                <p class="mt-1 text-sm text-gray-500">Klienci wymagający uwagi</p>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <!-- High Risk -->
                    <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                        <div>
                            <div class="text-sm font-medium text-red-900">Wysokie ryzyko</div>
                            <div class="text-xs text-red-700">Marża < 5%</div>
                        </div>
                        <div class="text-right">
                            <div class="text-lg font-bold text-red-600">3</div>
                            <div class="text-xs text-red-600">klientów</div>
                        </div>
                    </div>

                    <!-- Medium Risk -->
                    <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                        <div>
                            <div class="text-sm font-medium text-yellow-900">Średnie ryzyko</div>
                            <div class="text-xs text-yellow-700">Marża 5-10%</div>
                        </div>
                        <div class="text-right">
                            <div class="text-lg font-bold text-yellow-600">7</div>
                            <div class="text-xs text-yellow-600">klientów</div>
                        </div>
                    </div>

                    <!-- Low Risk -->
                    <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                        <div>
                            <div class="text-sm font-medium text-green-900">Niskie ryzyko</div>
                            <div class="text-xs text-green-700">Marża > 10%</div>
                        </div>
                        <div class="text-right">
                            <div class="text-lg font-bold text-green-600">14</div>
                            <div class="text-xs text-green-600">klientów</div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-gray-200">
                    <h4 class="text-sm font-medium text-gray-900 mb-3">Rekomendacje</h4>
                    <ul class="text-sm text-gray-600 space-y-2">
                        <li>• Renegocjuj cenniki z marżą poniżej 5%</li>
                        <li>• Sprawdź koszty operacyjne dla średniego ryzyka</li>
                        <li>• Rozważ bonusy za wolumen dla top klientów</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection