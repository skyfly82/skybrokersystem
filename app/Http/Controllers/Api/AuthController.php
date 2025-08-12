<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'api_key' => 'required|string'
        ]);

        $customer = Customer::where('api_key', $request->api_key)
            ->where('status', 'active')
            ->first();

        if (!$customer) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid API key'
            ], 401);
        }

        // Generate temporary token for this session
        $token = $customer->createToken('api-access')->plainTextToken;

        return response()->json([
            'success' => true,
            'data' => [
                'token' => $token,
                'customer' => [
                    'uuid' => $customer->uuid,
                    'company_name' => $customer->company_name,
                    'current_balance' => $customer->current_balance,
                ]
            ]
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Successfully logged out'
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'customer' => $request->user(),
                'permissions' => $this->getUserPermissions($request->user())
            ]
        ]);
    }

    private function getUserPermissions(Customer $customer): array
    {
        return [
            'can_create_shipments' => $customer->canCreateShipment(),
            'can_view_reports' => true,
            'can_manage_users' => false, // Only via web interface
            'api_rate_limit' => 1000, // per hour
        ];
    }
}