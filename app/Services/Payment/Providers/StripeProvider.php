<?php

declare(strict_types=1);

namespace App\Services\Payment\Providers;

use App\Models\Payment;
use Exception;

class StripeProvider implements PaymentProviderInterface
{
    private $stripe;

    public function __construct()
    {
        if (!class_exists('\Stripe\StripeClient')) {
            throw new Exception('Stripe PHP SDK is not installed. Run: composer require stripe/stripe-php');
        }
        
        $this->stripe = new \Stripe\StripeClient(config('payments.providers.stripe.secret_key', ''));
    }

    public function createPayment(Payment $payment): array
    {
        try {
            $paymentIntent = $this->stripe->paymentIntents->create([
                'amount' => (int)($payment->amount * 100), // Stripe expects amount in cents
                'currency' => strtolower($payment->currency),
                'metadata' => [
                    'payment_uuid' => $payment->uuid,
                    'customer_id' => $payment->customer_id,
                ],
                'description' => $payment->description,
                'receipt_email' => $payment->customer->email,
            ]);

            return [
                'external_id' => $paymentIntent->id,
                'status' => 'pending',
                'payment_url' => null, // Would be handled by frontend
                'metadata' => [
                    'client_secret' => $paymentIntent->client_secret,
                    'stripe_payment_intent_id' => $paymentIntent->id,
                ]
            ];

        } catch (ApiErrorException $e) {
            throw new \Exception('Stripe API Error: ' . $e->getMessage());
        }
    }

    public function getPaymentStatus(string $externalId): array
    {
        try {
            $paymentIntent = $this->stripe->paymentIntents->retrieve($externalId);

            return [
                'status' => $this->mapStatus($paymentIntent->status),
                'external_id' => $externalId,
            ];

        } catch (ApiErrorException $e) {
            throw new \Exception('Stripe status check failed: ' . $e->getMessage());
        }
    }

    public function refundPayment(Payment $payment, float $amount): array
    {
        try {
            $refund = $this->stripe->refunds->create([
                'payment_intent' => $payment->external_id,
                'amount' => (int)($amount * 100),
                'reason' => 'requested_by_customer',
                'metadata' => [
                    'original_payment_uuid' => $payment->uuid,
                ]
            ]);

            return [
                'external_id' => $refund->id,
                'status' => 'completed',
                'amount' => $amount,
            ];

        } catch (ApiErrorException $e) {
            throw new \Exception('Stripe refund failed: ' . $e->getMessage());
        }
    }

    public function handleWebhook(array $data): array
    {
        $event = $data['type'] ?? null;
        $paymentIntent = $data['data']['object'] ?? null;

        if (!$paymentIntent || !isset($paymentIntent['id'])) {
            throw new \Exception('Invalid Stripe webhook data');
        }

        $status = match($event) {
            'payment_intent.succeeded' => 'completed',
            'payment_intent.payment_failed' => 'failed',
            'payment_intent.canceled' => 'cancelled',
            default => 'pending',
        };

        return [
            'payment_id' => $paymentIntent['id'],
            'status' => $status,
            'failure_reason' => $paymentIntent['last_payment_error']['message'] ?? null,
        ];
    }

    private function mapStatus(string $stripeStatus): string
    {
        return match($stripeStatus) {
            'succeeded' => 'completed',
            'requires_payment_method', 'requires_confirmation', 'requires_action' => 'pending',
            'processing' => 'processing',
            'canceled' => 'cancelled',
            default => 'failed',
        };
    }
}