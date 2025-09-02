<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\Payment\PaymentService;
use Illuminate\Http\Request;

class PaymentsController extends Controller
{
    public function __construct(
        private PaymentService $paymentService
    ) {}

    public function index(Request $request)
    {
        $customer = auth()->user()->customer;

        $query = $customer->payments()
            ->when($request->status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when($request->type, function ($query, $type) {
                return $query->where('type', $type);
            });

        $payments = $query->latest()->paginate(20);

        return view('customer.payments.index', compact('payments'));
    }

    public function show(Payment $payment)
    {
        $this->authorize('view', $payment);

        $payment->load(['payable', 'transaction']);

        return view('customer.payments.show', compact('payment'));
    }

    public function create(Request $request)
    {
        $customer = auth()->user()->customer;

        // Handle order payment (new workflow)
        if ($request->has('order_id')) {
            $order = $customer->orders()->findOrFail($request->order_id);

            if (! $order->canBePaid()) {
                return redirect()->route('customer.orders.show', $order)
                    ->with('error', 'To zamówienie nie może być opłacone.');
            }

            return view('customer.payments.create', compact('order'));
        }

        // Handle single shipment payment
        if ($request->has('shipment_id')) {
            $shipment = $customer->shipments()->findOrFail($request->shipment_id);

            return view('customer.payments.create', compact('shipment'));
        }

        // Handle bulk shipment payment
        if ($request->has('shipment_ids')) {
            $shipmentIds = explode(',', $request->shipment_ids);
            $shipments = $customer->shipments()->whereIn('id', $shipmentIds)->get();

            if ($shipments->isEmpty()) {
                return redirect()->route('customer.shipments.index')
                    ->with('error', 'Nie znaleziono przesyłek do płatności.');
            }

            return view('customer.payments.create', compact('shipments'));
        }

        return redirect()->route('customer.shipments.index')
            ->with('error', 'Brak przesyłek do płatności.');
    }

    public function process(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|in:balance,deferred,online',
            'order_id' => 'sometimes|exists:orders,id',
            'shipment_id' => 'sometimes|exists:shipments,id',
            'shipment_ids' => 'sometimes|string',
        ]);

        $customer = auth()->user()->customer;
        $user = auth()->user();

        try {
            // Handle order payment (new workflow)
            if ($request->has('order_id')) {
                return $this->processOrderPayment($request, $customer, $user);
            }

            // Get shipments (legacy workflow)
            $shipments = collect();
            if ($request->has('shipment_id')) {
                $shipments->push($customer->shipments()->findOrFail($request->shipment_id));
            } elseif ($request->has('shipment_ids')) {
                $shipmentIds = explode(',', $request->shipment_ids);
                $shipments = $customer->shipments()->whereIn('id', $shipmentIds)->get();
            } else {
                return back()->withErrors(['error' => 'Nie wybrano przesyłek do płatności.']);
            }

            if ($shipments->isEmpty()) {
                return back()->withErrors(['error' => 'Nie znaleziono przesyłek do płatności.']);
            }

            // Check if shipments are payable
            foreach ($shipments as $shipment) {
                if ($shipment->status !== 'created' || $shipment->payments()->where('status', 'completed')->exists()) {
                    return back()->withErrors(['error' => 'Jedna z przesyłek nie może być opłacona.']);
                }
            }

            $totalAmount = $shipments->sum('total_price');
            $paymentMethod = $request->payment_method;

            if ($paymentMethod === 'balance') {
                // Check if customer has enough balance
                if ($customer->current_balance < $totalAmount) {
                    return back()->withErrors(['error' => 'Niewystarczające saldo na koncie. Dostępne: '.number_format($customer->current_balance, 2).' PLN']);
                }

                // Process payment from balance
                foreach ($shipments as $shipment) {
                    // Deduct balance using customer method with transaction details
                    $transaction = $customer->deductBalance(
                        $shipment->total_price,
                        'Płatność za przesyłkę: '.($shipment->tracking_number ?: $shipment->id),
                        null, // payment_id
                        $shipment->id, // transactionable_id
                        'App\\Models\\Shipment' // transactionable_type
                    );

                    $shipment->update(['status' => 'created']); // Use correct status
                }

                return redirect()->route('customer.shipments.index')
                    ->with('success', 'Płatność została zrealizowana pomyślnie z salda konta!');

            } elseif ($paymentMethod === 'deferred') {
                // Check credit limit
                $availableCredit = $customer->current_balance + ($customer->credit_limit ?? 0);
                if ($availableCredit < $totalAmount) {
                    return back()->withErrors(['error' => 'Przekroczono dostępny limit kredytowy. Dostępne: '.number_format($availableCredit, 2).' PLN']);
                }

                if (! $customer->credit_limit) {
                    return back()->withErrors(['error' => 'Płatność odroczona nie jest dostępna - brak przyznanego limitu kredytowego']);
                }

                // Process deferred payment
                foreach ($shipments as $shipment) {
                    $shipment->update(['status' => 'created']); // Use correct status

                    // Create deferred payment transaction
                    $transaction = new \App\Models\Transaction;
                    $transaction->uuid = \Illuminate\Support\Str::uuid();
                    $transaction->customer_id = $customer->id;
                    $transaction->payment_id = null;
                    $transaction->transactionable_id = $shipment->id;
                    $transaction->transactionable_type = 'App\\Models\\Shipment';
                    $transaction->type = 'debit';
                    $transaction->amount = $shipment->total_price;
                    $transaction->balance_before = $customer->current_balance;
                    $transaction->balance_after = $customer->current_balance; // Balance unchanged for deferred
                    $transaction->description = 'Płatność odroczona za przesyłkę: '.($shipment->tracking_number ?: $shipment->id);
                    $transaction->save();
                }

                return redirect()->route('customer.shipments.index')
                    ->with('success', 'Płatność została zrealizowana na płatność odroczoną!');

            } elseif ($paymentMethod === 'online') {
                // Create payment and redirect to simulation
                $payment = \App\Models\Payment::create([
                    'customer_id' => $customer->id,
                    'customer_user_id' => $user->id,
                    'type' => $shipments->count() > 1 ? 'shipment_bulk' : 'shipment_single',
                    'method' => 'simulation',
                    'provider' => 'simulation',
                    'amount' => $totalAmount,
                    'currency' => 'PLN',
                    'status' => 'pending',
                    'description' => 'Płatność za '.$shipments->count().' przesyłek',
                    'provider_data' => [
                        'shipment_ids' => $shipments->pluck('id')->toArray(),
                    ],
                    'expires_at' => now()->addHours(24),
                ]);

                return redirect()->route('payment.simulate', $payment->uuid);
            }

            return back()->withErrors(['error' => 'Nieznany sposób płatności.']);

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Wystąpił błąd podczas przetwarzania płatności: '.$e->getMessage()]);
        }
    }

    public function topup()
    {
        return view('customer.payments.topup');
    }

    public function processTopup(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10|max:10000',
            'method' => 'required|in:card,bank_transfer,blik,simulation',
        ]);

        try {
            $payment = $this->paymentService->createPayment(
                auth()->user()->customer,
                $request->amount,
                'topup',
                $request->method
            );

            $result = $this->paymentService->processPayment($payment);

            if ($result['success'] && $result['payment_url']) {
                return redirect($result['payment_url']);
            }

            return redirect()->route('customer.payments.show', $payment)
                ->with('success', 'Płatność została utworzona.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Process payment for an order (new workflow)
     */
    private function processOrderPayment(Request $request, $customer, $user)
    {
        $order = $customer->orders()->findOrFail($request->order_id);

        if (! $order->canBePaid()) {
            return back()->withErrors(['error' => 'To zamówienie nie może być opłacone.']);
        }

        $paymentMethod = $request->payment_method;
        $totalAmount = $order->total_amount;

        \DB::beginTransaction();

        try {
            if ($paymentMethod === 'balance') {
                // Check if customer has enough balance
                if ($customer->current_balance < $totalAmount) {
                    return back()->withErrors(['error' => 'Niewystarczające saldo na koncie. Dostępne: '.number_format($customer->current_balance, 2).' PLN']);
                }

                // Create payment record for the order
                $payment = Payment::create([
                    'customer_id' => $customer->id,
                    'customer_user_id' => $user->id,
                    'payable_type' => 'App\\Models\\Order',
                    'payable_id' => $order->id,
                    'type' => 'shipment',
                    'method' => 'wallet',
                    'provider' => 'wallet',
                    'amount' => $totalAmount,
                    'currency' => 'PLN',
                    'status' => 'completed',
                    'description' => 'Płatność za zamówienie: '.$order->order_number,
                    'completed_at' => now(),
                ]);

                // Process payment for each shipment in the order
                foreach ($order->shipments as $shipment) {
                    // Deduct balance using customer method with transaction details
                    $transaction = $customer->deductBalance(
                        $shipment->total_price,
                        'Płatność za przesyłkę w zamówieniu: '.$order->order_number,
                        $payment->id, // payment_id
                        $shipment->id, // transactionable_id
                        'App\\Models\\Shipment' // transactionable_type
                    );

                    // Update shipment status to PAID (not just created)
                    $shipment->update(['status' => 'paid']);
                }

                // Mark order as paid
                $order->markAsPaid();

                \DB::commit();

                return redirect()->route('customer.orders.show', $order)
                    ->with('success', 'Zamówienie zostało opłacone pomyślnie z salda konta!');

            } elseif ($paymentMethod === 'deferred') {
                // Check credit limit
                $availableCredit = $customer->current_balance + ($customer->credit_limit ?? 0);
                if ($availableCredit < $totalAmount) {
                    return back()->withErrors(['error' => 'Przekroczono dostępny limit kredytowy. Dostępne: '.number_format($availableCredit, 2).' PLN']);
                }

                if (! $customer->credit_limit) {
                    return back()->withErrors(['error' => 'Płatność odroczona nie jest dostępna - brak przyznanego limitu kredytowego']);
                }

                // Create payment record for deferred payment
                $payment = Payment::create([
                    'customer_id' => $customer->id,
                    'customer_user_id' => $user->id,
                    'payable_type' => 'App\\Models\\Order',
                    'payable_id' => $order->id,
                    'type' => 'shipment',
                    'method' => 'bank_transfer',
                    'provider' => 'deferred_payment',
                    'amount' => $totalAmount,
                    'currency' => 'PLN',
                    'status' => 'completed',
                    'description' => 'Płatność odroczona za zamówienie: '.$order->order_number,
                    'completed_at' => now(),
                ]);

                // Process deferred payment for each shipment
                foreach ($order->shipments as $shipment) {
                    // Create deferred payment transaction (no balance deduction)
                    $transaction = new \App\Models\Transaction;
                    $transaction->uuid = \Illuminate\Support\Str::uuid();
                    $transaction->customer_id = $customer->id;
                    $transaction->payment_id = $payment->id;
                    $transaction->transactionable_id = $shipment->id;
                    $transaction->transactionable_type = 'App\\Models\\Shipment';
                    $transaction->type = 'debit';
                    $transaction->amount = $shipment->total_price;
                    $transaction->balance_before = $customer->current_balance;
                    $transaction->balance_after = $customer->current_balance; // Balance unchanged for deferred
                    $transaction->description = 'Płatność odroczona za przesyłkę w zamówieniu: '.$order->order_number;
                    $transaction->save();

                    // Update shipment status to PAID
                    $shipment->update(['status' => 'paid']);
                }

                // Mark order as paid
                $order->markAsPaid();

                \DB::commit();

                return redirect()->route('customer.orders.show', $order)
                    ->with('success', 'Zamówienie zostało opłacone na płatność odroczoną!');

            } elseif ($paymentMethod === 'online') {
                // Create payment record for online payment
                $payment = Payment::create([
                    'customer_id' => $customer->id,
                    'customer_user_id' => $user->id,
                    'payable_type' => 'App\\Models\\Order',
                    'payable_id' => $order->id,
                    'type' => 'shipment',
                    'method' => 'simulation',
                    'provider' => 'simulation',
                    'amount' => $totalAmount,
                    'currency' => 'PLN',
                    'status' => 'pending',
                    'description' => 'Płatność online za zamówienie: '.$order->order_number,
                    'expires_at' => now()->addHours(24),
                ]);

                \DB::commit();

                return redirect()->route('payment.simulate', $payment->uuid);
            }

            \DB::rollBack();

            return back()->withErrors(['error' => 'Nieznany sposób płatności.']);

        } catch (\Exception $e) {
            \DB::rollBack();

            return back()->withErrors(['error' => 'Wystąpił błąd podczas przetwarzania płatności: '.$e->getMessage()]);
        }
    }
}
