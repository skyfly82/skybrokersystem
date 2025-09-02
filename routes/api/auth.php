<?php

/**
 * Cel: API routes for authentication
 * Moduł: Auth
 * Odpowiedzialny: Claude-Code
 * Data: 2025-09-02
 */

use App\Http\Controllers\Api\Auth\ApiKeyController;
use App\Http\Controllers\Api\Auth\AuthController;
use Illuminate\Support\Facades\Route;

// Authentication endpoints
Route::prefix('auth')->name('api.auth.')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('forgot-password');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('reset-password');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('/me', [AuthController::class, 'me'])->name('me');
        Route::put('/profile', [AuthController::class, 'updateProfile'])->name('update-profile');
        Route::post('/change-password', [AuthController::class, 'changePassword'])->name('change-password');
    });
});

// API Key management
Route::middleware(['api.key'])->prefix('api-keys')->name('api.keys.')->group(function () {
    Route::get('/', [ApiKeyController::class, 'index'])->name('index');
    Route::post('/', [ApiKeyController::class, 'store'])->name('store');
    Route::delete('/{apiKey}', [ApiKeyController::class, 'destroy'])->name('destroy');
    Route::post('/{apiKey}/regenerate', [ApiKeyController::class, 'regenerate'])->name('regenerate');
});
