<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrdersController extends Controller
{
    public function index(Request $request)
    {
        $customer = auth()->user()->customer;

        $query = $customer->orders()
            ->with(['shipments', 'payments'])
            ->when($request->status, function ($query, $status) {
                return $query->where('status', $status);
            });

        $orders = $query->latest()->paginate(20);

        return view('customer.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $this->authorize('view', $order);

        $order->load(['shipments.courierService', 'payments', 'customerUser']);

        return view('customer.orders.show', compact('order'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'shipment_ids' => 'required|array|min:1',
            'shipment_ids.*' => 'exists:shipments,id',
            'notes' => 'nullable|string|max:1000',
        ]);

        $customer = auth()->user()->customer;
        $user = auth()->user();

        try {
            DB::beginTransaction();

            // Get draft shipments that belong to customer
            $shipments = Shipment::whereIn('id', $request->shipment_ids)
                ->where('customer_id', $customer->id)
                ->where('status', 'draft')
                ->get();

            if ($shipments->isEmpty()) {
                return back()->withErrors(['error' => 'Nie znaleziono odpowiednich przesyłek w koszyku.']);
            }

            // Calculate total amount
            $totalAmount = $shipments->sum('total_price');

            // Create order
            $order = Order::create([
                'customer_id' => $customer->id,
                'customer_user_id' => $user->id,
                'total_amount' => $totalAmount,
                'currency' => 'PLN',
                'notes' => $request->notes,
                'status' => 'pending',
            ]);

            // Assign shipments to order
            $shipments->each(function ($shipment) use ($order) {
                $shipment->update(['order_id' => $order->id]);
            });

            DB::commit();

            return redirect()->route('customer.orders.show', $order)
                ->with('success', 'Zamówienie zostało utworzone! Numer: '.$order->order_number);

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors(['error' => 'Wystąpił błąd podczas tworzenia zamówienia: '.$e->getMessage()]);
        }
    }

    public function cancel(Order $order)
    {
        $this->authorize('update', $order);

        if (! $order->canBeCancelled()) {
            return back()->withErrors(['error' => 'To zamówienie nie może być anulowane.']);
        }

        try {
            $order->cancel();

            return back()->with('success', 'Zamówienie zostało anulowane. Przesyłki wróciły do koszyka.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Wystąpił błąd podczas anulowania zamówienia: '.$e->getMessage()]);
        }
    }

    public function pay(Order $order)
    {
        $this->authorize('view', $order);

        if (! $order->canBePaid()) {
            return back()->withErrors(['error' => 'To zamówienie nie może być opłacone.']);
        }

        // Redirect to payment page
        return redirect()->route('customer.payments.create', ['order_id' => $order->id]);
    }
}
