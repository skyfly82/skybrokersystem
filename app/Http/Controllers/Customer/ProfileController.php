<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\UpdateProfileRequest;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show()
    {
        $user = auth('customer_user')->user();
        $customer = $user->customer;

        // Get recent audit logs for the customer and its users
        $recentLogs = AuditLog::where(function($query) use ($customer) {
                $query->where(function($q) use ($customer) {
                    $q->where('auditable_type', 'App\\Models\\Customer')
                      ->where('auditable_id', $customer->id);
                })->orWhere(function($q) use ($customer) {
                    $q->where('auditable_type', 'App\\Models\\CustomerUser')
                      ->whereIn('auditable_id', $customer->users->pluck('id'));
                });
            })
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get login logs for customer users
        $loginLogs = AuditLog::where('auditable_type', 'App\\Models\\CustomerUser')
            ->whereIn('auditable_id', $customer->users->pluck('id'))
            ->where('event', 'login')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('customer.profile.show', compact('user', 'customer', 'recentLogs', 'loginLogs'));
    }

    public function edit()
    {
        $user = auth('customer_user')->user();
        $customer = $user->customer;

        return view('customer.profile.edit', compact('user', 'customer'));
    }

    public function update(Request $request)
    {
        $user = auth('customer_user')->user();
        $customer = $user->customer;

        // Validation rules
        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:customer_users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
        ];

        // Add company validation rules only for admins
        if ($user->canCreateUsers() || $user->is_primary) {
            $rules = array_merge($rules, [
                'company_name' => 'required|string|max:255',
                'tax_number' => 'nullable|string|max:20',
                'company_email' => 'required|email|max:255',
                'company_phone' => 'nullable|string|max:20',
                'company_address' => 'nullable|string',
                'cod_return_account' => 'nullable|string|regex:/^(PL)?[0-9]{26}$/',
                'settlement_account' => 'nullable|string|regex:/^(PL)?[0-9]{26}$/',
            ]);
        }

        $validated = $request->validate($rules);

        // Update user data
        $user->update($request->only([
            'first_name', 'last_name', 'email', 'phone'
        ]));

        // Update customer data if user has permissions
        if ($user->canCreateUsers() || $user->is_primary) {
            $customerData = [];
            
            // Map form fields to model fields
            if ($request->has('company_name')) {
                $customerData['company_name'] = $request->company_name;
            }
            if ($request->has('tax_number')) {
                $customerData['tax_number'] = $request->tax_number;
            }
            if ($request->has('company_email')) {
                $customerData['email'] = $request->company_email;
            }
            if ($request->has('company_phone')) {
                $customerData['phone'] = $request->company_phone;
            }
            if ($request->has('company_address')) {
                $customerData['address'] = $request->company_address;
            }
            if ($request->has('cod_return_account')) {
                $customerData['cod_return_account'] = $request->cod_return_account;
            }
            if ($request->has('settlement_account')) {
                $customerData['settlement_account'] = $request->settlement_account;
            }

            if (!empty($customerData)) {
                $customer->update($customerData);
            }
        }

        return redirect()->route('customer.profile.show')
            ->with('success', 'Profil został zaktualizowany pomyślnie.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|min:8|confirmed',
        ]);

        auth()->user()->update([
            'password' => bcrypt($request->password)
        ]);

        return back()->with('success', 'Hasło zostało zmienione.');
    }

    public function notifications()
    {
        $user = auth()->user();
        $customer = $user->customer;

        return view('customer.profile.notifications', compact('user', 'customer'));
    }

    public function updateNotifications(Request $request)
    {
        $request->validate([
            'email_notifications' => 'array',
            'sms_notifications' => 'array',
        ]);

        $preferences = [
            'email' => $request->email_notifications ?? [],
            'sms' => $request->sms_notifications ?? [],
        ];

        auth()->user()->update([
            'notification_preferences' => $preferences
        ]);

        return back()->with('success', 'Preferencje powiadomień zostały zaktualizowane.');
    }
}