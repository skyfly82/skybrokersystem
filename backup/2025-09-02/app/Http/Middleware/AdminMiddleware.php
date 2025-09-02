<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next, string $role = 'admin'): Response
    {
        $user = $request->user();

        if (!$user || !$user->isAdmin()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Access denied'], 403);
            }
            
            abort(403, 'Access denied');
        }

        if ($role === 'super_admin' && !$user->isSuperAdmin()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Super admin access required'], 403);
            }
            
            abort(403, 'Super admin access required');
        }

        return $next($request);
    }
}