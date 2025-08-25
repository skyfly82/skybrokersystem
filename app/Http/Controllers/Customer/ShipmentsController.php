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

    public function cart()
    {
        return view('customer.shipments.cart');
    }

    public function addressBook()
    {
        return view('customer.shipments.address-book');
    }

    public function processCart(Request $request)
    {
        try {
            $cartItems = $request->validate([
                'items' => 'required|array|min:1',
                'items.*.courier_code' => 'required|string',
                'items.*.service_type' => 'required|string',
                'items.*.sender' => 'required|array',
                'items.*.recipient' => 'required|array',
                'items.*.package' => 'required|array',
                'items.*.options' => 'sometimes|array',
            ])['items'];

            $customer = auth()->user()->customer;
            $user = auth()->user();
            $createdShipments = [];
            $totalAmount = 0;

            foreach ($cartItems as $item) {
                // Validate and create each shipment
                $shipmentData = [
                    'courier_code' => $item['courier_code'],
                    'service_type' => $item['service_type'],
                    'sender' => $item['sender'],
                    'recipient' => $item['recipient'],
                    'package' => $item['package'],
                ];

                // Add optional services
                if (isset($item['options'])) {
                    $shipmentData = array_merge($shipmentData, $item['options']);
                }

                $shipment = $this->shipmentService->createShipment($shipmentData, $customer, $user);
                $createdShipments[] = $shipment;
                $totalAmount += $shipment->total_price;
            }

            // If only one shipment, redirect to individual payment
            if (count($createdShipments) === 1) {
                return redirect()->route('customer.payments.create', [
                    'shipment_id' => $createdShipments[0]->id
                ])->with('success', 'Przesyłka została utworzona. Przejdź do płatności.');
            }

            // Multiple shipments - create bulk payment
            return redirect()->route('customer.payments.create', [
                'shipment_ids' => implode(',', array_column($createdShipments, 'id'))
            ])->with('success', count($createdShipments) . ' przesyłek zostało utworzonych. Przejdź do płatności.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
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

    public function getCourierServices(string $courierCode)
    {
        try {
            $courier = CourierService::where('code', $courierCode)->firstOrFail();
            
            if ($courierCode === 'inpost') {
                // InPost service matrix
                $services = [
                    [
                        'code' => 'inpost_locker_standard',
                        'name' => 'Paczkomat → Paczkomat Standard',
                        'description' => 'Nadanie i odbiór z paczkomatu (48h)',
                        'type' => 'locker_to_locker',
                        'estimated_price' => 'od 12.99 PLN'
                    ],
                    [
                        'code' => 'inpost_locker_express', 
                        'name' => 'Paczkomat → Paczkomat Express',
                        'description' => 'Nadanie i odbiór z paczkomatu (24h)', 
                        'type' => 'locker_to_locker',
                        'estimated_price' => 'od 19.99 PLN'
                    ],
                    [
                        'code' => 'inpost_courier_to_locker',
                        'name' => 'Kurier → Paczkomat', 
                        'description' => 'Odbiór kurierem, doręczenie do paczkomatu',
                        'type' => 'courier_to_locker',
                        'estimated_price' => 'od 15.99 PLN'
                    ],
                    [
                        'code' => 'inpost_locker_to_courier',
                        'name' => 'Paczkomat → Kurier',
                        'description' => 'Nadanie z paczkomatu, doręczenie kurierem', 
                        'type' => 'locker_to_courier',
                        'estimated_price' => 'od 18.99 PLN'
                    ],
                    [
                        'code' => 'inpost_courier_standard',
                        'name' => 'Kurier → Kurier Standard', 
                        'description' => 'Odbiór i doręczenie kurierem (48h)',
                        'type' => 'courier_to_courier',
                        'estimated_price' => 'od 24.99 PLN'
                    ],
                    [
                        'code' => 'inpost_courier_express',
                        'name' => 'Kurier → Kurier Express',
                        'description' => 'Odbiór i doręczenie kurierem (24h)',
                        'type' => 'courier_to_courier', 
                        'estimated_price' => 'od 34.99 PLN'
                    ]
                ];
            } else {
                $services = json_decode($courier->supported_services, true) ?? [];
            }
            
            return response()->json(['success' => true, 'data' => $services]);
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