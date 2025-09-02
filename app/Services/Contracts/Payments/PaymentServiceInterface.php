<?php

/**
 * Cel: Kontrakt dla serwisu płatności
 * Moduł: Payments
 * Odpowiedzialny: Claude-Code
 * Data: 2025-09-02
 */

namespace App\Services\Contracts\Payments;

use App\Models\Payment;
use App\Models\Transaction;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface PaymentServiceInterface
{
    /**
     * Get paginated payments for a customer
     */
    public function getPaymentsForCustomer(int $customerId, array $filters = []): LengthAwarePaginator;

    /**
     * Create new payment
     */
    public function createPayment(array $data): Payment;

    /**
     * Process payment
     */
    public function processPayment(Payment $payment): Payment;

    /**
     * Cancel payment
     */
    public function cancelPayment(Payment $payment, ?string $reason = null): Payment;

    /**
     * Refund payment
     */
    public function refundPayment(Payment $payment, ?float $amount = null): Payment;

    /**
     * Handle payment webhook
     */
    public function handleWebhook(string $provider, array $data): bool;

    /**
     * Get transaction history
     */
    public function getTransactionHistory(int $customerId, array $filters = []): LengthAwarePaginator;

    /**
     * Validate payment data
     */
    public function validatePaymentData(array $data): array;
}
