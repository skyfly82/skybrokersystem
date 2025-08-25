<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomerAdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Check if user is authenticated and is a customer user
        if (!$user) {
            return redirect()->route('customer.login')
                ->withErrors(['error' => 'Musisz być zalogowany aby uzyskać dostęp.']);
        }

        // Check if user has admin role
        if ($user->role !== 'admin') {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Access denied'], 403);
            }
            
            return redirect()->route('customer.dashboard')
                ->withErrors(['error' => 'Nie masz uprawnień do tej sekcji. Tylko administratorzy firmy mogą zarządzać użytkownikami.']);
        }

        return $next($request);
    }
}