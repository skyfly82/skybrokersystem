<?php

/**
 * Cel: API routes for order management
 * Moduł: Orders
 * Odpowiedzialny: Claude-Code
 * Data: 2025-09-02
 */

use App\Http\Controllers\Api\Orders\OrderController;
use Illuminate\Support\Facades\Route;

Route::middleware(['api.key'])->prefix('orders')->name('api.orders.')->group(function () {
    Route::get('/', [OrderController::class, 'index'])->name('index');
    Route::post('/', [OrderController::class, 'store'])->name('store');
    Route::get('/{order}', [OrderController::class, 'show'])->name('show');
    Route::put('/{order}', [OrderController::class, 'update'])->name('update');
    Route::delete('/{order}', [OrderController::class, 'destroy'])->name('destroy');

    // Order status management
    Route::patch('/{order}/status', [OrderController::class, 'updateStatus'])->name('update-status');
    Route::post('/{order}/cancel', [OrderController::class, 'cancel'])->name('cancel');
});
