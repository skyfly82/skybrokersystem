<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\CustomersController as AdminCustomersController;
use App\Http\Controllers\Admin\SystemSettingsController as AdminSystemSettingsController;
use App\Http\Controllers\Admin\ShipmentsController as AdminShipmentsController;
use App\Http\Controllers\Admin\PaymentsController as AdminPaymentsController;
use App\Http\Controllers\Admin\NotificationsController as AdminNotificationsController;
use App\Http\Controllers\Admin\UsersController as AdminUsersController;
use App\Http\Controllers\Customer\AuthController as CustomerAuthController;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboardController;
use App\Http\Controllers\Customer\ShipmentsController as CustomerShipmentsController;
use App\Http\Controllers\Customer\PaymentsController as CustomerPaymentsController;
use App\Http\Controllers\Customer\ProfileController as CustomerProfileController;
use App\Http\Controllers\Customer\UsersController as CustomerUsersController;

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

// Home page route
Route::get('/', function () {
    return view('home');
})->name('home');

// Dashboard redirect route for authenticated users
Route::get('/dashboard', function () {
    if (auth()->guard('system_user')->check()) {
        return redirect()->route('admin.dashboard');
    }
    
    if (auth()->guard('customer_user')->check()) {
        return redirect()->route('customer.dashboard');
    }
    
    return redirect()->route('home');
})->name('dashboard.redirect');

// Language switching route
Route::get('/language/{locale}', function ($locale) {
    if (!in_array($locale, array_keys(config('app.supported_locales')))) {
        abort(404);
    }
    
    session()->put('locale', $locale);
    app()->setLocale($locale);
    
    return redirect()->back();
})->name('language.switch');

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
            Route::get('/create', [AdminShipmentsController::class, 'create'])->name('create');
            Route::post('/', [AdminShipmentsController::class, 'store'])->name('store');
            Route::get('/{shipment}', [AdminShipmentsController::class, 'show'])->name('show');
            Route::get('/{shipment}/edit', [AdminShipmentsController::class, 'edit'])->name('edit');
            Route::put('/{shipment}', [AdminShipmentsController::class, 'update'])->name('update');
            Route::delete('/{shipment}', [AdminShipmentsController::class, 'destroy'])->name('destroy');
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
        
        // Reports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', function () { 
                return view('admin.reports.index', [
                    'title' => 'Reports Dashboard',
                    'description' => 'View system reports and analytics'
                ]); 
            })->name('index');
            
            Route::get('/shipments', function () { 
                return view('admin.reports.shipments', [
                    'title' => 'Shipments Report',
                    'description' => 'Detailed shipments analytics'
                ]); 
            })->name('shipments');
            
            Route::get('/payments', function () { 
                return view('admin.reports.payments', [
                    'title' => 'Payments Report',
                    'description' => 'Financial reports and payment analytics'
                ]); 
            })->name('payments');
            
            Route::get('/customers', function () { 
                return view('admin.reports.customers', [
                    'title' => 'Customers Report',
                    'description' => 'Customer analytics and statistics'
                ]); 
            })->name('customers');
        });
        
        // Courier Services
        Route::prefix('couriers')->name('couriers.')->group(function () {
            Route::get('/', function () { 
                return view('admin.couriers.index', [
                    'title' => 'Courier Services',
                    'description' => 'Manage courier integrations and settings'
                ]); 
            })->name('index');
            
            Route::get('/settings', function () { 
                return view('admin.couriers.settings', [
                    'title' => 'Courier Settings',
                    'description' => 'Configure courier API settings'
                ]); 
            })->name('settings');
        });
        
        // System Settings - WSZYSTKIE POTRZEBNE ROUTY
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', function () { 
                return view('admin.settings.index', [
                    'title' => 'System Settings',
                    'description' => 'Configure system-wide settings'
                ]); 
            })->name('index');
            
            // DODANA BRAKUJĄCA RUTA!
            Route::get('/general', function () { 
                return view('admin.settings.general', [
                    'title' => 'General Settings',
                    'description' => 'General system configuration'
                ]); 
            })->name('general');
            
            Route::get('/system', [AdminSystemSettingsController::class, 'index'])->name('system');
            Route::post('/system', [AdminSystemSettingsController::class, 'update'])->name('system.update');
            
            Route::get('/verification', [AdminSystemSettingsController::class, 'verification'])->name('verification');
            Route::post('/verification', [AdminSystemSettingsController::class, 'updateVerification'])->name('verification.update');
            Route::post('/test-email', [AdminSystemSettingsController::class, 'testEmail'])->name('test-email');
            
            Route::get('/courier', function () { 
                return view('admin.settings.courier', [
                    'title' => 'Courier Settings',
                    'description' => 'Courier service configuration'
                ]); 
            })->name('courier');
            
            Route::get('/payment', function () { 
                return view('admin.settings.payment', [
                    'title' => 'Payment Settings',
                    'description' => 'Payment gateway configuration'
                ]); 
            })->name('payment');
            
            Route::get('/notifications', function () { 
                return view('admin.settings.notifications', [
                    'title' => 'Notification Settings',
                    'description' => 'Configure email and SMS notifications'
                ]); 
            })->name('notifications');
            
            Route::get('/security', function () { 
                return view('admin.settings.security', [
                    'title' => 'Security Settings',
                    'description' => 'Security and authentication settings'
                ]); 
            })->name('security');
            
            Route::get('/api', function () { 
                return view('admin.settings.api', [
                    'title' => 'API Settings',
                    'description' => 'API configuration and keys'
                ]); 
            })->name('api');
            
            Route::get('/maintenance', function () { 
                return view('admin.settings.maintenance', [
                    'title' => 'Maintenance Settings',
                    'description' => 'System maintenance and updates'
                ]); 
            })->name('maintenance');
        });
        
        // Employees Management (Admin and Super Admin)
        Route::prefix('employees')->name('employees.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\EmployeesController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\Admin\EmployeesController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\Admin\EmployeesController::class, 'store'])->name('store');
            Route::get('/{employee}', [App\Http\Controllers\Admin\EmployeesController::class, 'show'])->name('show');
            Route::get('/{employee}/edit', [App\Http\Controllers\Admin\EmployeesController::class, 'edit'])->name('edit');
            Route::put('/{employee}', [App\Http\Controllers\Admin\EmployeesController::class, 'update'])->name('update');
            Route::delete('/{employee}', [App\Http\Controllers\Admin\EmployeesController::class, 'destroy'])->name('destroy');
        });
        
        // Permissions Management (Super Admin Only)
        Route::middleware('admin:super_admin')->prefix('permissions')->name('permissions.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\PermissionsController::class, 'index'])->name('index');
            Route::post('/', [App\Http\Controllers\Admin\PermissionsController::class, 'update'])->name('update');
        });
        
        // System Users Management (Super Admin Only)
        Route::middleware('admin:super_admin')->prefix('users')->name('users.')->group(function () {
            Route::get('/', [AdminUsersController::class, 'index'])->name('index');
            Route::get('/create', [AdminUsersController::class, 'create'])->name('create');
            Route::post('/', [AdminUsersController::class, 'store'])->name('store');
            Route::get('/{user}', [AdminUsersController::class, 'show'])->name('show');
            Route::get('/{user}/edit', [AdminUsersController::class, 'edit'])->name('edit');
            Route::put('/{user}', [AdminUsersController::class, 'update'])->name('update');
            Route::delete('/{user}', [AdminUsersController::class, 'destroy'])->name('destroy');
            Route::patch('/{user}/status', [AdminUsersController::class, 'updateStatus'])->name('update-status');
        });
        
        // System Logs
        Route::prefix('logs')->name('logs.')->group(function () {
            Route::get('/', function () { 
                return view('admin.logs.index', [
                    'title' => 'System Logs',
                    'description' => 'View system activity and error logs'
                ]); 
            })->name('index');
            
            Route::get('/errors', function () { 
                return view('admin.logs.errors', [
                    'title' => 'Error Logs',
                    'description' => 'System error logs'
                ]); 
            })->name('errors');
            
            Route::get('/access', function () { 
                return view('admin.logs.access', [
                    'title' => 'Access Logs',
                    'description' => 'User access logs'
                ]); 
            })->name('access');
        });
        
        // Analytics & Statistics
        Route::prefix('analytics')->name('analytics.')->group(function () {
            Route::get('/', function () { 
                return view('admin.analytics.index', [
                    'title' => 'Analytics Dashboard',
                    'description' => 'System analytics and insights'
                ]); 
            })->name('index');
            
            Route::get('/shipments', function () { 
                return view('admin.analytics.shipments', [
                    'title' => 'Shipment Analytics',
                    'description' => 'Detailed shipment analytics'
                ]); 
            })->name('shipments');
            
            Route::get('/revenue', function () { 
                return view('admin.analytics.revenue', [
                    'title' => 'Revenue Analytics',
                    'description' => 'Financial performance analytics'
                ]); 
            })->name('revenue');
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
        
        // Verification routes
        Route::get('/verify/{token}', [CustomerAuthController::class, 'showVerifyForm'])->name('verify');
        Route::post('/verify/{token}', [CustomerAuthController::class, 'verify']);
        Route::post('/verify/{token}/resend', [CustomerAuthController::class, 'resendCode'])->name('verify.resend');
    });
    
    Route::post('/logout', [CustomerAuthController::class, 'logout'])->name('logout');
    
    // Pending approval route (only for authenticated but not active customers)  
    Route::get('/pending', [CustomerAuthController::class, 'showPendingForm'])->name('pending')->middleware('auth:customer_user');
    
    // Protected Customer Routes
    Route::middleware(['auth:customer_user', 'customer.active'])->group(function () {
        // Dashboard
        Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');
        
        // Shipments Management
        Route::prefix('shipments')->name('shipments.')->group(function () {
            Route::get('/', [CustomerShipmentsController::class, 'index'])->name('index');
            Route::get('/cart', [CustomerShipmentsController::class, 'cart'])->name('cart');
            Route::post('/cart/process', [CustomerShipmentsController::class, 'processCart'])->name('cart.process');
            Route::get('/address-book', [CustomerShipmentsController::class, 'addressBook'])->name('address-book');
            Route::get('/create', [CustomerShipmentsController::class, 'create'])->name('create');
            Route::post('/', [CustomerShipmentsController::class, 'store'])->name('store');
            Route::get('/{shipment}', [CustomerShipmentsController::class, 'show'])->name('show');
            Route::get('/{shipment}/track', [CustomerShipmentsController::class, 'track'])->name('track');
            Route::get('/{shipment}/label', [CustomerShipmentsController::class, 'label'])->name('label');
            Route::post('/{shipment}/cancel', [CustomerShipmentsController::class, 'cancel'])->name('cancel');
            Route::post('/export', [CustomerShipmentsController::class, 'export'])->name('export');
            
            // AJAX endpoints for shipment creation
            Route::post('/calculate-price', [CustomerShipmentsController::class, 'calculatePrice'])->name('calculate-price');
            Route::post('/pickup-points', [CustomerShipmentsController::class, 'getPickupPoints'])->name('pickup-points');
        });
        
        // API endpoints for courier services (AJAX calls)
        Route::get('/couriers/{courierCode}/services', [CustomerShipmentsController::class, 'getCourierServices'])->name('customer.courier.services');
        
        // Payments Management
        Route::prefix('payments')->name('payments.')->group(function () {
            Route::get('/', [CustomerPaymentsController::class, 'index'])->name('index');
            Route::get('/{payment}', [CustomerPaymentsController::class, 'show'])->name('show');
            Route::get('/topup/create', [CustomerPaymentsController::class, 'topup'])->name('topup');
            Route::post('/topup', [CustomerPaymentsController::class, 'processTopup'])->name('topup.process');
            Route::post('/export', [CustomerPaymentsController::class, 'export'])->name('export');
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
        
        // Users Management (for customer admin users only)
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [App\Http\Controllers\Customer\UsersController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\Customer\UsersController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\Customer\UsersController::class, 'store'])->name('store');
            Route::get('/{customerUser}', [App\Http\Controllers\Customer\UsersController::class, 'show'])->name('show');
            Route::get('/{customerUser}/edit', [App\Http\Controllers\Customer\UsersController::class, 'edit'])->name('edit');
            Route::put('/{customerUser}', [App\Http\Controllers\Customer\UsersController::class, 'update'])->name('update');
            Route::delete('/{customerUser}', [App\Http\Controllers\Customer\UsersController::class, 'destroy'])->name('destroy');
            Route::post('/{customerUser}/transfer-admin', [App\Http\Controllers\Customer\UsersController::class, 'transferAdmin'])->name('transfer-admin');
        });
        
        // Customer Reports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', function () { 
                return view('customer.reports.index', [
                    'title' => 'Reports',
                    'description' => 'Your shipment and payment reports'
                ]); 
            })->name('index');
            
            Route::get('/shipments', function () { 
                return view('customer.reports.shipments', [
                    'title' => 'Shipment Reports',
                    'description' => 'Detailed shipment analytics'
                ]); 
            })->name('shipments');
            
            Route::get('/payments', function () { 
                return view('customer.reports.payments', [
                    'title' => 'Payment Reports',
                    'description' => 'Payment history and analytics'
                ]); 
            })->name('payments');
        });
    });
});

/*
|--------------------------------------------------------------------------
| Payment Return Routes (Public)
|--------------------------------------------------------------------------
*/

Route::prefix('payments')->name('payments.')->group(function () {
    // Payment gateway returns (no authentication required)
    Route::get('/return/paynow', function () { 
        return view('payments.return', ['provider' => 'PayNow']); 
    })->name('return.paynow');
    
    Route::get('/return/stripe', function () { 
        return view('payments.return', ['provider' => 'Stripe']); 
    })->name('return.stripe');
    
    Route::get('/cancel/paynow', function () { 
        return view('payments.cancel', ['provider' => 'PayNow']); 
    })->name('cancel.paynow');
    
    Route::get('/cancel/stripe', function () { 
        return view('payments.cancel', ['provider' => 'Stripe']); 
    })->name('cancel.stripe');
});

/*
|--------------------------------------------------------------------------
| Public API Routes (for tracking without auth)
|--------------------------------------------------------------------------
*/

Route::prefix('api/public')->name('api.public.')->group(function () {
    Route::get('/track/{trackingNumber}', function ($trackingNumber) {
        return response()->json([
            'tracking_number' => $trackingNumber,
            'status' => 'in_transit',
            'message' => 'Package is in transit'
        ]);
    })->name('track');
    
    Route::get('/status', function () {
        return response()->json([
            'status' => 'operational',
            'timestamp' => now(),
            'version' => '6.0.0'
        ]);
    })->name('status');
});

/*
|--------------------------------------------------------------------------
| Health Check Route
|--------------------------------------------------------------------------
*/

Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now(),
        'service' => 'SkyBrokerSystem',
        'version' => '6.0.0'
    ]);
})->name('health');

/*
|--------------------------------------------------------------------------
| Fallback Route
|--------------------------------------------------------------------------
*/

Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});