<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\RegisterRequest;
use App\Models\Customer;
use App\Models\CustomerUser;
use App\Notifications\CustomerRegisteredNotification;
use App\Notifications\CustomerVerificationCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('customer.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        $credentials['is_active'] = true;

        if (Auth::guard('customer_user')->attempt($credentials, $request->remember)) {
            $request->session()->regenerate();
            
            // Update last login if user exists
            if ($user = Auth::guard('customer_user')->user()) {
                $user->update([
                    'last_login_at' => now(),
                    'last_login_ip' => $request->ip(),
                ]);
            }

            return redirect()->intended(route('customer.dashboard'));
        }

        return back()->withErrors([
            'email' => 'Nieprawidłowe dane logowania.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::guard('customer_user')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('customer.login');
    }
    
    public function showPendingForm()
    {
        $user = Auth::guard('customer_user')->user();
        
        // If user is not authenticated, redirect to login
        if (!$user) {
            return redirect()->route('customer.login');
        }
        
        // If customer is already active, redirect to dashboard
        if ($user->customer && $user->customer->isActive()) {
            return redirect()->route('customer.dashboard');
        }
        
        // Show pending approval page
        return view('customer.auth.pending');
    }

    public function showRegistrationForm()
    {
        return view('customer.auth.register');
    }

    public function register(RegisterRequest $request)
    {
        try {
            DB::beginTransaction();

            // Create the customer company
            $customer = Customer::create([
                'company_name' => $request->company_name,
                'nip' => $request->nip,
                'company_address' => $request->company_address,
                'city' => $request->city,
                'postal_code' => $request->postal_code,
                'country' => 'PL',
                'phone' => $request->phone,
                'email' => $request->email,
                'status' => 'pending', // Requires admin approval
                'credit_limit' => 1000.00, // Default credit limit
                'current_balance' => 0.00,
                'settings' => [
                    'auto_approve_shipments' => false,
                    'require_approval' => true,
                    'default_pickup_method' => 'point'
                ]
            ]);

            // Create the primary user
            $primaryUser = CustomerUser::create([
                'customer_id' => $customer->id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'role' => 'admin',
                'is_active' => true,
                'is_primary' => true,
                'permissions' => [
                    'create_shipments',
                    'view_reports',
                    'manage_users',
                    'manage_settings'
                ]
            ]);

            // Generate verification code and send to customer
            $verificationData = $customer->generateVerificationCode();
            $primaryUser->notify(new CustomerVerificationCode($customer, $verificationData));

            // Send notification to admins about new registration
            $admins = \App\Models\SystemUser::where('role', 'admin')->get();
            if ($admins->count() > 0) {
                Notification::send($admins, new CustomerRegisteredNotification($customer));
            }

            DB::commit();

            // Redirect to verification page
            return redirect()->route('customer.verify', ['token' => $verificationData['token']])
                ->with('success', 'Konto zostało utworzone pomyślnie! Sprawdź swój email i wprowadź 6-cyfrowy kod weryfikacyjny.')
                ->with('email', $customer->email);

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Log the actual error for debugging
            \Log::error('Customer registration failed: ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->except(['password', 'password_confirmation'])
            ]);
            
            return back()
                ->withInput($request->except(['password', 'password_confirmation']))
                ->with('error', 'Wystąpił błąd podczas tworzenia konta. Spróbuj ponownie lub skontaktuj się z pomocą techniczną. (Błąd: ' . $e->getMessage() . ')');
        }
    }

    public function showVerifyForm(Request $request, string $token = null)
    {
        if (!$token) {
            return redirect()->route('customer.login')->with('error', 'Nieprawidłowy link weryfikacyjny.');
        }

        $customer = Customer::where('verification_token', $token)->first();
        
        if (!$customer) {
            return redirect()->route('customer.login')->with('error', 'Nieprawidłowy lub wygasły link weryfikacyjny.');
        }

        if (!$customer->verifyToken($token)) {
            return redirect()->route('customer.login')->with('error', 'Link weryfikacyjny wygasł. Skontaktuj się z administratorem.');
        }

        if ($customer->isEmailVerified()) {
            return redirect()->route('customer.login')->with('success', 'Konto zostało już zweryfikowane. Możesz się zalogować.');
        }

        return view('customer.auth.verify', [
            'customer' => $customer,
            'token' => $token,
            'email' => $customer->email,
            'canResend' => $customer->canResendCode(),
            'codeExpiryMinutes' => \App\Models\SystemSetting::get('verification_code_expiry_minutes', 60)
        ]);
    }

    public function verify(Request $request, string $token)
    {
        $request->validate([
            'verification_code' => 'required|string|size:6|regex:/^[0-9]{6}$/'
        ]);

        $customer = Customer::where('verification_token', $token)->first();
        
        if (!$customer || !$customer->verifyToken($token)) {
            return back()->with('error', 'Nieprawidłowy lub wygasły link weryfikacyjny.');
        }

        if ($customer->verifyCode($request->verification_code)) {
            return redirect()->route('customer.login')->with('success', 'Konto zostało pomyślnie zweryfikowane! Możesz się teraz zalogować.');
        }

        return back()
            ->with('error', 'Nieprawidłowy lub wygasły kod weryfikacyjny.')
            ->with('canResend', $customer->canResendCode());
    }

    public function resendCode(Request $request, string $token)
    {
        $customer = Customer::where('verification_token', $token)->first();
        
        if (!$customer || !$customer->verifyToken($token)) {
            return response()->json(['success' => false, 'message' => 'Nieprawidłowy lub wygasły link weryfikacyjny.'], 400);
        }

        if (!$customer->canResendCode()) {
            return response()->json(['success' => false, 'message' => 'Kod można wysłać ponownie za kilka minut.'], 429);
        }

        try {
            // Generate new code but keep the same token
            $verificationData = $customer->generateVerificationCode();
            $verificationData['token'] = $token; // Keep original token
            
            $customer->update(['verification_token' => $token]); // Restore token
            
            $primaryUser = $customer->getPrimaryUser();
            if ($primaryUser) {
                $primaryUser->notify(new CustomerVerificationCode($customer, $verificationData));
            }

            return response()->json([
                'success' => true, 
                'message' => 'Nowy kod został wysłany na Twój email.',
                'codeExpiryMinutes' => \App\Models\SystemSetting::get('verification_code_expiry_minutes', 60)
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Wystąpił błąd podczas wysyłania kodu.'], 500);
        }
    }
}