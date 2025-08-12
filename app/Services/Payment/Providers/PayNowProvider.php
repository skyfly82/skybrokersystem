<?php

declare(strict_types=1);

namespace App\Services\Payment\Providers;

use App\Models\Payment;
use Illuminate\Support\Facades\Http;

class PayNowProvider implements PaymentProviderInterface
{
    private string $apiUrl;
    private string $apiKey;
    private string $signatureKey;

    public function __construct()
    {
        $this->apiUrl = config('payments.paynow.api_url');
        $this->apiKey = config('payments.paynow.api_key');
        $this->signatureKey = config('payments.paynow.signature_key');
    }

    public function createPayment(Payment $payment): array
    {
        $payload = [
            'amount' => (int)($payment->amount * 100), // PayNow expects amount in grosz
            'currency' => $payment->currency,
            'externalId' => $payment->uuid,
            'description' => $payment->description,
            'buyer' => [
                'email' => $payment->customer->email,
                'firstName' => $payment->customer->company_name,
                'lastName' => '',
                'phone' => $payment->customer->phone,
            ],
            'continueUrl' => route('payments.return', ['payment' => $payment->uuid]),
            'notifyUrl' => route('webhooks.paynow'),
        ];

        $response = Http::withHeaders([
            'Api-Key' => $this->apiKey,
            'Content-Type' => 'application/json',
            'Signature' => $this->generateSignature($payload),
        ])->post($this->apiUrl . '/v1/payments', $payload);

        if (!$response->successful()) {
            throw new \Exception('PayNow API Error: ' . $response->body());
        }

        $data = $response->json();

        return [
            'external_id' => $data['paymentId'],
            'status' => 'pending',
            'payment_url' => $data['redirectUrl'],
            'metadata' => [
                'paynow_payment_id' => $data['paymentId'],
                'created_at' => now()->toISOString(),
            ]
        ];
    }

    public function getPaymentStatus(string $externalId): array
    {
        $response = Http::withHeaders([
            'Api-Key' => $this->apiKey,
        ])->get($this->apiUrl . "/v1/payments/{$externalId}/status");

        if (!$response->successful()) {
            throw new \Exception('PayNow status check failed');
        }

        $data = $response->json();

        return [
            'status' => $this->mapStatus($data['status']),
            'external_id' => $externalId,
        ];
    }

    public function refundPayment(Payment $payment, float $amount): array
    {
        $payload = [
            'amount' => (int)($amount * 100),
            'reason' => 'Customer refund request',
        ];

        $response = Http::withHeaders([
            'Api-Key' => $this->apiKey,
            'Content-Type' => 'application/json',
            'Signature' => $this->generateSignature($payload),
        ])->post($this->apiUrl . "/v1/payments/{$payment->external_id}/refunds", $payload);

        if (!$response->successful()) {
            throw new \Exception('PayNow refund failed: ' . $response->body());
        }

        $data = $response->json();

        return [
            'external_id' => $data['refundId'],
            'status' => 'completed',
            'amount' => $amount,
        ];
    }

    public function handleWebhook(array $data): array
    {
        // Verify webhook signature
        if (!$this->verifyWebhookSignature($data)) {
            throw new \Exception('Invalid webhook signature');
        }

        return [
            'payment_id' => $data['paymentId'],
            'status' => $this->mapStatus($data['status']),
            'failure_reason' => $data['failureReason'] ?? null,
        ];
    }

    private function generateSignature(array $data): string
    {
        $dataString = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return base64_encode(hash_hmac('sha256', $dataString, $this->signatureKey, true));
    }

    private function verifyWebhookSignature(array $data): bool
    {
        $receivedSignature = request()->header('Signature');
        $expectedSignature = $this->generateSignature($data);
        
        return hash_equals($expectedSignature, $receivedSignature);
    }

    private function mapStatus(string $paynowStatus): string
    {
        return match($paynowStatus) {
            'NEW', 'PENDING' => 'pending',
            'CONFIRMED' => 'completed',
            'REJECTED', 'ERROR' => 'failed',
            'EXPIRED' => 'cancelled',
            default => 'pending',
        };
    }
}