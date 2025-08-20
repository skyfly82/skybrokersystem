<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ShipmentsController;
use App\Http\Controllers\Api\PaymentsController;
use App\Http\Controllers\Api\CouriersController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\WebhooksController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

/*
|--------------------------------------------------------------------------
| API Version 1
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    
    /*
    |--------------------------------------------------------------------------
    | Public Routes
    |--------------------------------------------------------------------------
    */
    
    // API Health Check
    Route::get('/health', function () {
        return response()->json([
            'status' => 'ok',
            'version' => '1.0',
            'timestamp' => now()->toISOString(),
            'endpoints' => [
                'auth' => '/api/v1/auth',
                'shipments' => '/api/v1/shipments',
                'payments' => '/api/v1/payments',
                'couriers' => '/api/v1/couriers',
            ]
        ]);
    });
    
    // Authentication
    Route::prefix('auth')->group(function () {
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
        Route::get('/me', [AuthController::class, 'me'])->middleware('auth:sanctum');
    });
    
    // Public courier information
    Route::prefix('couriers')->group(function () {
        Route::get('/', [CouriersController::class, 'index']);
        Route::get('/{courier}', [CouriersController::class, 'show']);
        Route::get('/{courier}/services', [CouriersController::class, 'services']);
        Route::post('/{courier}/pickup-points', [CouriersController::class, 'pickupPoints']);
        Route::post('/{courier}/calculate-price', [CouriersController::class, 'calculatePrice']);
    });
    
    // Public tracking (no auth required)
    Route::get('/track/{trackingNumber}', [ShipmentsController::class, 'track']);
    
    /*
    |--------------------------------------------------------------------------
    | Protected Routes (API Key Authentication)
    |--------------------------------------------------------------------------
    */
    
    Route::middleware(['api.key', 'throttle:api'])->group(function () {
        
        // Customer Information
        Route::prefix('customer')->group(function () {
            Route::get('/', [CustomerController::class, 'show']);
            Route::get('/balance', [CustomerController::class, 'balance']);
            Route::get('/transactions', [CustomerController::class, 'transactions']);
            Route::get('/stats', [CustomerController::class, 'stats']);
        });
        
        // Shipments Management
        Route::prefix('shipments')->group(function () {
            Route::get('/', [ShipmentsController::class, 'index']);
            Route::post('/', [ShipmentsController::class, 'store']);
            Route::get('/{uuid}', [ShipmentsController::class, 'show']);
            Route::post('/{shipment}/cancel', [ShipmentsController::class, 'cancel']);
            Route::get('/{shipment}/label', [ShipmentsController::class, 'label']);
        });
        
        // Payments Management
        Route::prefix('payments')->group(function () {
            Route::get('/', [PaymentsController::class, 'index']);
            Route::post('/', [PaymentsController::class, 'store']);
            Route::get('/{payment}', [PaymentsController::class, 'show']);
        });
    });
    
    /*
    |--------------------------------------------------------------------------
    | Alternative Authentication (Sanctum)
    |--------------------------------------------------------------------------
    */
    
    Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
        
        // Same endpoints as API key auth, for web app integration
        Route::prefix('sanctum')->group(function () {
            
            // Customer Information
            Route::prefix('customer')->group(function () {
                Route::get('/', [CustomerController::class, 'show']);
                Route::get('/balance', [CustomerController::class, 'balance']);
                Route::get('/transactions', [CustomerController::class, 'transactions']);
                Route::get('/stats', [CustomerController::class, 'stats']);
            });
            
            // Shipments Management
            Route::prefix('shipments')->group(function () {
                Route::get('/', [ShipmentsController::class, 'index']);
                Route::post('/', [ShipmentsController::class, 'store']);
                Route::get('/{uuid}', [ShipmentsController::class, 'show']);
                Route::post('/{shipment}/cancel', [ShipmentsController::class, 'cancel']);
                Route::get('/{shipment}/label', [ShipmentsController::class, 'label']);
            });
            
            // Payments Management
            Route::prefix('payments')->group(function () {
                Route::get('/', [PaymentsController::class, 'index']);
                Route::post('/', [PaymentsController::class, 'store']);
                Route::get('/{payment}', [PaymentsController::class, 'show']);
            });
        });
    });
});

/*
|--------------------------------------------------------------------------
| Webhook Routes (No Authentication)
|--------------------------------------------------------------------------
*/

Route::prefix('webhooks')->name('webhooks.')->group(function () {
    
    // Payment Webhooks
    Route::post('/paynow', [WebhooksController::class, 'paynow'])->name('paynow');
    Route::post('/stripe', [WebhooksController::class, 'stripe'])->name('stripe');
    
    // Courier Webhooks
    Route::post('/inpost', [WebhooksController::class, 'inpost'])->name('inpost');
    Route::post('/dhl', [WebhooksController::class, 'dhl'])->name('dhl');
    Route::post('/dpd', [WebhooksController::class, 'dpd'])->name('dpd');
    Route::post('/gls', [WebhooksController::class, 'gls'])->name('gls');
    Route::post('/meest', [WebhooksController::class, 'meest'])->name('meest');
    Route::post('/fedex', [WebhooksController::class, 'fedex'])->name('fedex');
    Route::post('/ambro', [WebhooksController::class, 'ambro'])->name('ambro');
    Route::post('/packeta', [WebhooksController::class, 'packeta'])->name('packeta');
});

/*
|--------------------------------------------------------------------------
| API Documentation Routes
|--------------------------------------------------------------------------
*/

Route::get('/docs', function () {
    return response()->json([
        'name' => 'SkyBrokerSystem API',
        'version' => '1.0',
        'description' => 'Comprehensive API for courier shipment management',
        'documentation' => url('/api/v1/docs/swagger'),
        'authentication' => [
            'api_key' => [
                'type' => 'API Key',
                'header' => 'X-API-Key',
                'description' => 'Use your customer API key'
            ],
            'sanctum' => [
                'type' => 'Bearer Token',
                'header' => 'Authorization: Bearer {token}',
                'description' => 'Get token from /api/v1/auth/login'
            ]
        ],
        'rate_limits' => [
            'api' => '1000 requests per hour',
            'webhooks' => 'unlimited'
        ],
        'endpoints' => [
            'GET /api/v1/health' => 'API health check',
            'POST /api/v1/auth/login' => 'Authenticate with API key',
            'GET /api/v1/customer' => 'Get customer information',
            'GET /api/v1/shipments' => 'List shipments',
            'POST /api/v1/shipments' => 'Create new shipment',
            'GET /api/v1/shipments/{uuid}' => 'Get shipment details',
            'GET /api/v1/track/{trackingNumber}' => 'Track shipment (public)',
            'GET /api/v1/payments' => 'List payments',
            'POST /api/v1/payments' => 'Create payment',
            'GET /api/v1/couriers' => 'List available couriers',
            'POST /api/v1/couriers/{courier}/calculate-price' => 'Calculate shipping price',
        ],
        'examples' => [
            'create_shipment' => [
                'url' => 'POST /api/v1/shipments',
                'headers' => [
                    'X-API-Key' => 'sk_your_api_key_here',
                    'Content-Type' => 'application/json'
                ],
                'body' => [
                    'courier_code' => 'inpost',
                    'service_type' => 'inpost_locker_standard',
                    'sender' => [
                        'name' => 'Your Company',
                        'address' => 'ul. Przykładowa 1',
                        'city' => 'Warszawa',
                        'postal_code' => '00-001',
                        'phone' => '+48123456789',
                        'email' => 'sender@example.com'
                    ],
                    'recipient' => [
                        'name' => 'Jan Kowalski',
                        'phone' => '+48987654321',
                        'email' => 'recipient@example.com',
                        'pickup_point' => 'WAW01234'
                    ],
                    'package' => [
                        'weight' => 1.5,
                        'length' => 30,
                        'width' => 20,
                        'height' => 10
                    ],
                    'reference_number' => 'ORDER-2024-001',
                    'notes' => 'Handle with care'
                ]
            ],
            'track_shipment' => [
                'url' => 'GET /api/v1/track/1234567890123456',
                'response' => [
                    'success' => true,
                    'data' => [
                        'status' => 'delivered',
                        'events' => [
                            [
                                'date' => '2024-01-15T10:30:00Z',
                                'status' => 'delivered',
                                'description' => 'Package delivered',
                                'location' => 'Warszawa'
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ]);
})->name('docs');

// Swagger/OpenAPI documentation
Route::get('/docs/swagger', function () {
    return response()->json([
        'openapi' => '3.0.0',
        'info' => [
            'title' => 'SkyBrokerSystem API',
            'version' => '1.0.0',
            'description' => 'Comprehensive API for courier shipment management'
        ],
        'servers' => [
            ['url' => url('/api/v1')]
        ],
        'paths' => [
            // This would contain full OpenAPI specification
            // For now, returning basic structure
        ]
    ]);
})->name('docs.swagger');