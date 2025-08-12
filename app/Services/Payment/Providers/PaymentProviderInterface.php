<?php

declare(strict_types=1);

namespace App\Services\Payment\Providers;

use App\Models\Payment;

interface PaymentProviderInterface
{
    public function createPayment(Payment $payment): array;
    
    public function getPaymentStatus(string $externalId): array;
    
    public function refundPayment(Payment $payment, float $amount): array;
    
    public function handleWebhook(array $data): array;
}