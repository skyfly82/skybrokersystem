<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\CustomersController as AdminCustomersController;
use App\Http\Controllers\Admin\ShipmentsController as AdminShipmentsController;
use App\Http\Controllers\Admin\PaymentsController as AdminPaymentsController;
use App\Http\Controllers\Admin\NotificationsController as AdminNotificationsController;
use App\Http\Controllers\Customer\AuthController as CustomerAuthController;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboardController;
use App\Http\Controllers\Customer\ShipmentsController as CustomerShipmentsController;
use App\Http\Controllers\Customer\PaymentsController as CustomerPaymentsController;
use App\Http\Controllers\Customer\ProfileController as CustomerProfileController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Main redirect
Route::get('/', function () {
    if (auth()->guard('system_user')->check()) {
        return redirect()->route('admin.dashboard');
    }
    
    if (auth()->guard('customer_user')->check()) {
        return redirect()->route('customer.dashboard');
    }
    
    return redirect()->route('customer.login');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->group(function () {
    // Admin Authentication Routes
    Route::middleware('guest:system_user')->group(function () {
        Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AdminAuthController::class, 'login']);
    });
    
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
    
    // Protected Admin Routes
    Route::middleware(['auth:system_user', 'admin'])->group(function () {
        // Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/stats', [AdminDashboardController::class, 'stats'])->name('dashboard.stats');
        
        // Customers Management
        Route::prefix('customers')->name('customers.')->group(function () {
            Route::get('/', [AdminCustomersController::class, 'index'])->name('index');
            Route::get('/create', [AdminCustomersController::class, 'create'])->name('create');
            Route::post('/', [AdminCustomersController::class, 'store'])->name('store');
            Route::get('/{customer}', [AdminCustomersController::class, 'show'])->name('show');
            Route::get('/{customer}/edit', [AdminCustomersController::class, 'edit'])->name('edit');
            Route::put('/{customer}', [AdminCustomersController::class, 'update'])->name('update');
            Route::delete('/{customer}', [AdminCustomersController::class, 'destroy'])->name('destroy');
            
            // Customer Actions
            Route::post('/{customer}/approve', [AdminCustomersController::class, 'approve'])->name('approve');
            Route::post('/{customer}/suspend', [AdminCustomersController::class, 'suspend'])->name('suspend');
            Route::post('/{customer}/regenerate-api-key', [AdminCustomersController::class, 'regenerateApiKey'])->name('regenerate-api-key');
            Route::post('/{customer}/add-balance', [AdminCustomersController::class, 'addBalance'])->name('add-balance');
        });
        
        // Shipments Management
        Route::prefix('shipments')->name('shipments.')->group(function () {
            Route::get('/', [AdminShipmentsController::class, 'index'])->name('index');
            Route::get('/{shipment}', [AdminShipmentsController::class, 'show'])->name('show');
            Route::post('/{shipment}/update-status', [AdminShipmentsController::class, 'updateStatus'])->name('update-status');
        });
        
        // Payments Management
        Route::prefix('payments')->name('payments.')->group(function () {
            Route::get('/', [AdminPaymentsController::class, 'index'])->name('index');
            Route::get('/{payment}', [AdminPaymentsController::class, 'show'])->name('show');
            Route::post('/{payment}/refund', [AdminPaymentsController::class, 'refund'])->name('refund');
        });
        
        // Notifications Management
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [AdminNotificationsController::class, 'index'])->name('index');
            Route::get('/templates', [AdminNotificationsController::class, 'templates'])->name('templates');
            Route::get('/templates/create', [AdminNotificationsController::class, 'createTemplate'])->name('templates.create');
            Route::post('/templates', [AdminNotificationsController::class, 'storeTemplate'])->name('templates.store');
            Route::get('/templates/{template}/edit', [AdminNotificationsController::class, 'editTemplate'])->name('templates.edit');
            Route::put('/templates/{template}', [AdminNotificationsController::class, 'updateTemplate'])->name('templates.update');
            Route::post('/test', [AdminNotificationsController::class, 'testNotification'])->name('test');
        });
        
        // Courier Services (placeholder routes)
        Route::prefix('couriers')->name('couriers.')->group(function () {
            Route::get('/', function () { return view('admin.couriers.index'); })->name('index');
        });
        
        // Reports (placeholder routes)
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', function () { return view('admin.reports.index'); })->name('index');
        });
        
        // Super Admin Only Routes
        Route::middleware('admin:super_admin')->group(function () {
            // System Users Management
            Route::prefix('users')->name('users.')->group(function () {
                Route::get('/', function () { return view('admin.users.index'); })->name('index');
            });
            
            // System Settings
            Route::prefix('settings')->name('settings.')->group(function () {
                Route::get('/', function () { return view('admin.settings.index'); })->name('index');
            });
            
            // System Logs
            Route::prefix('logs')->name('logs.')->group(function () {
                Route::get('/', function () { return view('admin.logs.index'); })->name('index');
            });
        });
    });
});

/*
|--------------------------------------------------------------------------
| Customer Routes
|--------------------------------------------------------------------------
*/

Route::prefix('customer')->name('customer.')->group(function () {
    // Customer Authentication Routes
    Route::middleware('guest:customer_user')->group(function () {
        Route::get('/login', [CustomerAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [CustomerAuthController::class, 'login']);
        Route::get('/register', [CustomerAuthController::class, 'showRegistrationForm'])->name('register');
        Route::post('/register', [CustomerAuthController::class, 'register']);
    });
    
    Route::post('/logout', [CustomerAuthController::class, 'logout'])->name('logout');
    
    // Protected Customer Routes
    Route::middleware(['auth:customer_user', 'customer.active'])->group(function () {
        // Dashboard
        Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');
        
        // Shipments Management
        Route::prefix('shipments')->name('shipments.')->group(function () {
            Route::get('/', [CustomerShipmentsController::class, 'index'])->name('index');
            Route::get('/create', [CustomerShipmentsController::class, 'create'])->name('create');
            Route::post('/', [CustomerShipmentsController::class, 'store'])->name('store');
            Route::get('/{shipment}', [CustomerShipmentsController::class, 'show'])->name('show');
            Route::get('/{shipment}/track', [CustomerShipmentsController::class, 'track'])->name('track');
            Route::get('/{shipment}/label', [CustomerShipmentsController::class, 'label'])->name('label');
            Route::post('/{shipment}/cancel', [CustomerShipmentsController::class, 'cancel'])->name('cancel');
            
            // AJAX endpoints for shipment creation
            Route::post('/calculate-price', [CustomerShipmentsController::class, 'calculatePrice'])->name('calculate-price');
            Route::post('/pickup-points', [CustomerShipmentsController::class, 'getPickupPoints'])->name('pickup-points');
        });
        
        // Payments Management
        Route::prefix('payments')->name('payments.')->group(function () {
            Route::get('/', [CustomerPaymentsController::class, 'index'])->name('index');
            Route::get('/{payment}', [CustomerPaymentsController::class, 'show'])->name('show');
            Route::get('/topup/create', [CustomerPaymentsController::class, 'topup'])->name('topup');
            Route::post('/topup', [CustomerPaymentsController::class, 'processTopup'])->name('topup.process');
        });
        
        // Profile Management
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [CustomerProfileController::class, 'show'])->name('show');
            Route::get('/edit', [CustomerProfileController::class, 'edit'])->name('edit');
            Route::put('/', [CustomerProfileController::class, 'update'])->name('update');
            Route::put('/password', [CustomerProfileController::class, 'updatePassword'])->name('update-password');
            Route::get('/notifications', [CustomerProfileController::class, 'notifications'])->name('notifications');
            Route::put('/notifications', [CustomerProfileController::class, 'updateNotifications'])->name('update-notifications');
        });
        
        // Users Management (for admin users only)
        Route::middleware('customer.admin')->prefix('users')->name('users.')->group(function () {
            Route::get('/', function () { return view('customer.users.index'); })->name('index');
            Route::get('/create', function () { return view('customer.users.create'); })->name('create');
            Route::post('/', function () { return redirect()->route('customer.users.index'); })->name('store');
            Route::get('/{user}/edit', function () { return view('customer.users.edit'); })->name('edit');
            Route::put('/{user}', function () { return redirect()->route('customer.users.index'); })->name('update');
            Route::delete('/{user}', function () { return redirect()->route('customer.users.index'); })->name('destroy');
        });
    });
});

/*
|--------------------------------------------------------------------------
| Payment Return Routes (Public)
|--------------------------------------------------------------------------
*/

Route::prefix('payments')->name('payments.')->group(function () {
    Route::get('/{payment}/return', function () {
        return view('payments.return');
    })->name('return');
    
    Route::get('/{payment}/simulation', function () {
        return view('payments.simulation');
    })->name('simulation');
});

/*
|--------------------------------------------------------------------------
| Public Tracking
|--------------------------------------------------------------------------
*/

Route::get('/track/{trackingNumber}', function ($trackingNumber) {
    return view('public.track', compact('trackingNumber'));
})->name('public.track');

/*
|--------------------------------------------------------------------------
| Health Check
|--------------------------------------------------------------------------
*/

Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toISOString(),
        'version' => config('app.version', '6.0.0'),
    ]);
})->name('health');