<?php

declare(strict_types=1);

namespace App\Services\Payment\Providers;

use App\Models\Payment;

class SimulationProvider implements PaymentProviderInterface
{
    public function createPayment(Payment $payment): array
    {
        // Simulate payment creation
        $externalId = 'sim_' . uniqid();
        
        // Auto-complete small amounts for testing
        $status = $payment->amount <= 100 ? 'completed' : 'pending';
        
        if ($status === 'completed') {
            $payment->markAsCompleted();
        }

        return [
            'external_id' => $externalId,
            'status' => $status,
            'payment_url' => route('payments.simulation', ['payment' => $payment->uuid]),
            'metadata' => [
                'provider' => 'simulation',
                'created_at' => now()->toISOString(),
            ]
        ];
    }

    public function getPaymentStatus(string $externalId): array
    {
        // Simulate status check
        return [
            'status' => 'completed',
            'external_id' => $externalId,
        ];
    }

    public function refundPayment(Payment $payment, float $amount): array
    {
        // Simulate refund
        return [
            'external_id' => 'ref_' . uniqid(),
            'status' => 'completed',
            'amount' => $amount,
        ];
    }

    public function handleWebhook(array $data): array
    {
        // Simulate webhook processing
        return [
            'payment_id' => $data['payment_id'] ?? null,
            'status' => $data['status'] ?? 'completed',
        ];
    }
}