<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TrackUserActivity
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Track login activity if user just authenticated
        if (Auth::guard('customer_user')->check()) {
            $user = Auth::guard('customer_user')->user();

            // Update last login information
            if (! $user->last_login_at || $user->last_login_at->diffInMinutes(now()) > 30) {
                $user->update([
                    'last_login_at' => now(),
                    'last_login_ip' => $request->ip(),
                ]);

                // Log the login event
                AuditLog::create([
                    'auditable_type' => get_class($user),
                    'auditable_id' => $user->id,
                    'user_type' => 'customer_user',
                    'user_id' => $user->id,
                    'user_name' => $user->first_name.' '.$user->last_name,
                    'user_email' => $user->email,
                    'event' => 'login',
                    'old_values' => null,
                    'new_values' => ['login_time' => now()->toISOString()],
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->header('User-Agent'),
                    'description' => 'User logged in successfully',
                ]);
            }
        }

        if (Auth::guard('system_user')->check()) {
            $user = Auth::guard('system_user')->user();

            // Update last login information
            if (! $user->last_login_at || $user->last_login_at->diffInMinutes(now()) > 30) {
                $user->update([
                    'last_login_at' => now(),
                    'last_login_ip' => $request->ip(),
                ]);

                // Log the login event
                AuditLog::create([
                    'auditable_type' => get_class($user),
                    'auditable_id' => $user->id,
                    'user_type' => 'system_user',
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'user_email' => $user->email,
                    'event' => 'login',
                    'old_values' => null,
                    'new_values' => ['login_time' => now()->toISOString()],
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->header('User-Agent'),
                    'description' => 'System user logged in successfully',
                ]);
            }
        }

        return $response;
    }
}
