<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CourierPoint;
use App\Models\CourierService;
use Illuminate\Http\Request;

class MapController extends Controller
{
    public function points(Request $request)
    {
        $query = CourierPoint::query()->with(['courierService:id,code,name']);

        // Filters
        if ($request->filled('courier_codes')) {
            $codes = is_array($request->courier_codes) ? $request->courier_codes : explode(',', (string)$request->courier_codes);
            $query->whereHas('courierService', fn ($q) => $q->whereIn('code', $codes));
        }

        if ($request->filled('courier_ids')) {
            $ids = is_array($request->courier_ids) ? $request->courier_ids : explode(',', (string)$request->courier_ids);
            $query->whereIn('courier_service_id', $ids);
        }

        if ($request->filled('types')) {
            $types = is_array($request->types) ? $request->types : explode(',', (string)$request->types);
            $query->whereIn('type', $types);
        }

        if ($request->filled('bbox')) {
            // bbox=south,west,north,east
            [$south, $west, $north, $east] = array_map('floatval', explode(',', (string) $request->bbox));
            $query->withinBBox($south, $west, $north, $east);
        }

        if ($request->filled('q')) {
            $q = (string)$request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('city', 'like', "%{$q}%")
                    ->orWhere('postal_code', 'like', "%{$q}%")
                    ->orWhere('code', 'like', "%{$q}%");
            });
        }

        $limit = min((int) $request->get('limit', config('map.api.default_limit')), config('map.api.max_limit'));
        $points = $query->active()->limit($limit)->get();

        if ($request->get('format') === 'geojson') {
            return response()->json($this->toGeoJson($points));
        }

        return response()->json([
            'success' => true,
            'data' => $points->map(fn ($p) => [
                'id' => $p->id,
                'uuid' => $p->uuid,
                'code' => $p->code,
                'type' => $p->type,
                'name' => $p->name,
                'address' => [
                    'street' => $p->street,
                    'building_number' => $p->building_number,
                    'apartment_number' => $p->apartment_number,
                    'city' => $p->city,
                    'postal_code' => $p->postal_code,
                    'country_code' => $p->country_code,
                ],
                'lat' => $p->latitude,
                'lng' => $p->longitude,
                'functions' => $p->functions ?? [],
                'opening_hours' => $p->opening_hours ?? null,
                'courier' => [
                    'id' => $p->courier_service_id,
                    'code' => $p->courierService?->code,
                    'name' => $p->courierService?->name,
                ],
            ]),
            'meta' => [
                'count' => $points->count(),
                'limit' => $limit,
            ]
        ]);
    }

    public function show(string $idOrCode)
    {
        $point = CourierPoint::with('courierService')
            ->when(is_numeric($idOrCode), fn ($q) => $q->where('id', (int)$idOrCode), fn ($q) => $q->where('code', $idOrCode))
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $point,
        ]);
    }

    private function toGeoJson($points): array
    {
        return [
            'type' => 'FeatureCollection',
            'features' => $points->map(function ($p) {
                return [
                    'type' => 'Feature',
                    'geometry' => [
                        'type' => 'Point',
                        'coordinates' => [(float)$p->longitude, (float)$p->latitude],
                    ],
                    'properties' => [
                        'id' => $p->id,
                        'code' => $p->code,
                        'type' => $p->type,
                        'name' => $p->name,
                        'city' => $p->city,
                        'postal_code' => $p->postal_code,
                        'courier' => $p->courierService?->code,
                    ],
                ];
            })->toArray(),
        ];
    }
}

