<?php

/**
 * Cel: API routes for payment processing
 * Moduł: Payments
 * Odpowiedzialny: Claude-Code
 * Data: 2025-09-02
 */

use App\Http\Controllers\Api\Payments\PaymentController;
use App\Http\Controllers\Api\Payments\TransactionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['api.key'])->prefix('payments')->name('api.payments.')->group(function () {
    Route::get('/', [PaymentController::class, 'index'])->name('index');
    Route::post('/', [PaymentController::class, 'store'])->name('store');
    Route::get('/{payment}', [PaymentController::class, 'show'])->name('show');
    Route::post('/{payment}/process', [PaymentController::class, 'process'])->name('process');
    Route::post('/{payment}/cancel', [PaymentController::class, 'cancel'])->name('cancel');
    Route::post('/{payment}/refund', [PaymentController::class, 'refund'])->name('refund');
    
    // Transaction history
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/{transaction}', [TransactionController::class, 'show'])->name('transactions.show');
});

// Webhook endpoints (no rate limiting)
Route::prefix('payments/webhooks')->name('api.payments.webhooks.')->group(function () {
    Route::post('/paynow', [PaymentController::class, 'paynowWebhook'])->name('paynow');
    Route::post('/stripe', [PaymentController::class, 'stripeWebhook'])->name('stripe');
});