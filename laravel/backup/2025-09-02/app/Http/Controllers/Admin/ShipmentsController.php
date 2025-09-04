<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourierService;
use App\Models\Customer;
use App\Models\Shipment;
use Illuminate\Http\Request;

class ShipmentsController extends Controller
{
    public function index(Request $request)
    {
        $query = Shipment::with(['customer', 'customerUser', 'courierService'])
            ->when($request->search, function ($query, $search) {
                return $query->where('tracking_number', 'like', "%{$search}%")
                    ->orWhere('reference_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($q) use ($search) {
                        $q->where('company_name', 'like', "%{$search}%");
                    });
            })
            ->when($request->status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when($request->courier, function ($query, $courier) {
                return $query->whereHas('courierService', function ($q) use ($courier) {
                    $q->where('code', $courier);
                });
            })
            ->when($request->customer, function ($query, $customer) {
                return $query->where('customer_id', $customer);
            });

        $shipments = $query->latest()->paginate(25);

        $customers = Customer::select('id', 'company_name')->get();
        $couriers = CourierService::select('id', 'code', 'name')->where('is_active', true)->get();

        $stats = [
            'total_shipments' => Shipment::count(),
            'today_shipments' => Shipment::whereDate('created_at', today())->count(),
            'pending_shipments' => Shipment::whereIn('status', ['created', 'printed'])->count(),
            'delivered_shipments' => Shipment::where('status', 'delivered')->count(),
        ];

        return view('admin.shipments.index', compact('shipments', 'customers', 'couriers', 'stats'));
    }

    public function show(Shipment $shipment)
    {
        $shipment->load([
            'customer',
            'customerUser',
            'courierService',
            'payments',
            'transactions',
            'statusHistory',
            'courierApiLogs' => function ($query) {
                $query->with('courierService')->latest();
            },
        ]);

        return view('admin.shipments.show', compact('shipment'));
    }

    public function updateStatus(Request $request, Shipment $shipment)
    {
        $request->validate([
            'status' => 'required|in:created,printed,dispatched,in_transit,out_for_delivery,delivered,returned,cancelled,failed',
            'notes' => 'nullable|string|max:500',
        ]);

        $oldStatus = $shipment->status;

        $shipment->update([
            'status' => $request->status,
            'notes' => $request->notes ? $shipment->notes."\n".now()->format('Y-m-d H:i').': '.$request->notes : $shipment->notes,
            'delivered_at' => $request->status === 'delivered' ? now() : $shipment->delivered_at,
        ]);

        // Log status change
        activity()
            ->performedOn($shipment)
            ->causedBy(auth()->user())
            ->withProperties([
                'old_status' => $oldStatus,
                'new_status' => $request->status,
                'notes' => $request->notes,
            ])
            ->log('status_updated');

        return back()->with('success', 'Status przesyłki został zaktualizowany.');
    }
}
