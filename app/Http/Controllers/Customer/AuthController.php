<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\LoginRequest;
use App\Http\Requests\Customer\RegisterRequest;
use App\Models\Customer;
use App\Models\CustomerUser;
use App\Notifications\CustomerRegistered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('customer.auth.login');
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        $credentials['is_active'] = true;

        if (Auth::guard('customer_user')->attempt($credentials, $request->remember)) {
            $request->session()->regenerate();
            
            // Update last login
            Auth::guard('customer_user')->user()->update([
                'last_login_at' => now(),
                'last_login_ip' => $request->ip(),
            ]);

            return redirect()->intended(route('customer.dashboard'));
        }

        return back()->withErrors([
            'email' => 'Nieprawidłowe dane logowania.',
        ])->onlyInput('email');
    }

    public function showRegistrationForm()
    {
        return view('customer.auth.register');
    }

    public function register(RegisterRequest $request)
    {
        try {
            DB::beginTransaction();

            // Create customer
            $customer = Customer::create([
                'company_name' => $request->company_name,
                'nip' => $request->nip,
                'company_address' => $request->company_address,
                'city' => $request->city,
                'postal_code' => $request->postal_code,
                'phone' => $request->phone,
                'email' => $request->email,
                'status' => 'pending', // Requires admin approval
            ]);

            // Create primary user
            $user = CustomerUser::create([
                'customer_id' => $customer->id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'phone' => $request->phone,
                'role' => 'admin',
                'is_primary' => true,
                'is_active' => true,
            ]);

            DB::commit();

            // Send notification to admin
            $user->notify(new CustomerRegistered($customer));

            return redirect()->route('customer.login')
                ->with('success', 'Rejestracja przebiegła pomyślnie. Twoje konto oczekuje na zatwierdzenie przez administratora.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Wystąpił błąd podczas rejestracji.'])->withInput();
        }
    }

    public function logout(Request $request)
    {
        Auth::guard('customer_user')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('customer.login');
    }
}