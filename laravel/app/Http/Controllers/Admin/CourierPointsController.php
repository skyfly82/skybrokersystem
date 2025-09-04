<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourierPoint;
use App\Models\CourierService;
use Illuminate\Http\Request;

class CourierPointsController extends Controller
{
    public function index(Request $request)
    {

        $query = CourierPoint::query()->with('courierService')
            ->when($request->courier, fn ($q, $c) => $q->forCourier($c))
            ->when($request->type, fn ($q, $t) => $q->where('type', $t))
            ->when($request->city, fn ($q, $city) => $q->where('city', 'like', "%{$city}%"))
            ->when($request->active, fn ($q, $a) => $q->where('is_active', (bool) $a));

        $points = $query->latest()->paginate(20);
        $couriers = CourierService::orderBy('name')->get();

        return view('courier_points.index', compact('points', 'couriers'));
    }

    public function create()
    {
        $couriers = CourierService::orderBy('name')->get();

        return view('courier_points.create', compact('couriers'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateData($request);
        $validated['uuid'] = \Illuminate\Support\Str::uuid();
        CourierPoint::create($validated);

        return redirect()->route('admin.courier-points.index')->with('success', 'Punkt został dodany.');
    }

    public function edit(CourierPoint $courierPoint)
    {
        $couriers = CourierService::orderBy('name')->get();

        return view('courier_points.create', ['courierPoint' => $courierPoint, 'couriers' => $couriers]);
    }

    public function update(Request $request, CourierPoint $courierPoint)
    {
        $validated = $this->validateData($request, $courierPoint->id);
        $courierPoint->update($validated);

        return redirect()->route('admin.courier-points.index')->with('success', 'Punkt został zaktualizowany.');
    }

    public function destroy(CourierPoint $courierPoint)
    {
        $courierPoint->delete();

        return back()->with('success', 'Punkt został usunięty.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
            'courier_service_id' => 'required|exists:courier_services,id',
            'type' => 'required|string|max:50',
            'delimiter' => 'sometimes|string|in:;,\t,|,',
            'has_header' => 'sometimes|boolean',
        ]);

        // Defer to artisan command for actual heavy lifting
        // Here we can queue a job or call command synchronously
        \Artisan::call('points:import', [
            'path' => $request->file('file')->getRealPath(),
            '--courier' => (string) $request->courier_service_id,
            '--type' => $request->type,
            '--delimiter' => $request->get('delimiter', ';'),
            '--header' => $request->boolean('has_header', true),
        ]);

        return back()->with('success', 'Import punktów został uruchomiony.');
    }

    private function validateData(Request $request, ?int $ignoreId = null): array
    {
        $unique = 'unique:courier_points,code';
        if ($ignoreId) {
            $unique .= ','.$ignoreId;
        }

        return $request->validate([
            'courier_service_id' => 'required|exists:courier_services,id',
            'code' => ['required', 'string', 'max:50', $unique],
            'type' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'street' => 'nullable|string|max:255',
            'building_number' => 'nullable|string|max:50',
            'apartment_number' => 'nullable|string|max:50',
            'city' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:12',
            'country_code' => 'nullable|string|size:2',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'opening_hours' => 'nullable|array',
            'functions' => 'nullable|array',
            'is_active' => 'sometimes|boolean',
            'metadata' => 'nullable|array',
            'external_id' => 'nullable|string|max:100',
        ]);
    }
}
