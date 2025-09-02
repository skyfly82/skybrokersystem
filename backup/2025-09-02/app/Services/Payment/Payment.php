<?php

declare(strict_types=1);

namespace App\Services\Payment;

use App\Models\Customer;
use App\Models\Payment;
use App\Models\Shipment;
use App\Services\Payment\Providers\PaymentProviderInterface;
use App\Services\Payment\Providers\SimulationProvider;
use App\Services\Payment\Providers\PayNowProvider;
use App\Services\Payment\Providers\StripeProvider;
use App\Exceptions\PaymentException;
use App\Events\PaymentCompleted;
use App\Events\PaymentFailed;

class PaymentService
{
    private array $providers = [
        'simulation' => SimulationProvider::class,
        'paynow' => PayNowProvider::class,
        'stripe' => StripeProvider::class,
    ];

    public function createPayment(
        Customer $customer,
        float $amount,
        string $type = 'topup',
        string $method = 'simulation',
        ?object $payable = null,
        array $metadata = []
    ): Payment {
        $payment = Payment::create([
            'customer_id' => $customer->id,
            'payable_type' => $payable ? get_class($payable) : null,
            'payable_id' => $payable?->id,
            'type' => $type,
            'method' => $method,
            'provider' => $this->getProviderForMethod($method),
            'amount' => $amount,
            'status' => 'pending',
            'description' => $this->generateDescription($type, $payable),
            'expires_at' => now()->addHours(24),
        ]);

        return $payment;
    }

    public function processPayment(Payment $payment): array
    {
        $provider = $this->getProvider($payment->provider);
        
        try {
            $result = $provider->createPayment($payment);
            
            $payment->update([
                'external_id' => $result['external_id'] ?? null,
                'provider_data' => $result['metadata'] ?? null,
                'status' => $result['status'] ?? 'processing',
            ]);

            return [
                'success' => true,
                'payment_url' => $result['payment_url'] ?? null,
                'redirect_url' => $result['redirect_url'] ?? null,
            ];
        } catch (\Exception $e) {
            $payment->markAsFailed($e->getMessage());
            throw new PaymentException($e->getMessage());
        }
    }

    public function handleWebhook(string $provider, array $data): void
    {
        $providerInstance = $this->getProvider($provider);
        $result = $providerInstance->handleWebhook($data);

        if (!$result['payment_id']) {
            throw new PaymentException('Payment ID not found in webhook');
        }

        $payment = Payment::where('external_id', $result['payment_id'])->first();
        
        if (!$payment) {
            throw new PaymentException('Payment not found');
        }

        switch ($result['status']) {
            case 'completed':
                $payment->markAsCompleted();
                event(new PaymentCompleted($payment));
                break;
                
            case 'failed':
                $payment->markAsFailed($result['failure_reason'] ?? 'Payment failed');
                event(new PaymentFailed($payment));
                break;
        }
    }

    public function refundPayment(Payment $payment, float $amount = null): Payment
    {
        if (!$payment->isCompleted()) {
            throw new PaymentException('Can only refund completed payments');
        }

        $refundAmount = $amount ?? $payment->amount;
        $provider = $this->getProvider($payment->provider);

        $refund = Payment::create([
            'customer_id' => $payment->customer_id,
            'type' => 'refund',
            'method' => $payment->method,
            'provider' => $payment->provider,
            'amount' => -$refundAmount,
            'status' => 'pending',
            'description' => "Refund for payment {$payment->uuid}",
        ]);

        try {
            $result = $provider->refundPayment($payment, $refundAmount);
            
            $refund->update([
                'external_id' => $result['external_id'],
                'status' => 'completed',
            ]);

            // Deduct from customer balance
            $payment->customer->deductBalance($refundAmount, "Refund {$refund->uuid}");

        } catch (\Exception $e) {
            $refund->markAsFailed($e->getMessage());
            throw new PaymentException($e->getMessage());
        }

        return $refund;
    }

    private function getProvider(string $provider): PaymentProviderInterface
    {
        if (!isset($this->providers[$provider])) {
            throw new PaymentException("Unsupported payment provider: {$provider}");
        }

        return app($this->providers[$provider]);
    }

    private function getProviderForMethod(string $method): string
    {
        return match($method) {
            'card', 'blik' => config('payments.default_card_provider', 'paynow'),
            'bank_transfer' => config('payments.default_bank_provider', 'paynow'),
            'paypal' => 'stripe',
            'simulation' => 'simulation',
            default => 'simulation',
        };
    }

    private function generateDescription(string $type, ?object $payable): string
    {
        return match($type) {
            'shipment' => "Payment for shipment {$payable->uuid}",
            'topup' => 'Account top-up',
            'subscription' => 'Subscription payment',
            default => 'Payment',
        };
    }
}