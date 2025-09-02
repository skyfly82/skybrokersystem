<?php

/**
 * Cel: API routes for courier services management
 * Moduł: Couriers
 * Odpowiedzialny: Claude-Code
 * Data: 2025-09-02
 */

use App\Http\Controllers\Api\CouriersController;
use Illuminate\Support\Facades\Route;

// Public courier information
Route::prefix('couriers')->name('api.couriers.')->group(function () {
    Route::get('/', [CouriersController::class, 'index'])->name('index');
    Route::get('/{courier}/services', [CouriersController::class, 'services'])->name('services');
    Route::get('/points', [CouriersController::class, 'points'])->name('points');
    Route::get('/points/search', [CouriersController::class, 'searchPoints'])->name('points.search');
});
