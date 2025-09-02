<?php

/**
 * Cel: Middleware dodający nagłówki bezpieczeństwa HTTP
 * Moduł: Security
 * Odpowiedzialny: Claude-Code
 * Data: 2025-09-02
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Prevent clickjacking attacks
        $response->headers->set('X-Frame-Options', 'DENY');

        // Prevent MIME type sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Enable XSS protection
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Enforce HTTPS connections (only in production)
        if (app()->environment('production')) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        // Content Security Policy for API endpoints
        if ($request->is('api/*')) {
            $response->headers->set('Content-Security-Policy', "default-src 'none'; frame-ancestors 'none';");
        }

        // Referrer policy
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Permissions policy (formerly Feature Policy)
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        // Remove server information
        $response->headers->remove('X-Powered-By');
        $response->headers->set('Server', 'SkyBrokerSystem');

        return $response;
    }
}
