<?php

declare(strict_types=1);

namespace App\Services\Payment;

use App\Models\Customer;
use App\Models\Payment;
use App\Services\Payment\Providers\PaymentProviderInterface;
use App\Services\Payment\Providers\PayNowProvider;
use App\Services\Payment\Providers\SimulationProvider;
use App\Services\Payment\Providers\StripeProvider;
use Exception;
use Illuminate\Support\Str;

class PaymentService
{
    private array $providers = [];

    public function __construct() {}

    /**
     * Create a new payment
     */
    public function createPayment(
        Customer $customer,
        float $amount,
        string $type = 'topup',
        array $metadata = []
    ): Payment {
        return Payment::create([
            'uuid' => Str::uuid(),
            'customer_id' => $customer->id,
            'amount' => $amount,
            'currency' => 'PLN',
            'type' => $type,
            'status' => 'pending',
            'provider' => $this->getDefaultProvider(),
            'metadata' => $metadata,
        ]);
    }

    /**
     * Process payment using configured provider
     */
    public function processPayment(Payment $payment): array
    {
        $provider = $this->getProvider($payment->provider);

        try {
            $result = $provider->createPayment($payment);

            // Update payment with provider data
            $payment->update([
                'provider_payment_id' => $result['payment_id'] ?? null,
                'provider_data' => $result['provider_data'] ?? [],
            ]);

            return [
                'success' => true,
                'payment_url' => $result['payment_url'] ?? null,
                'payment_id' => $payment->id,
            ];

        } catch (\Exception $e) {
            $payment->update([
                'status' => 'failed',
                'failure_reason' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Complete payment (called by webhooks)
     */
    public function completePayment(Payment $payment, array $data = []): bool
    {
        if ($payment->status !== 'pending') {
            return false;
        }

        $payment->update([
            'status' => 'completed',
            'completed_at' => now(),
            'provider_data' => array_merge($payment->provider_data ?? [], $data),
        ]);

        // Add balance to customer account
        if ($payment->type === 'topup') {
            $payment->customer->increment('balance', $payment->amount);
        }

        return true;
    }

    /**
     * Fail payment
     */
    public function failPayment(Payment $payment, string $reason = ''): bool
    {
        if ($payment->status !== 'pending') {
            return false;
        }

        $payment->update([
            'status' => 'failed',
            'failure_reason' => $reason,
        ]);

        return true;
    }

    /**
     * Get payment provider instance
     */
    private function getProvider(string $provider): PaymentProviderInterface
    {
        if (! isset($this->providers[$provider])) {
            $this->providers[$provider] = match ($provider) {
                'simulation' => new SimulationProvider,
                'paynow' => new PayNowProvider,
                'stripe' => $this->createStripeProvider(),
                default => new SimulationProvider,
            };
        }

        return $this->providers[$provider];
    }

    /**
     * Create Stripe provider with error handling
     */
    private function createStripeProvider(): PaymentProviderInterface
    {
        try {
            return new StripeProvider;
        } catch (Exception $e) {
            // If Stripe is not available, log the error and fallback to simulation
            \Log::warning('Stripe provider unavailable: '.$e->getMessage());

            return new SimulationProvider;
        }
    }

    /**
     * Get default payment provider based on configuration
     */
    private function getDefaultProvider(): string
    {
        // Check which providers are enabled in config
        if (config('services.paynow.enabled', false)) {
            return 'paynow';
        }

        if (config('services.stripe.enabled', false)) {
            return 'stripe';
        }

        // Default to simulation for development
        return 'simulation';
    }

    /**
     * Get available payment methods for customer
     */
    public function getAvailablePaymentMethods(Customer $customer): array
    {
        $methods = [];

        if (config('services.paynow.enabled', false)) {
            $methods[] = [
                'provider' => 'paynow',
                'name' => 'PayNow',
                'methods' => ['card', 'blik', 'bank_transfer'],
            ];
        }

        if (config('services.stripe.enabled', false)) {
            $methods[] = [
                'provider' => 'stripe',
                'name' => 'Stripe',
                'methods' => ['card'],
            ];
        }

        // Always include simulation for development
        if (config('app.debug') || config('services.payment_simulation.enabled', false)) {
            $methods[] = [
                'provider' => 'simulation',
                'name' => 'Symulacja (Test)',
                'methods' => ['simulation'],
            ];
        }

        return $methods;
    }
}
