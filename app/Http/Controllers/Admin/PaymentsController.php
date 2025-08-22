<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class PaymentsController extends Controller
{
    public function index()
    {
        // Mock data - no services needed
        $payments = [
            [
                'id' => 1,
                'uuid' => 'PAY-001',
                'amount' => 199.99,
                'status' => 'completed',
                'customer_name' => 'Test Company',
                'created_at' => now()->subDays(1),
            ],
            [
                'id' => 2,
                'uuid' => 'PAY-002', 
                'amount' => 89.50,
                'status' => 'pending',
                'customer_name' => 'Another Corp',
                'created_at' => now()->subHours(3),
            ]
        ];

        return view('admin.payments.index', compact('payments'));
    }

    public function show($payment)
    {
        $payment = (object)[
            'id' => $payment,
            'uuid' => 'PAY-' . str_pad($payment, 3, '0', STR_PAD_LEFT),
            'amount' => 199.99,
            'status' => 'completed',
            'customer_name' => 'Test Company',
            'created_at' => now()->subDays(1),
        ];

        return view('admin.payments.show', compact('payment'));
    }

    public function refund($payment)
    {
        return back()->with('success', 'Payment refund processed successfully');
    }
}
