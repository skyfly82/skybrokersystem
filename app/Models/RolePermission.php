<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RolePermission extends Model
{
    protected $fillable = [
        'user_type',
        'role',
        'permission_id',
        'granted',
    ];

    protected $casts = [
        'granted' => 'boolean',
    ];

    public function permission(): BelongsTo
    {
        return $this->belongsTo(Permission::class);
    }

    public static function getRolePermissions(string $userType, string $role): array
    {
        return self::where('user_type', $userType)
            ->where('role', $role)
            ->where('granted', true)
            ->with('permission')
            ->get()
            ->pluck('permission.name')
            ->toArray();
    }

    public static function hasPermission(string $userType, string $role, string $permissionName): bool
    {
        return self::join('permissions', 'role_permissions.permission_id', '=', 'permissions.id')
            ->where('role_permissions.user_type', $userType)
            ->where('role_permissions.role', $role)
            ->where('permissions.name', $permissionName)
            ->where('role_permissions.granted', true)
            ->exists();
    }
}
