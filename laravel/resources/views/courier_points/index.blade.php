@extends('layouts.admin')

@section('title', 'Punkty kurierskie')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="md:flex md:items-center md:justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Punkty kurierskie</h2>
            <p class="text-sm text-gray-600">Zarządzanie punktami OSM/kurierów</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.courier-points.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md">Dodaj punkt</a>
        </div>
    </div>

    <div class="bg-white p-4 rounded-md shadow mb-4">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-3">
            <div>
                <label class="text-sm text-gray-600">Kurier</label>
                <select name="courier" class="w-full border-gray-300 rounded-md">
                    <option value="">Wszyscy</option>
                    @foreach($couriers as $c)
                        <option value="{{ $c->id }}" @selected(request('courier')==$c->id)>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-sm text-gray-600">Typ</label>
                <select name="type" class="w-full border-gray-300 rounded-md">
                    <option value="">Wszystkie</option>
                    <option value="parcel_locker" @selected(request('type')==='parcel_locker')>Paczkomat</option>
                    <option value="pickup_point" @selected(request('type')==='pickup_point')>Punkt</option>
                    <option value="depot" @selected(request('type')==='depot')>Magazyn</option>
                    <option value="branch" @selected(request('type')==='branch')>Oddział</option>
                </select>
            </div>
            <div>
                <label class="text-sm text-gray-600">Miasto</label>
                <input type="text" name="city" value="{{ request('city') }}" class="w-full border-gray-300 rounded-md" />
            </div>
            <div>
                <label class="text-sm text-gray-600">Aktywne</label>
                <select name="active" class="w-full border-gray-300 rounded-md">
                    <option value="">Wszystkie</option>
                    <option value="1" @selected(request('active')==='1')>Tak</option>
                    <option value="0" @selected(request('active')==='0')>Nie</option>
                </select>
            </div>
            <div class="flex items-end">
                <button class="px-4 py-2 bg-gray-100 rounded-md">Filtruj</button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-md shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kod</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nazwa</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kurier</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Typ</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Adres</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aktywny</th>
                    <th class="px-4 py-3" />
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($points as $p)
                    <tr>
                        <td class="px-4 py-2 font-mono">{{ $p->code }}</td>
                        <td class="px-4 py-2">{{ $p->name }}</td>
                        <td class="px-4 py-2">{{ $p->courierService?->name }}</td>
                        <td class="px-4 py-2">{{ $p->type }}</td>
                        <td class="px-4 py-2 text-sm text-gray-600">{{ $p->street }} {{ $p->building_number }}, {{ $p->postal_code }} {{ $p->city }}</td>
                        <td class="px-4 py-2">
                            <span class="px-2 py-1 text-xs rounded {{ $p->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">{{ $p->is_active ? 'Tak' : 'Nie' }}</span>
                        </td>
                        <td class="px-4 py-2 text-right">
                            <a href="{{ route('admin.courier-points.edit', $p) }}" class="text-blue-600">Edytuj</a>
                            <form action="{{ route('admin.courier-points.destroy', $p) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-600 ml-3" onclick="return confirm('Usunąć punkt?')">Usuń</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-6 text-center text-gray-500">Brak punktów</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3 border-t">{{ $points->appends(request()->query())->links() }}</div>
    </div>
</div>
@endsection

