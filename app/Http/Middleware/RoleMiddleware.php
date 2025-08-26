<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $permission - The required permission/ability
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user();

        if (!$user) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthenticated'], 401);
            }
            
            // Determine which guard to redirect to
            if ($request->is('admin/*')) {
                return redirect()->route('admin.login');
            }
            return redirect()->route('customer.login');
        }

        // Check permission based on user type and role
        if (!$this->hasPermission($user, $permission)) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Access denied'], 403);
            }
            
            abort(403, 'Nie masz uprawnień do tej akcji.');
        }

        return $next($request);
    }

    /**
     * Check if user has the required permission
     */
    private function hasPermission($user, string $permission): bool
    {
        // For CustomerUser
        if (method_exists($user, 'canCreateUsers')) {
            switch ($permission) {
                case 'manage_users':
                    return $user->canCreateUsers();
                case 'manage_payments':
                    return $user->canManagePayments();
                case 'access_financials':
                    return $user->canAccessFinancials();
                case 'manage_shipments':
                    return $user->canManageShipments();
                case 'view_reports':
                    return $user->canViewReports();
                default:
                    return false;
            }
        }

        // For SystemUser
        if (method_exists($user, 'canCreateEmployees')) {
            switch ($permission) {
                case 'manage_employees':
                    return $user->canCreateEmployees();
                case 'manage_customers':
                    return $user->canManageCustomers();
                case 'manage_users':
                    return $user->canManageUsers();
                case 'view_reports':
                    return $user->canViewReports();
                default:
                    return false;
            }
        }

        return false;
    }
}
