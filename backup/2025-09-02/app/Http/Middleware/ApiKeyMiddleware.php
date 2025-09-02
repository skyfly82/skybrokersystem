<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Customer;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('X-API-Key') ?? $request->get('api_key');

        if (! $apiKey) {
            return response()->json([
                'success' => false,
                'error' => 'API key is required',
            ], 401);
        }

        $customer = Customer::where('api_key', $apiKey)
            ->where('status', 'active')
            ->first();

        if (! $customer) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid API key',
            ], 401);
        }

        // Set customer as authenticated user
        $request->setUserResolver(fn () => $customer);

        return $next($request);
    }
}
