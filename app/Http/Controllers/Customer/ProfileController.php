<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\UpdateProfileRequest;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user();
        $customer = $user->customer;

        return view('customer.profile.show', compact('user', 'customer'));
    }

    public function edit()
    {
        $user = auth()->user();
        $customer = $user->customer;

        return view('customer.profile.edit', compact('user', 'customer'));
    }

    public function update(UpdateProfileRequest $request)
    {
        $user = auth()->user();
        $customer = $user->customer;

        // Update user data
        $user->update($request->only([
            'first_name', 'last_name', 'email', 'phone', 'notification_preferences'
        ]));

        // Update customer data if user is primary
        if ($user->is_primary) {
            $customer->update($request->only([
                'company_name', 'company_address', 'city', 'postal_code', 
                'phone', 'email', 'website', 'notification_preferences'
            ]));
        }

        return redirect()->route('customer.profile.show')
            ->with('success', 'Profil został zaktualizowany.');
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