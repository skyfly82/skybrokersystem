<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentSimulationController extends Controller
{
    public function show(string $paymentUuid)
    {
        $payment = Payment::where('uuid', $paymentUuid)->firstOrFail();

        // Check if payment is still valid
        if ($payment->status !== 'pending') {
            return redirect()->route('customer.shipments.index')
                ->with('error', 'Ta płatność nie jest już dostępna.');
        }

        if ($payment->expires_at && $payment->expires_at->isPast()) {
            return redirect()->route('customer.shipments.index')
                ->with('error', 'Link płatności wygasł.');
        }

        return view('payment.simulate', compact('payment'));
    }

    public function process(Request $request, string $paymentUuid)
    {
        $request->validate([
            'action' => 'required|in:success,fail',
        ]);

        $payment = Payment::where('uuid', $paymentUuid)->firstOrFail();

        if ($payment->status !== 'pending') {
            return redirect()->route('customer.shipments.index')
                ->with('error', 'Ta płatność została już przetworzona.');
        }

        if ($request->action === 'success') {
            // Mark payment as completed
            $payment->update([
                'status' => 'completed',
                'paid_at' => now(),
                'provider_data' => array_merge($payment->provider_data ?? [], [
                    'simulation_result' => 'success',
                    'processed_at' => now()->toISOString(),
                ]),
            ]);

            // For shipment payments, deduct balance; for topup, add balance
            if ($payment->type === 'topup') {
                // Add balance using customer method
                $transaction = $payment->customer->addBalance($payment->amount, $payment->description);
                $transaction->update(['payment_id' => $payment->id]);
            } else {
                // For shipment payments, deduct balance
                $transaction = $payment->customer->deductBalance($payment->amount, $payment->description);
                $transaction->update(['payment_id' => $payment->id]);
            }

            // Update shipment statuses to 'created' (paid status)
            if (isset($payment->provider_data['shipment_ids'])) {
                $shipments = \App\Models\Shipment::whereIn('id', $payment->provider_data['shipment_ids'])->get();
                foreach ($shipments as $shipment) {
                    $shipment->update(['status' => 'created']);
                    // Update transaction to link with shipment
                    if ($payment->type !== 'topup') {
                        $transaction->update([
                            'transactionable_id' => $shipment->id,
                            'transactionable_type' => 'App\\Models\\Shipment',
                        ]);
                    }
                }
            }

            return redirect()->route('customer.shipments.index')
                ->with('success', 'Płatność została zrealizowana pomyślnie! Twoje przesyłki zostały opłacone.')
                ->with('clear_cart', true);
        } else {
            // Mark payment as failed
            $payment->update([
                'status' => 'failed',
                'failure_reason' => 'Payment cancelled by user in simulation',
                'provider_data' => array_merge($payment->provider_data ?? [], [
                    'simulation_result' => 'failed',
                    'processed_at' => now()->toISOString(),
                ]),
            ]);

            return redirect()->route('customer.shipments.index')
                ->with('error', 'Płatność została anulowana. Twoje przesyłki pozostają nieopłacone.');
        }
    }
}
