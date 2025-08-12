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

    public function topup()
    {
        return view('customer.payments.topup');
    }

    public function processTopup(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10|max:10000',
            'method' => 'required|in:card,bank_transfer,blik,simulation'
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
}