<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\ApiKey;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckApiKey
{
    public function handle(Request $request, Closure $next, string $requiredScope = 'map.read'): Response
    {
        $header = config('map.api.header', 'X-API-Key');
        $key = $request->header($header) ?? $request->query('api_key');

        if (!$key) {
            return response()->json(['success' => false, 'error' => 'API key is required'], 401);
        }

        $apiKey = ApiKey::where('key', $key)->first();
        if (!$apiKey || !$apiKey->isActive()) {
            return response()->json(['success' => false, 'error' => 'Invalid API key'], 401);
        }

        if (!$apiKey->hasScope($requiredScope)) {
            return response()->json(['success' => false, 'error' => 'Insufficient scope'], 403);
        }

        if (!$apiKey->withinLimits()) {
            return response()->json(['success' => false, 'error' => 'Rate limit exceeded'], 429);
        }

        // attach resolved user (customer) context if present
        if ($apiKey->customer_id) {
            $request->setUserResolver(fn () => $apiKey->customer);
        }

        $apiKey->registerHit();
        return $next($request);
    }
}

