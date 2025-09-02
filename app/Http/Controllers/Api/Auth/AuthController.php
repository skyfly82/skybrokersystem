<?php

/**
 * Cel: Chudy kontroler API dla uwierzytelniania
 * Moduł: Auth
 * Odpowiedzialny: Claude-Code
 * Data: 2025-09-02
 */

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Services\Contracts\Auth\AuthServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthServiceInterface $authService
    ) {}

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');
        
        $result = $this->authService->login($credentials);

        return response()->json([
            'message' => 'Login successful',
            'data' => $result
        ]);
    }

    public function register(Request $request): JsonResponse
    {
        $user = $this->authService->register($request->all());

        return response()->json([
            'message' => 'Registration successful. Your account is pending approval.',
            'data' => $user
        ], 201);
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request);

        return response()->json([
            'message' => 'Logout successful'
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'data' => $request->user()->load('customer')
        ]);
    }

    public function forgotPassword(Request $request): JsonResponse
    {
        $this->authService->sendPasswordResetEmail($request->input('email'));

        return response()->json([
            'message' => 'If an account with that email exists, a password reset link has been sent.'
        ]);
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $success = $this->authService->resetPassword($request->only(
            'email', 'password', 'password_confirmation', 'token'
        ));

        if (!$success) {
            return response()->json([
                'message' => 'Failed to reset password. Please try again.'
            ], 400);
        }

        return response()->json([
            'message' => 'Password reset successful'
        ]);
    }

    public function changePassword(Request $request): JsonResponse
    {
        $this->authService->changePassword(
            $request->user(),
            $request->input('current_password'),
            $request->input('new_password')
        );

        return response()->json([
            'message' => 'Password changed successfully'
        ]);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $updatedUser = $this->authService->updateProfile(
            $request->user(),
            $request->all()
        );

        return response()->json([
            'message' => 'Profile updated successfully',
            'data' => $updatedUser
        ]);
    }
}