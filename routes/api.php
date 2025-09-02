<?php

/**
 * Cel: Główny punkt wejścia dla API - zawiera tylko podstawowe trasy
 * Moduł: Core API
 * Odpowiedzialny: Claude-Code
 * Data: 2025-09-02
 */

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Health check endpoint
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toISOString(),
        'version' => config('app.version', '6.0'),
    ]);
});

// Basic user info endpoint
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
