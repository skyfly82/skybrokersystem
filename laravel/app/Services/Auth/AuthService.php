<?php

/**
 * Cel: Implementacja serwisu uwierzytelniania
 * Moduł: Auth
 * Odpowiedzialny: sky_fly82
 * Data: 2025-09-02
 */

namespace App\Services\Auth;

use App\Models\ApiKey;
use App\Models\CustomerUser;
use App\Services\Contracts\Auth\AuthServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthService implements AuthServiceInterface
{
    public function login(array $credentials): array
    {
        if (! Auth::guard('customer_user')->attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => 'The provided credentials are incorrect.',
            ]);
        }

        $user = Auth::guard('customer_user')->user();

        // Check if customer is active
        if (! $user->customer->isActive()) {
            Auth::guard('customer_user')->logout();
            throw ValidationException::withMessages([
                'account' => 'Your account is not active. Please contact support.',
            ]);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return [
            'user' => $user->load('customer'),
            'token' => $token,
            'token_type' => 'Bearer',
        ];
    }

    public function register(array $data): CustomerUser
    {
        // Registration logic would typically involve customer approval process
        $user = CustomerUser::create([
            'customer_id' => $data['customer_id'],
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'] ?? 'user',
            'is_active' => false, // Requires approval
        ]);

        // Send verification email or notify admin

        return $user;
    }

    public function logout(Request $request): bool
    {
        $request->user()->currentAccessToken()?->delete();

        return true;
    }

    public function sendPasswordResetEmail(string $email): bool
    {
        $user = CustomerUser::where('email', $email)->first();

        if (! $user) {
            // Don't reveal whether email exists
            return true;
        }

        $status = Password::broker('customer_users')->sendResetLink(['email' => $email]);

        return $status === Password::RESET_LINK_SENT;
    }

    public function resetPassword(array $data): bool
    {
        $status = Password::broker('customer_users')->reset($data, function ($user, $password) {
            $user->password = Hash::make($password);
            $user->save();
        });

        return $status === Password::PASSWORD_RESET;
    }

    public function changePassword(CustomerUser $user, string $currentPassword, string $newPassword): bool
    {
        if (! Hash::check($currentPassword, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'Current password is incorrect.',
            ]);
        }

        $user->update(['password' => Hash::make($newPassword)]);

        return true;
    }

    public function updateProfile(CustomerUser $user, array $data): CustomerUser
    {
        $user->update([
            'name' => $data['name'] ?? $user->name,
            'email' => $data['email'] ?? $user->email,
            'phone' => $data['phone'] ?? $user->phone,
        ]);

        return $user->fresh();
    }

    public function generateApiKey(CustomerUser $user, string $name): array
    {
        $key = 'sk_'.Str::random(48);

        ApiKey::create([
            'customer_id' => $user->customer_id,
            'customer_user_id' => $user->id,
            'name' => $name,
            'key' => hash('sha256', $key),
            'last_used_at' => null,
        ]);

        return [
            'name' => $name,
            'key' => $key, // Return plain key only once
            'created_at' => now(),
        ];
    }

    public function revokeApiKey(string $apiKey): bool
    {
        return ApiKey::where('key', hash('sha256', $apiKey))->delete() > 0;
    }

    public function validateApiKey(string $apiKey): ?CustomerUser
    {
        $hashedKey = hash('sha256', $apiKey);

        $apiKeyRecord = ApiKey::where('key', $hashedKey)
            ->where('is_active', true)
            ->first();

        if (! $apiKeyRecord) {
            return null;
        }

        // Update last used timestamp
        $apiKeyRecord->update(['last_used_at' => now()]);

        return $apiKeyRecord->customerUser;
    }
}
