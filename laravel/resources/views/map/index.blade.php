@extends('layouts.guest')

@section('title', 'Mapa punktów kurierskich')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NXE++JE6BHzQ2IN1qfZbH2hY1SgGZ6wQbE1C5likM=" crossorigin="" />
    <style>#map { height: 70vh; }</style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="mb-4">
        <h2 class="text-2xl font-bold text-gray-900">Mapa punktów</h2>
        <p class="text-sm text-gray-600">Dane z OpenStreetMap (OSM) + nasze punkty kurierskie</p>
    </div>

    <div class="bg-white p-4 rounded-md shadow mb-4 grid grid-cols-1 md:grid-cols-4 gap-3">
        <div>
            <label class="text-sm text-gray-600">Kurier</label>
            <select id="couriers" class="w-full border-gray-300 rounded-md" multiple>
                <option value="inpost">InPost</option>
                <option value="dhl">DHL</option>
                <option value="dpd">DPD</option>
                <option value="gls">GLS</option>
            </select>
        </div>
        <div>
            <label class="text-sm text-gray-600">Typ</label>
            <select id="types" class="w-full border-gray-300 rounded-md" multiple>
                <option value="parcel_locker">Paczkomat</option>
                <option value="pickup_point">Punkt</option>
            </select>
        </div>
        <div>
            <label class="text-sm text-gray-600">Szukaj</label>
            <input id="query" type="text" class="w-full border-gray-300 rounded-md" placeholder="Miasto / kod / nazwa">
        </div>
        <div class="flex items-end">
            <button id="reload" class="px-4 py-2 bg-blue-600 text-white rounded-md">Odśwież</button>
        </div>
    </div>

    <div id="map" class="rounded-md shadow"></div>
</div>

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    const map = L.map('map').setView([52.2297, 21.0122], 12);
    L.tileLayer(@json(config('map.tiles.url')), {
        maxZoom: @json(config('map.tiles.max_zoom')),
        attribution: @json(config('map.tiles.attribution'))
    }).addTo(map);

    const markers = L.layerGroup().addTo(map);

    async function loadPoints() {
        markers.clearLayers();
        const bounds = map.getBounds();
        const bbox = [bounds.getSouth(), bounds.getWest(), bounds.getNorth(), bounds.getEast()].join(',');
        const codes = Array.from(document.getElementById('couriers').selectedOptions).map(o => o.value);
        const types = Array.from(document.getElementById('types').selectedOptions).map(o => o.value);
        const q = document.getElementById('query').value;

        const params = new URLSearchParams({ bbox, limit: 250 });
        if (codes.length) codes.forEach(c => params.append('courier_codes[]', c));
        if (types.length) types.forEach(t => params.append('types[]', t));
        if (q) params.append('q', q);

        // NOTE: this endpoint requires X-API-Key header; for demo replace with your key or proxy via backend
        const res = await fetch(`/api/map/points?${params.toString()}`, { headers: { 'X-API-Key': '{{ env('MAP_DEMO_API_KEY', '') }}' }});
        const data = await res.json();

        (data.data || []).forEach(p => {
            const m = L.marker([p.lat, p.lng]).bindPopup(
                `<b>${p.name}</b><br>${p.address.street || ''} ${p.address.building_number || ''}<br>` +
                `${p.address.postal_code || ''} ${p.address.city || ''}<br>` +
                `<small>${p.courier?.name || ''} (${p.type})</small>`
            );
            markers.addLayer(m);
        });
    }

    map.on('moveend', loadPoints);
    document.getElementById('reload').addEventListener('click', loadPoints);
    loadPoints();
</script>
@endpush
@endsection
