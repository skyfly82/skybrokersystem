<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\RolePermission;
use Illuminate\Http\Request;

class PermissionsController extends Controller
{
    public function index()
    {
        // Define available roles
        $roles = [
            'system_user' => [
                'super_admin' => 'Super Administrator',
                'admin' => 'Administrator',
                'moderator' => 'Moderator',
                'employee' => 'Employee',
            ],
            'customer_user' => [
                'admin' => 'Company Admin',
                'accountant' => 'Accountant',
                'warehouse' => 'Warehouse Manager',
                'user' => 'Standard User',
                'viewer' => 'View Only',
            ],
        ];

        // Get all permissions grouped by category
        $permissions = Permission::where('is_active', true)
            ->orderBy('category')
            ->orderBy('display_name')
            ->get()
            ->groupBy('category');

        // Get current role permissions
        $rolePermissions = [];
        foreach ($roles as $userType => $roleList) {
            foreach ($roleList as $role => $displayName) {
                $rolePermissions[$userType][$role] = RolePermission::getRolePermissions($userType, $role);
            }
        }

        return view('admin.permissions.index', compact('roles', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'permissions' => 'required|array',
            'permissions.*.*.*' => 'boolean',
        ]);

        // Clear all existing permissions
        RolePermission::truncate();

        // Add new permissions
        foreach ($request->permissions as $userType => $roles) {
            foreach ($roles as $role => $permissionIds) {
                foreach ($permissionIds as $permissionId => $granted) {
                    if ($granted) {
                        RolePermission::create([
                            'user_type' => $userType,
                            'role' => $role,
                            'permission_id' => (int) $permissionId,
                            'granted' => true,
                        ]);
                    }
                }
            }
        }

        return redirect()
            ->route('admin.permissions.index')
            ->with('success', 'Uprawnienia zostały zaktualizowane pomyślnie.');
    }
}
