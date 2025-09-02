<?php

/**
 * Cel: Kontrakt dla serwisu uwierzytelniania
 * Moduł: Auth
 * Odpowiedzialny: Claude-Code
 * Data: 2025-09-02
 */

namespace App\Services\Contracts\Auth;

use App\Models\CustomerUser;
use Illuminate\Http\Request;

interface AuthServiceInterface
{
    /**
     * Authenticate user and return token
     */
    public function login(array $credentials): array;

    /**
     * Register new user
     */
    public function register(array $data): CustomerUser;

    /**
     * Logout user
     */
    public function logout(Request $request): bool;

    /**
     * Send password reset email
     */
    public function sendPasswordResetEmail(string $email): bool;

    /**
     * Reset password using token
     */
    public function resetPassword(array $data): bool;

    /**
     * Change user password
     */
    public function changePassword(CustomerUser $user, string $currentPassword, string $newPassword): bool;

    /**
     * Update user profile
     */
    public function updateProfile(CustomerUser $user, array $data): CustomerUser;

    /**
     * Generate API key for user
     */
    public function generateApiKey(CustomerUser $user, string $name): array;

    /**
     * Revoke API key
     */
    public function revokeApiKey(string $apiKey): bool;

    /**
     * Validate API key
     */
    public function validateApiKey(string $apiKey): ?CustomerUser;
}