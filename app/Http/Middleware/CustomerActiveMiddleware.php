<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomerActiveMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->is_active) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Account inactive'], 403);
            }
            
            return redirect()->route('customer.login')
                ->withErrors(['error' => 'Twoje konto zostało dezaktywowane.']);
        }

        $customer = $user->customer;

        if (!$customer || !$customer->isActive()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Customer account inactive'], 403);
            }
            
            return redirect()->route('customer.login')
                ->withErrors(['error' => 'Konto firmy zostało zawieszone.']);
        }

        return $next($request);
    }
}