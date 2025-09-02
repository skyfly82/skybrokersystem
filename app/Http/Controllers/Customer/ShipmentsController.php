<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\CreateShipmentRequest;
use App\Http\Requests\LabelDownloadRequest;
use App\Models\CourierService;
use App\Models\Shipment;
use App\Services\Courier\Providers\InPostService;
use App\Services\ShipmentService;
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

        $shipment->load(['courierService', 'customerUser', 'payments', 'statusHistory']);

        return view('customer.shipments.show', compact('shipment'));
    }

    public function edit(Shipment $shipment)
    {
        $this->authorize('update', $shipment);

        // Only allow editing of unpaid shipments with status 'created'
        if (! $shipment->isEditable()) {
            return redirect()->route('customer.shipments.show', $shipment)
                ->with('error', 'Ta przesyłka nie może być edytowana w obecnym statusie.');
        }

        $couriers = CourierService::where('is_active', true)->orderBy('sort_order')->get();

        return view('customer.shipments.edit', compact('shipment', 'couriers'));
    }

    public function update(CreateShipmentRequest $request, Shipment $shipment)
    {
        $this->authorize('update', $shipment);

        // Only allow editing of unpaid shipments with status 'created'
        if (! $shipment->isEditable()) {
            return redirect()->route('customer.shipments.show', $shipment)
                ->with('error', 'Ta przesyłka nie może być edytowana w obecnym statusie.');
        }

        try {
            $validated = $request->validated();

            // Update shipment data
            $shipment->update([
                'sender_data' => $validated['sender'],
                'recipient_data' => $validated['recipient'],
                'package_data' => $validated['package'],
                'cod_amount' => $validated['cod_amount'] ?? null,
                'insurance_amount' => $validated['insurance_amount'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'service_type' => $validated['service_type'],
            ]);

            // Log the update
            activity()
                ->performedOn($shipment)
                ->causedBy(auth()->user())
                ->withProperties([
                    'old' => $shipment->getOriginal(),
                    'new' => $shipment->getAttributes(),
                ])
                ->log('shipment_updated');

            return redirect()->route('customer.shipments.show', $shipment)
                ->with('success', 'Przesyłka została zaktualizowana pomyślnie.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
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
            $validated = $request->validate([
                'items' => 'required|array|min:1',
                'items.*.courier_code' => 'required|string',
                'items.*.service_type' => 'required|string',
                'items.*.sender' => 'required|array',
                'items.*.recipient' => 'required|array',
                'items.*.package' => 'required|array',
                'items.*.options' => 'sometimes|array',
                'create_order' => 'sometimes|boolean',
                'notes' => 'sometimes|string|max:1000',
                // Legacy payment fields (for backward compatibility)
                'payment_method' => 'sometimes|string|in:balance,deferred,online',
                'total_amount' => 'sometimes|numeric|min:0',
            ]);

            $customer = auth()->user()->customer;
            $user = auth()->user();

            // Check if this is order creation workflow
            $createOrder = $validated['create_order'] ?? false;

            if ($createOrder) {
                // New order-based workflow
                return $this->createOrderFromCart($validated, $customer, $user);
            }

            // Legacy payment workflow (for backward compatibility)
            $paymentMethod = $validated['payment_method'];
            $totalAmount = floatval($validated['total_amount']);

            // Check payment method availability
            if ($paymentMethod === 'balance') {
                if ($customer->current_balance < $totalAmount) {
                    return back()->withErrors(['error' => 'Niewystarczające saldo na koncie. Dostępne: '.number_format($customer->current_balance, 2).' PLN']);
                }
            } elseif ($paymentMethod === 'deferred') {
                $availableCredit = $customer->current_balance + ($customer->credit_limit ?? 0);
                if ($availableCredit < $totalAmount) {
                    return back()->withErrors(['error' => 'Przekroczono dostępny limit kredytowy. Dostępne: '.number_format($availableCredit, 2).' PLN']);
                }
                if (! $customer->credit_limit) {
                    return back()->withErrors(['error' => 'Płatność odroczona nie jest dostępna - brak przyznanego limitu kredytowego']);
                }
            }

            $createdShipments = [];
            $processedAmount = 0;

            foreach ($validated['items'] as $item) {
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

                // Create shipment in draft mode (don't send to courier yet)
                $shipment = new \App\Models\Shipment;
                $shipment->customer_id = $customer->id;
                $shipment->customer_user_id = $user->id;
                $shipment->courier_service_id = 1; // Default to InPost
                $shipment->status = 'draft';
                $shipment->service_type = $shipmentData['service_type'];
                $shipment->sender_data = $shipmentData['sender'];
                $shipment->recipient_data = $shipmentData['recipient'];
                $shipment->package_data = $shipmentData['package'];
                $shipment->reference_number = $shipmentData['reference_number'] ?? null;
                $shipment->notes = $shipmentData['notes'] ?? null;

                // Calculate estimated cost (you may want to use a pricing service here)
                $estimatedCost = $this->calculateEstimatedCost($shipmentData);
                $shipment->cost_data = [
                    'price' => $estimatedCost,
                    'currency' => 'PLN',
                    'vat_rate' => 23,
                    'total' => $estimatedCost * 1.23,
                ];

                $shipment->save();
                $createdShipments[] = $shipment;
                $processedAmount += $shipment->cost_data['total'];
            }

            // Handle different payment methods
            if ($paymentMethod === 'balance') {
                // Deduct from balance immediately
                foreach ($createdShipments as $shipment) {
                    // Deduct balance using customer method with transaction details
                    $transaction = $customer->deductBalance(
                        $shipment->cost_data['total'],
                        'Płatność za przesyłkę: '.$shipment->id,
                        null, // payment_id
                        $shipment->id, // transactionable_id
                        'App\\Models\\Shipment' // transactionable_type
                    );

                    $shipment->update(['status' => 'created']); // Use correct status - paid and ready for InPost
                }

                return redirect()->route('customer.shipments.index')
                    ->with('success', 'Zamówienie zostało złożone i opłacone z salda konta!')
                    ->with('clear_cart', true);

            } elseif ($paymentMethod === 'deferred') {
                // Mark as paid on credit
                foreach ($createdShipments as $shipment) {
                    $shipment->update(['status' => 'created']); // Use correct status

                    // Create deferred payment transaction
                    $transaction = new \App\Models\Transaction;
                    $transaction->uuid = \Illuminate\Support\Str::uuid();
                    $transaction->customer_id = $customer->id;
                    $transaction->payment_id = null;
                    $transaction->transactionable_id = $shipment->id;
                    $transaction->transactionable_type = 'App\\Models\\Shipment';
                    $transaction->type = 'debit'; // Change from 'deferred' to valid enum
                    $transaction->amount = $shipment->total_price;
                    $transaction->balance_before = $customer->current_balance;
                    $transaction->balance_after = $customer->current_balance; // Balance unchanged for deferred
                    $transaction->description = 'Płatność odroczona za przesyłkę: '.($shipment->tracking_number ?: $shipment->id);
                    $transaction->save();
                }

                return redirect()->route('customer.shipments.index')
                    ->with('success', 'Zamówienie zostało złożone na płatność odroczoną!')
                    ->with('clear_cart', true);

            } elseif ($paymentMethod === 'online') {
                // Create payment and redirect to simulation page
                $payment = \App\Models\Payment::create([
                    'customer_id' => $customer->id,
                    'customer_user_id' => $user->id,
                    'type' => 'shipment_bulk',
                    'method' => 'simulation',
                    'provider' => 'simulation',
                    'amount' => $processedAmount,
                    'currency' => 'PLN',
                    'status' => 'pending',
                    'description' => 'Płatność za '.count($createdShipments).' przesyłek',
                    'provider_data' => [
                        'shipment_ids' => collect($createdShipments)->pluck('id')->toArray(),
                    ],
                    'expires_at' => now()->addHours(24),
                ]);

                return redirect()->route('payment.simulate', $payment->uuid)
                    ->with('clear_cart', true);
            }

            // Default fallback - redirect to payment creation
            $shipmentIds = collect($createdShipments)->pluck('id')->implode(',');

            return redirect()->route('customer.payments.create', ['shipment_ids' => $shipmentIds])
                ->with('success', count($createdShipments).' przesyłek zostało utworzonych. Przejdź do płatności.');

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
                        'estimated_price' => 'od 12.99 PLN',
                    ],
                    [
                        'code' => 'inpost_locker_express',
                        'name' => 'Paczkomat → Paczkomat Express',
                        'description' => 'Nadanie i odbiór z paczkomatu (24h)',
                        'type' => 'locker_to_locker',
                        'estimated_price' => 'od 16.99 PLN',
                    ],
                    [
                        'code' => 'inpost_courier_to_locker',
                        'name' => 'Kurier → Paczkomat',
                        'description' => 'Odbiór kurierem, doręczenie do paczkomatu',
                        'type' => 'courier_to_locker',
                        'estimated_price' => 'od 15.99 PLN',
                    ],
                    [
                        'code' => 'inpost_locker_to_courier',
                        'name' => 'Paczkomat → Kurier',
                        'description' => 'Nadanie z paczkomatu, doręczenie kurierem',
                        'type' => 'locker_to_courier',
                        'estimated_price' => 'od 17.99 PLN',
                    ],
                    [
                        'code' => 'inpost_courier_standard',
                        'name' => 'Kurier → Kurier Standard',
                        'description' => 'Odbiór i doręczenie kurierem (48h)',
                        'type' => 'courier_to_courier',
                        'estimated_price' => 'od 18.99 PLN',
                    ],
                    [
                        'code' => 'inpost_courier_express',
                        'name' => 'Kurier → Kurier Express',
                        'description' => 'Odbiór i doręczenie kurierem (24h)',
                        'type' => 'courier_to_courier',
                        'estimated_price' => 'od 24.99 PLN',
                    ],
                    [
                        'code' => 'inpost_pop_standard',
                        'name' => 'POP Standard',
                        'description' => 'Punkt odbioru partnerski (48h)',
                        'type' => 'pop_pickup',
                        'estimated_price' => 'od 10.99 PLN',
                    ],
                    [
                        'code' => 'inpost_pop_express',
                        'name' => 'POP Express',
                        'description' => 'Punkt odbioru partnerski (24h)',
                        'type' => 'pop_pickup',
                        'estimated_price' => 'od 14.99 PLN',
                    ],
                    [
                        'code' => 'inpost_no_label',
                        'name' => 'Bez etykiety',
                        'description' => 'Płatność przy odbiorze (48h)',
                        'type' => 'no_label',
                        'estimated_price' => 'od 8.99 PLN',
                    ],
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
                'city' => $request->city,
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

    public function label(LabelDownloadRequest $request, Shipment $shipment)
    {
        $this->authorize('view', $shipment);

        try {
            $customer = auth()->user()->customer;
            $customerSettings = $customer->settings ?? [];

            // Get preferred format from customer settings or request parameter
            $preferredFormat = $customerSettings['label_format'] ?? 'pdf_a4';
            $requestFormat = $request->get('format');

            // Parse format and size from customer preference or request
            if ($requestFormat) {
                $format = $requestFormat;
                $size = $request->get('size', 'A4');
            } else {
                // Parse customer preference (e.g., 'pdf_a4', 'pdf_a6', 'zpl', 'epl')
                if (str_contains($preferredFormat, '_')) {
                    [$format, $sizeCode] = explode('_', $preferredFormat, 2);
                    $size = strtoupper($sizeCode);
                } else {
                    $format = $preferredFormat;
                    $size = 'A4';
                }
            }

            // Fallback to system defaults
            $format = $format ?: config('skybrokersystem.couriers.label_format', 'pdf');
            $size = $size ?: config('skybrokersystem.couriers.label_size', 'A4');

            // Check if shipment has tracking number or external ID for label generation
            if (! $shipment->tracking_number && ! $shipment->external_id) {
                return back()->withErrors(['error' => 'Przesyłka nie ma jeszcze numeru śledzenia ani ID zewnętrznego. Spróbuj ponownie później.']);
            }

            $identifier = $shipment->tracking_number ?: $shipment->external_id;
            $label = $this->shipmentService->getLabel($identifier, $format, $size);

            // Get format configuration
            $availableFormats = config('skybrokersystem.couriers.available_formats', []);
            $formatConfig = $availableFormats[strtolower($format)] ?? $availableFormats['pdf'];

            $contentType = $formatConfig['mime_type'];
            $fileExtension = $formatConfig['extension'];

            // Generate filename with format and size info
            $filename = 'label-'.$shipment->tracking_number;
            if (strtolower($format) === 'pdf' && strtoupper($size) !== 'A4') {
                $filename .= '-'.strtoupper($size);
            }
            $filename .= '.'.$fileExtension;

            return response($label)
                ->header('Content-Type', $contentType)
                ->header('Content-Disposition', 'attachment; filename="'.$filename.'"');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Nie udało się pobrać etykiety: '.$e->getMessage()]);
        }
    }

    public function cancel(Shipment $shipment)
    {
        $this->authorize('update', $shipment);

        if (! $shipment->canBeCancelled()) {
            return back()->withErrors(['error' => 'Przesyłka nie może być anulowana w obecnym statusie.']);
        }

        try {
            $this->shipmentService->cancelShipment($shipment);

            return back()->with('success', 'Przesyłka została anulowana.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function destroy(Shipment $shipment)
    {
        $this->authorize('delete', $shipment);

        // Only allow deletion of unpaid shipments with status 'created'
        if (! in_array($shipment->status, ['created', 'failed']) || $shipment->payments()->where('status', 'completed')->exists()) {
            return back()->withErrors(['error' => 'Można usuwać tylko nieopłacone przesyłki ze statusem "utworzona" lub "nieudana".']);
        }

        try {
            // Log the deletion
            activity()
                ->performedOn($shipment)
                ->causedBy(auth()->user())
                ->withProperties([
                    'deleted_shipment' => [
                        'tracking_number' => $shipment->tracking_number,
                        'reference_number' => $shipment->reference_number,
                        'status' => $shipment->status,
                        'total_price' => $shipment->total_price,
                    ],
                ])
                ->log('shipment_deleted');

            $shipment->delete();

            return redirect()->route('customer.shipments.index')
                ->with('success', 'Przesyłka została usunięta.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Nie udało się usunąć przesyłki: '.$e->getMessage()]);
        }
    }

    public function bulkCreate(Request $request)
    {
        $customer = auth()->user()->customer;

        try {
            $validated = $request->validate([
                'shipments' => 'required|array|min:1|max:20',
                'shipments.*.type' => 'required|string',
                'shipments.*.sender' => 'required|array',
                'shipments.*.recipient' => 'required|array',
                'shipments.*.selectedOffer' => 'required|array',
                'payment_method' => 'required|string|in:balance,deferred,online',
                'total_amount' => 'required|numeric|min:0',
            ]);

            // Check payment method availability
            $totalAmount = floatval($validated['total_amount']);
            $paymentMethod = $validated['payment_method'];

            if ($paymentMethod === 'balance') {
                if ($customer->current_balance < $totalAmount) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Niewystarczające saldo na koncie. Dostępne: '.number_format($customer->current_balance, 2).' PLN',
                    ], 400);
                }
            } elseif ($paymentMethod === 'deferred') {
                $availableCredit = $customer->current_balance + ($customer->credit_limit ?? 0);
                if ($availableCredit < $totalAmount) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Przekroczono dostępny limit kredytowy. Dostępne: '.number_format($availableCredit, 2).' PLN',
                    ], 400);
                }
                if (! $customer->credit_limit) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Płatność odroczona nie jest dostępna - brak przyznanego limitu kredytowego',
                    ], 400);
                }
            }

            $shipments = [];
            $processedAmount = 0;

            foreach ($validated['shipments'] as $shipmentData) {
                // Create shipment in draft mode (don't send to courier yet)
                $shipment = new \App\Models\Shipment;
                $shipment->customer_id = $customer->id;
                $shipment->customer_user_id = auth()->user()->id;
                $shipment->courier_service_id = 1; // Default to InPost
                $shipment->status = 'draft';
                $shipment->service_type = $shipmentData['selectedOffer']['id'];
                $shipment->sender_data = $shipmentData['sender'];
                $shipment->recipient_data = $shipmentData['recipient'];
                $shipment->package_data = [
                    'weight' => $shipmentData['dimensions']['weight'] ?? 1,
                    'length' => $shipmentData['dimensions']['length'] ?? 20,
                    'width' => $shipmentData['dimensions']['width'] ?? 15,
                    'height' => $shipmentData['dimensions']['height'] ?? 10,
                ];
                $shipment->notes = $shipmentData['notes'] ?? '';

                // Calculate estimated cost
                $shipmentDataForCost = [
                    'service_type' => $shipmentData['selectedOffer']['id'],
                    'package' => $shipment->package_data,
                ];
                $estimatedCost = $this->calculateEstimatedCost($shipmentDataForCost);
                $shipment->cost_data = [
                    'price' => $estimatedCost,
                    'currency' => 'PLN',
                    'vat_rate' => 23,
                    'total' => $estimatedCost * 1.23,
                ];

                $shipment->save();
                $shipments[] = $shipment;
                $processedAmount += $shipment->cost_data['total'];
            }

            // Handle different payment methods
            if ($paymentMethod === 'balance') {
                // Deduct from balance immediately
                foreach ($shipments as $shipment) {
                    // Deduct balance using customer method with transaction details
                    $transaction = $customer->deductBalance(
                        $shipment->cost_data['total'],
                        'Płatność za przesyłkę: '.$shipment->id,
                        null, // payment_id
                        $shipment->id, // transactionable_id
                        'App\\Models\\Shipment' // transactionable_type
                    );

                    $shipment->update(['status' => 'created']); // Use correct status
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Zamówienie zostało złożone i opłacone z salda!',
                    'shipments' => count($shipments),
                    'total_amount' => $processedAmount,
                    'payment_method' => $paymentMethod,
                    'redirect_url' => route('customer.shipments.index'),
                    'clear_cart' => true,
                ]);
            } elseif ($paymentMethod === 'deferred') {
                // Mark as paid on credit
                foreach ($shipments as $shipment) {
                    $shipment->update(['status' => 'created']); // Use correct status

                    // Create deferred payment transaction
                    $transaction = new \App\Models\Transaction;
                    $transaction->uuid = \Illuminate\Support\Str::uuid();
                    $transaction->customer_id = $customer->id;
                    $transaction->payment_id = null;
                    $transaction->transactionable_id = $shipment->id;
                    $transaction->transactionable_type = 'App\\Models\\Shipment';
                    $transaction->type = 'debit'; // Change from 'deferred' to valid enum
                    $transaction->amount = $shipment->total_price;
                    $transaction->balance_before = $customer->current_balance;
                    $transaction->balance_after = $customer->current_balance; // Balance unchanged for deferred
                    $transaction->description = 'Płatność odroczona za przesyłkę: '.($shipment->tracking_number ?: $shipment->id);
                    $transaction->save();
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Zamówienie zostało złożone na płatność odroczoną!',
                    'shipments' => count($shipments),
                    'total_amount' => $processedAmount,
                    'payment_method' => $paymentMethod,
                    'redirect_url' => route('customer.shipments.index'),
                    'clear_cart' => true,
                ]);
            } elseif ($paymentMethod === 'online') {
                // Create payment and redirect to simulation page
                $payment = \App\Models\Payment::create([
                    'customer_id' => $customer->id,
                    'customer_user_id' => auth()->user()->id,
                    'type' => 'shipment_bulk',
                    'method' => 'simulation',
                    'provider' => 'simulation',
                    'amount' => $processedAmount,
                    'currency' => 'PLN',
                    'status' => 'pending',
                    'description' => 'Płatność za '.count($shipments).' przesyłek',
                    'provider_data' => [
                        'shipment_ids' => collect($shipments)->pluck('id')->toArray(),
                    ],
                    'expires_at' => now()->addHours(24),
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Przesyłki utworzone - przekierowanie do płatności',
                    'shipments' => count($shipments),
                    'total_amount' => $processedAmount,
                    'payment_method' => $paymentMethod,
                    'payment_id' => $payment->id,
                    'redirect_url' => route('payment.simulate', $payment->uuid),
                    'clear_cart' => true,
                ]);
            }

            // Default fallback - redirect to payment creation
            $shipmentIds = collect($shipments)->pluck('id')->implode(',');

            return response()->json([
                'success' => true,
                'message' => 'Przesyłki utworzone - przejdź do płatności',
                'shipments' => count($shipments),
                'total_amount' => $processedAmount,
                'payment_method' => $paymentMethod,
                'redirect_url' => route('customer.payments.create', ['shipment_ids' => $shipmentIds]),
                'clear_cart' => true,
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Błędne dane formularza',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Bulk shipment creation failed', [
                'customer_id' => $customer->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Wystąpił błąd podczas tworzenia przesyłek: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Calculate estimated cost for a shipment based on service type and package data
     */
    private function calculateEstimatedCost(array $shipmentData): float
    {
        $serviceType = $shipmentData['service_type'];
        $package = $shipmentData['package'];
        $weight = $package['weight'] ?? 1.0;

        // Basic pricing logic - this should ideally use actual courier pricing API
        $basePrices = [
            'inpost_locker_standard' => 12.99,
            'inpost_locker_express' => 16.99,
            'inpost_courier_to_locker' => 15.99,
            'inpost_locker_to_courier' => 17.99,
            'inpost_courier_standard' => 18.99,
            'inpost_courier_express' => 24.99,
            'inpost_kurier_standard' => 18.99,
            'inpost_pop_standard' => 10.99,
            'inpost_pop_express' => 14.99,
            'inpost_no_label' => 8.99,
        ];

        $basePrice = $basePrices[$serviceType] ?? 15.99; // Default price

        // Add weight-based pricing
        if ($weight > 5) {
            $basePrice += ($weight - 5) * 2.0;
        }

        // Add size-based pricing for large packages
        $volume = ($package['length'] ?? 20) * ($package['width'] ?? 15) * ($package['height'] ?? 10);
        if ($volume > 8000) { // > 20x15x10 cm
            $basePrice += 5.0;
        }

        return round($basePrice, 2);
    }

    /**
     * Create order from cart items (new workflow)
     */
    private function createOrderFromCart(array $validated, $customer, $user)
    {
        \DB::beginTransaction();

        try {
            $createdShipments = [];
            $totalAmount = 0;

            // Create draft shipments first
            foreach ($validated['items'] as $item) {
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

                // Create shipment in draft mode
                $shipment = new \App\Models\Shipment;
                $shipment->customer_id = $customer->id;
                $shipment->customer_user_id = $user->id;
                $shipment->courier_service_id = 1; // Default to InPost
                $shipment->status = 'draft';
                $shipment->service_type = $shipmentData['service_type'];
                $shipment->sender_data = $shipmentData['sender'];
                $shipment->recipient_data = $shipmentData['recipient'];
                $shipment->package_data = $shipmentData['package'];
                $shipment->reference_number = $shipmentData['reference_number'] ?? null;
                $shipment->notes = $shipmentData['notes'] ?? null;

                // Calculate estimated cost
                $estimatedCost = $this->calculateEstimatedCost($shipmentData);
                $shipment->cost_data = [
                    'price' => $estimatedCost,
                    'currency' => 'PLN',
                    'vat_rate' => 23,
                    'total' => $estimatedCost * 1.23,
                ];

                $shipment->save();
                $createdShipments[] = $shipment;
                $totalAmount += $shipment->cost_data['total'];
            }

            // Create order
            $order = \App\Models\Order::create([
                'customer_id' => $customer->id,
                'customer_user_id' => $user->id,
                'total_amount' => $totalAmount,
                'currency' => 'PLN',
                'notes' => $validated['notes'] ?? null,
                'status' => 'pending',
            ]);

            // Assign shipments to order
            foreach ($createdShipments as $shipment) {
                $shipment->update(['order_id' => $order->id]);
            }

            \DB::commit();

            return redirect()->route('customer.orders.show', $order)
                ->with('success', 'Zamówienie zostało utworzone! Numer: '.$order->order_number)
                ->with('clear_cart', true);

        } catch (\Exception $e) {
            \DB::rollBack();

            return back()->withErrors(['error' => 'Wystąpił błąd podczas tworzenia zamówienia: '.$e->getMessage()]);
        }
    }
}
