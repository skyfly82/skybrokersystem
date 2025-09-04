<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerUser;
use App\Notifications\CustomerRegisteredNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    protected array $supportedProviders = ['google', 'facebook', 'linkedin-openid'];

    public function redirect(string $provider)
    {
        if (!in_array($provider, $this->supportedProviders)) {
            return redirect()->route('customer.register')->with('error', 'Nieobsługiwany dostawca uwierzytelniania.');
        }

        // Store registration type in session
        session(['registration_type' => request('type', 'company')]);
        
        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider, Request $request)
    {
        if (!in_array($provider, $this->supportedProviders)) {
            return redirect()->route('customer.register')->with('error', 'Nieobsługiwany dostawca uwierzytelniania.');
        }

        try {
            $socialUser = Socialite::driver($provider)->user();
            $registrationType = session('registration_type', 'company');

            // Check if user already exists with this social account
            $existingCustomer = Customer::where($provider . '_id', $socialUser->getId())->first();
            
            if ($existingCustomer) {
                return $this->loginExistingUser($existingCustomer);
            }

            // Check if customer exists with same email
            $existingCustomerByEmail = Customer::where('email', $socialUser->getEmail())->first();
            
            if ($existingCustomerByEmail) {
                return $this->linkSocialAccount($existingCustomerByEmail, $provider, $socialUser);
            }

            // Create new customer
            return $this->createNewCustomer($provider, $socialUser, $registrationType);

        } catch (\Exception $e) {
            \Log::error('Social authentication failed: ' . $e->getMessage(), [
                'provider' => $provider,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('customer.register')
                ->with('error', 'Wystąpił błąd podczas logowania przez ' . ucfirst($provider) . '. Spróbuj ponownie.');
        }
    }

    protected function loginExistingUser(Customer $customer)
    {
        $primaryUser = $customer->getPrimaryUser();
        
        if (!$primaryUser) {
            return redirect()->route('customer.register')
                ->with('error', 'Konto istnieje, ale nie ma przypisanego użytkownika. Skontaktuj się z administracją.');
        }

        Auth::guard('customer_user')->login($primaryUser, true);

        // Update last login
        $primaryUser->update([
            'last_login_at' => now(),
            'last_login_ip' => request()->ip(),
        ]);

        return redirect()->intended(route('customer.dashboard'));
    }

    protected function linkSocialAccount(Customer $customer, string $provider, $socialUser)
    {
        // Link social account to existing customer
        $customer->update([
            $provider . '_id' => $socialUser->getId(),
            'social_avatar' => array_merge($customer->social_avatar ?? [], [
                $provider => $socialUser->getAvatar()
            ])
        ]);

        // Also link to primary user
        $primaryUser = $customer->getPrimaryUser();
        if ($primaryUser) {
            $primaryUser->update([
                $provider . '_id' => $socialUser->getId(),
                'avatar' => $primaryUser->avatar ?? $socialUser->getAvatar(),
            ]);
        }

        return $this->loginExistingUser($customer);
    }

    protected function createNewCustomer(string $provider, $socialUser, string $type)
    {
        try {
            DB::beginTransaction();

            $providerName = $provider === 'linkedin-openid' ? 'linkedin' : $provider;

            // Determine names based on social user data
            $names = $this->extractNames($socialUser);

            // Create customer
            $customerData = [
                'type' => $type,
                'email' => $socialUser->getEmail(),
                'phone' => $socialUser->user['phone'] ?? null,
                'country' => 'PL',
                'status' => 'pending',
                'credit_limit' => 1000.00,
                'current_balance' => 0.00,
                $providerName . '_id' => $socialUser->getId(),
                'social_avatar' => [$providerName => $socialUser->getAvatar()],
                'registration_source' => $providerName,
                'settings' => [
                    'auto_approve_shipments' => false,
                    'require_approval' => true,
                    'default_pickup_method' => 'point',
                ],
            ];

            if ($type === 'company') {
                $customerData['company_name'] = $names['company'] ?? ($names['first'] . ' ' . $names['last']);
                $customerData['company_address'] = 'Adres do uzupełnienia';
                $customerData['city'] = 'Miasto do uzupełnienia';
                $customerData['postal_code'] = '00-000';
            } else {
                $customerData['individual_first_name'] = $names['first'];
                $customerData['individual_last_name'] = $names['last'];
            }

            $customer = Customer::create($customerData);

            // Create primary user
            $primaryUser = CustomerUser::create([
                'customer_id' => $customer->id,
                'first_name' => $names['first'],
                'last_name' => $names['last'],
                'email' => $socialUser->getEmail(),
                'phone' => $socialUser->user['phone'] ?? null,
                'role' => 'admin',
                'is_active' => true,
                'is_primary' => true,
                $providerName . '_id' => $socialUser->getId(),
                'avatar' => $socialUser->getAvatar(),
                'permissions' => [
                    'create_shipments',
                    'view_reports',
                    'manage_users',
                    'manage_settings',
                ],
            ]);

            // Send notification to admins
            $admins = \App\Models\SystemUser::where('role', 'admin')->get();
            if ($admins->count() > 0) {
                Notification::send($admins, new CustomerRegisteredNotification($customer));
            }

            DB::commit();

            // Login the user
            Auth::guard('customer_user')->login($primaryUser, true);

            return redirect()->route('customer.profile.edit')
                ->with('success', 'Konto zostało utworzone! Uzupełnij pozostałe dane aby aktywować pełny dostęp.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Social registration failed: ' . $e->getMessage(), [
                'provider' => $provider,
                'social_user_id' => $socialUser->getId(),
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('customer.register')
                ->with('error', 'Wystąpił błąd podczas tworzenia konta. Spróbuj ponownie.');
        }
    }

    protected function extractNames($socialUser): array
    {
        $name = $socialUser->getName() ?? '';
        $names = explode(' ', trim($name));
        
        return [
            'first' => $names[0] ?? 'Imię',
            'last' => isset($names[1]) ? implode(' ', array_slice($names, 1)) : 'Nazwisko',
            'company' => $socialUser->user['company']['name'] ?? null,
        ];
    }
}
