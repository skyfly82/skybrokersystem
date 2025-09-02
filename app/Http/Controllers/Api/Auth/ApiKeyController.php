<?php

/**
 * Cel: Chudy kontroler API dla zarządzania kluczami API
 * Moduł: Auth
 * Odpowiedzialny: Claude-Code
 * Data: 2025-09-02
 */

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Services\Contracts\Auth\AuthServiceInterface;
use App\Models\ApiKey;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ApiKeyController extends Controller
{
    public function __construct(
        private readonly AuthServiceInterface $authService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $apiKeys = ApiKey::where('customer_id', $request->user()->customer_id)
            ->select(['id', 'name', 'last_used_at', 'created_at', 'is_active'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['data' => $apiKeys]);
    }

    public function store(Request $request): JsonResponse
    {
        $apiKeyData = $this->authService->generateApiKey(
            $request->user(),
            $request->input('name')
        );

        return response()->json([
            'message' => 'API key created successfully. Please store it safely - it will not be shown again.',
            'data' => $apiKeyData
        ], 201);
    }

    public function destroy(Request $request, ApiKey $apiKey): JsonResponse
    {
        // Authorization would be handled by Policy
        
        $success = $this->authService->revokeApiKey($apiKey->key);

        if (!$success) {
            return response()->json([
                'message' => 'Failed to revoke API key'
            ], 400);
        }

        return response()->json([
            'message' => 'API key revoked successfully'
        ]);
    }

    public function regenerate(Request $request, ApiKey $apiKey): JsonResponse
    {
        // Authorization would be handled by Policy
        
        // Revoke old key
        $this->authService->revokeApiKey($apiKey->key);
        
        // Generate new key
        $newApiKeyData = $this->authService->generateApiKey(
            $request->user(),
            $apiKey->name
        );

        return response()->json([
            'message' => 'API key regenerated successfully. Please store it safely - it will not be shown again.',
            'data' => $newApiKeyData
        ]);
    }
}