<?php

/**
 * Cel: Główny punkt wejścia dla API - zawiera tylko podstawowe trasy
 * Moduł: Core API
 * Odpowiedzialny: sky_fly82
 * Data: 2025-09-02
 */

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Health check endpoint
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toISOString(),
        'version' => config('app.version', '1.0.0'),
    ]);
});

// Basic user info endpoint
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Load modular API routes
$apiRoutes = glob(base_path('routes/api/*.php'));
foreach ($apiRoutes as $route) {
    $routeName = basename($route, '.php');
    if ($routeName !== 'api') { // Skip the main api.php file
        Route::prefix($routeName)->group($route);
    }
}
