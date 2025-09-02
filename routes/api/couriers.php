<?php

/**
 * Cel: API routes for courier services management
 * Moduł: Couriers
 * Odpowiedzialny: Claude-Code
 * Data: 2025-09-02
 */

use App\Http\Controllers\Api\Couriers\CourierController;
use App\Http\Controllers\Api\Couriers\CourierPointController;
use App\Http\Controllers\Api\Couriers\ShipmentController;
use Illuminate\Support\Facades\Route;

// Public courier information
Route::prefix('couriers')->name('api.couriers.')->group(function () {
    Route::get('/', [CourierController::class, 'index'])->name('index');
    Route::get('/{courier}/services', [CourierController::class, 'services'])->name('services');
    Route::get('/{courier}/points', [CourierPointController::class, 'index'])->name('points');
    Route::get('/points/search', [CourierPointController::class, 'search'])->name('points.search');
});

// Protected courier operations
Route::middleware(['api.key'])->prefix('couriers')->name('api.couriers.')->group(function () {
    // Shipment management
    Route::post('/shipments', [ShipmentController::class, 'store'])->name('shipments.store');
    Route::get('/shipments/{shipment}', [ShipmentController::class, 'show'])->name('shipments.show');
    Route::get('/shipments/{shipment}/label', [ShipmentController::class, 'downloadLabel'])->name('shipments.label');
    Route::post('/shipments/{shipment}/track', [ShipmentController::class, 'track'])->name('shipments.track');
});