<?php

use App\Http\Controllers\Api\InPostWebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| InPost Webhook Routes
|--------------------------------------------------------------------------
|
| Routes for handling InPost webhooks - tracking updates and shipment notifications
|
*/

Route::prefix('webhooks/inpost')->name('webhooks.inpost.')->group(function () {
    
    // Tracking status updates webhook
    Route::post('/tracking', [InPostWebhookController::class, 'trackingWebhook'])
        ->name('tracking');
    
    // Shipment creation/update webhook
    Route::post('/shipment', [InPostWebhookController::class, 'shipmentWebhook'])
        ->name('shipment');
    
    // Test endpoint for webhook functionality
    Route::post('/test', [InPostWebhookController::class, 'testWebhook'])
        ->name('test');
    
    // Health check for InPost webhooks
    Route::get('/health', function () {
        return response()->json([
            'status' => 'ok',
            'service' => 'inpost_webhooks',
            'timestamp' => now()->toISOString(),
            'version' => '1.0.0',
        ]);
    })->name('health');
    
});