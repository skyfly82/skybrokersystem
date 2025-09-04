@extends('layouts.admin')

@section('title', 'API Settings')

@section('content')
<div class="max-w-6xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Klucze API (Mapa)</h2>
        <p class="text-sm text-gray-600">Generuj i zarządzaj kluczami API do endpointów mapy.</p>
    </div>

    @if(session('generated_key'))
        <div class="bg-green-50 border border-green-200 text-green-800 p-4 rounded mb-4">
            <div class="font-semibold mb-1">Nowy klucz API został wygenerowany:</div>
            <div class="font-mono break-all">{{ session('generated_key') }}</div>
            <div class="text-xs text-green-700 mt-2">Skopiuj klucz teraz — z powodów bezpieczeństwa nie będzie ponownie wyświetlany.</div>
        </div>
    @endif

    <div class="bg-white p-4 rounded shadow mb-6">
        <form method="POST" action="{{ route('admin.settings.api.generate') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @csrf
            <div>
                <label class="text-sm text-gray-600">Etykieta (opcjonalnie)</label>
                <input type="text" name="label" class="w-full border-gray-300 rounded" placeholder="np. Front mapy" />
            </div>
            <div>
                <label class="text-sm text-gray-600">Scope</label>
                <select name="scope" class="w-full border-gray-300 rounded">
                    <option value="map.read" selected>map.read</option>
                </select>
            </div>
            <div class="flex items-end">
                <button class="px-4 py-2 bg-blue-600 text-white rounded">Wygeneruj klucz</button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Etykieta</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Klucz</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Scope</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Status</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Utworzono</th>
                    <th class="px-4 py-2" />
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($keys as $k)
                    <tr>
                        <td class="px-4 py-2">{{ $k->name ?: '-' }}</td>
                        <td class="px-4 py-2 font-mono text-xs break-all">{{ $k->key }}</td>
                        <td class="px-4 py-2 text-xs">{{ implode(',', $k->scopes ?? []) }}</td>
                        <td class="px-4 py-2">
                            <span class="px-2 py-1 text-xs rounded {{ $k->status==='active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">{{ $k->status }}</span>
                        </td>
                        <td class="px-4 py-2 text-sm text-gray-600">{{ $k->created_at?->format('Y-m-d H:i') }}</td>
                        <td class="px-4 py-2 text-right">
                            @if($k->status !== 'revoked')
                                <form method="POST" action="{{ route('admin.settings.api.keys.revoke', $k) }}" onsubmit="return confirm('Unieważnić ten klucz?')" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button class="text-red-600">Unieważnij</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-6 text-center text-gray-500">Brak kluczy API</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3 border-t">{{ $keys->links() }}</div>
    </div>
</div>
@endsection

