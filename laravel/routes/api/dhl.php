<?php

/**
 * DHL Courier API Routes
 * Provides endpoints for DHL-specific courier operations
 * Author: SkyBrokerSystem Team
 * Date: 2025-09-02
 */

use App\Http\Controllers\Api\DhlController;
use Illuminate\Support\Facades\Route;

// Public DHL endpoints
Route::prefix('dhl')->name('api.dhl.')->group(function () {

    // Get available DHL services
    Route::get('/services', [DhlController::class, 'getServices'])
        ->name('services');

    // Calculate shipping price
    Route::post('/calculate-price', [DhlController::class, 'calculatePrice'])
        ->name('calculate-price');

    // Public tracking endpoint (no auth required)
    Route::get('/track/{trackingNumber}', [DhlController::class, 'trackShipment'])
        ->name('track')
        ->where('trackingNumber', '[A-Z0-9]+');

    // DHL webhook endpoint (public, but secured with signature)
    Route::post('/webhook', [DhlController::class, 'webhook'])
        ->name('webhook');
});

// Authenticated DHL endpoints
Route::prefix('dhl')->name('api.dhl.')->middleware(['auth:sanctum', 'api.key'])->group(function () {

    // Create shipment
    Route::post('/shipments', [DhlController::class, 'createShipment'])
        ->name('shipments.create');

    // Cancel shipment
    Route::delete('/shipments/{trackingNumber}', [DhlController::class, 'cancelShipment'])
        ->name('shipments.cancel')
        ->where('trackingNumber', '[A-Z0-9]+');

    // Get shipment label
    Route::get('/shipments/{trackingNumber}/label', [DhlController::class, 'getLabel'])
        ->name('shipments.label')
        ->where('trackingNumber', '[A-Z0-9]+');

    // Get detailed tracking information (authenticated)
    Route::get('/shipments/{trackingNumber}', [DhlController::class, 'trackShipment'])
        ->name('shipments.track')
        ->where('trackingNumber', '[A-Z0-9]+');
});
