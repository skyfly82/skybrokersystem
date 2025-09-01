<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MarketingMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->canManageContent()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Marketing access denied'], 403);
            }
            
            abort(403, 'Marketing access denied');
        }

        return $next($request);
    }
}
