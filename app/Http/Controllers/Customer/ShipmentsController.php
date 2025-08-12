<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use App\Models\CourierService;
use App\Http\Requests\Customer\CreateShipmentRequest;
use App\Services\ShipmentService;
use App\Services\Courier\Providers\InPostService;
use Illuminate\Http\Request;

class ShipmentsController extends Controller
{
    public function __construct(
        private ShipmentService $shipmentService,
        private InPostService $inPostService
    ) {}

    public function index(Request $request)
    {
        $customer = auth()->user()->customer;
        
        $query = $customer->shipments()->with(['courierService'])
            ->when($request->search, function ($query, $search) {
                return $query->where('tracking_number', 'like', "%{$search}%")
                            ->orWhere('reference_number', 'like', "%{$search}%");
            })
            ->when($request->status, function ($query, $status) {
                return $query->where('status', $status);
            });

        $shipments = $query->latest()->paginate(20);

        return view('customer.shipments.index', compact('shipments'));
    }

    public function show(Shipment $shipment)
    {
        $this->authorize('view', $shipment);
        
        $shipment->load(['courierService', 'customerUser', 'payments']);

        return view('customer.shipments.show', compact('shipment'));
    }

    public function create()
    {
        $couriers = CourierService::where('is_active', true)->orderBy('sort_order')->get();
        
        return view('customer.shipments.create', compact('couriers'));
    }

    public function store(CreateShipmentRequest $request)
    {
        try {
            $shipment = $this->shipmentService->createShipment(
                $request->validated(),
                auth()->user()->customer,
                auth()->user()
            );

            return redirect()->route('customer.shipments.show', $shipment)
                ->with('success', 'Przesyłka została utworzona pomyślnie.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function calculatePrice(Request $request)
    {
        try {
            $prices = $this->inPostService->calculatePrice($request->all());
            
            return response()->json(['success' => true, 'prices' => $prices]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function getPickupPoints(Request $request)
    {
        try {
            $points = $this->inPostService->getPickupPoints([
                'city' => $request->city
            ]);
            
            return response()->json(['success' => true, 'points' => $points]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function track(Shipment $shipment)
    {
        $this->authorize('view', $shipment);
        
        try {
            $tracking = $this->shipmentService->trackShipment($shipment->tracking_number);
            
            return view('customer.shipments.track', compact('shipment', 'tracking'));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Nie udało się pobrać informacji o śledzeniu.']);
        }
    }

    public function label(Shipment $shipment)
    {
        $this->authorize('view', $shipment);
        
        try {
            $label = $this->shipmentService->getLabel($shipment->tracking_number);
            
            return response($label)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="label-' . $shipment->tracking_number . '.pdf"');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Nie udało się pobrać etykiety.']);
        }
    }

    public function cancel(Shipment $shipment)
    {
        $this->authorize('update', $shipment);
        
        if (!$shipment->canBeCancelled()) {
            return back()->withErrors(['error' => 'Przesyłka nie może być anulowana w obecnym statusie.']);
        }
        
        try {
            $this->shipmentService->cancelShipment($shipment);
            
            return back()->with('success', 'Przesyłka została anulowana.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}