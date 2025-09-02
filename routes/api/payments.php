<?php

/**
 * Cel: API routes for payment processing
 * Moduł: Payments
 * Odpowiedzialny: Claude-Code
 * Data: 2025-09-02
 */

use App\Http\Controllers\Api\PaymentsController;
use Illuminate\Support\Facades\Route;

Route::middleware(['api.key'])->prefix('payments')->name('api.payments.')->group(function () {
    Route::get('/', [PaymentsController::class, 'index'])->name('index');
    Route::post('/', [PaymentsController::class, 'store'])->name('store');
    Route::get('/{payment}', [PaymentsController::class, 'show'])->name('show');
    Route::post('/{payment}/process', [PaymentsController::class, 'process'])->name('process');
});

// Webhook endpoints (no rate limiting)
Route::prefix('payments/webhooks')->name('api.payments.webhooks.')->group(function () {
    Route::post('/paynow', [PaymentsController::class, 'paynowWebhook'])->name('paynow');
    Route::post('/stripe', [PaymentsController::class, 'stripeWebhook'])->name('stripe');
});
