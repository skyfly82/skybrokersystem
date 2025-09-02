@extends('layouts.admin')

@section('title', (isset($courierPoint) ? 'Edytuj' : 'Dodaj') . ' punkt kurierski')

@section('content')
<div class="max-w-3xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="bg-white p-6 rounded-md shadow">
        <h2 class="text-xl font-bold mb-4">{{ isset($courierPoint) ? 'Edytuj punkt' : 'Dodaj punkt' }}</h2>

        <form method="POST" action="{{ isset($courierPoint) ? route('admin.courier-points.update', $courierPoint) : route('admin.courier-points.store') }}">
            @csrf
            @if(isset($courierPoint))
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm text-gray-600">Kurier</label>
                    <select name="courier_service_id" class="w-full border-gray-300 rounded-md" required>
                        @foreach($couriers as $c)
                            <option value="{{ $c->id }}" @selected(old('courier_service_id', $courierPoint->courier_service_id ?? '')==$c->id)>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-sm text-gray-600">Kod punktu</label>
                    <input type="text" name="code" value="{{ old('code', $courierPoint->code ?? '') }}" class="w-full border-gray-300 rounded-md" required />
                </div>
                <div>
                    <label class="text-sm text-gray-600">Typ</label>
                    <select name="type" class="w-full border-gray-300 rounded-md" required>
                        @php($typeVal = old('type', $courierPoint->type ?? 'parcel_locker'))
                        <option value="parcel_locker" @selected($typeVal==='parcel_locker')>Paczkomat</option>
                        <option value="pickup_point" @selected($typeVal==='pickup_point')>Punkt</option>
                        <option value="depot" @selected($typeVal==='depot')>Magazyn</option>
                        <option value="branch" @selected($typeVal==='branch')>Oddział</option>
                    </select>
                </div>
                <div>
                    <label class="text-sm text-gray-600">Nazwa</label>
                    <input type="text" name="name" value="{{ old('name', $courierPoint->name ?? '') }}" class="w-full border-gray-300 rounded-md" required />
                </div>
                <div class="md:col-span-2">
                    <label class="text-sm text-gray-600">Opis</label>
                    <input type="text" name="description" value="{{ old('description', $courierPoint->description ?? '') }}" class="w-full border-gray-300 rounded-md" />
                </div>
                <div>
                    <label class="text-sm text-gray-600">Ulica</label>
                    <input type="text" name="street" value="{{ old('street', $courierPoint->street ?? '') }}" class="w-full border-gray-300 rounded-md" />
                </div>
                <div>
                    <label class="text-sm text-gray-600">Nr budynku</label>
                    <input type="text" name="building_number" value="{{ old('building_number', $courierPoint->building_number ?? '') }}" class="w-full border-gray-300 rounded-md" />
                </div>
                <div>
                    <label class="text-sm text-gray-600">Nr lokalu</label>
                    <input type="text" name="apartment_number" value="{{ old('apartment_number', $courierPoint->apartment_number ?? '') }}" class="w-full border-gray-300 rounded-md" />
                </div>
                <div>
                    <label class="text-sm text-gray-600">Miasto</label>
                    <input type="text" name="city" value="{{ old('city', $courierPoint->city ?? '') }}" class="w-full border-gray-300 rounded-md" />
                </div>
                <div>
                    <label class="text-sm text-gray-600">Kod pocztowy</label>
                    <input type="text" name="postal_code" value="{{ old('postal_code', $courierPoint->postal_code ?? '') }}" class="w-full border-gray-300 rounded-md" />
                </div>
                <div>
                    <label class="text-sm text-gray-600">Kraj</label>
                    <input type="text" name="country_code" value="{{ old('country_code', $courierPoint->country_code ?? 'PL') }}" class="w-full border-gray-300 rounded-md" />
                </div>
                <div>
                    <label class="text-sm text-gray-600">Szer. geo (lat)</label>
                    <input type="number" step="0.0000001" name="latitude" value="{{ old('latitude', $courierPoint->latitude ?? '') }}" class="w-full border-gray-300 rounded-md" required />
                </div>
                <div>
                    <label class="text-sm text-gray-600">Dł. geo (lng)</label>
                    <input type="number" step="0.0000001" name="longitude" value="{{ old('longitude', $courierPoint->longitude ?? '') }}" class="w-full border-gray-300 rounded-md" required />
                </div>
                <div class="md:col-span-2">
                    <label class="text-sm text-gray-600">Aktywny</label>
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $courierPoint->is_active ?? true)) />
                </div>
            </div>

            <div class="mt-6 flex gap-2">
                <button class="px-4 py-2 bg-blue-600 text-white rounded-md">Zapisz</button>
                <a href="{{ route('admin.courier-points.index') }}" class="px-4 py-2 bg-gray-100 rounded-md">Anuluj</a>
            </div>
        </form>
    </div>
</div>
@endsection

