<?php

namespace App\Helpers;

use App\Models\Permission;
use App\Models\Role;
use App\Models\RolePermission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class PermissionHelper
{
    /**
     * Check if user has permission for a module and action.
     */
    public static function canAccess(string $moduleName, string $action, ?string $accessLevel = 'view'): bool
    {
        $user = Auth::user();

        if (! $user) {
            return false;
        }

        // Admin always has full access
        if ($user->isAdmin()) {
            return true;
        }

        // Get user's roles
        $roles = $user->roles;

        if ($roles->isEmpty()) {
            return false;
        }

        // Get permission
        $permission = Permission::findByModuleAndAction($moduleName, $action);

        if (! $permission) {
            return false;
        }

        // Check if any of user's roles have the required access level
        foreach ($roles as $role) {
            $rolePermission = self::getRolePermission($role->id, $permission->id);

            if (! $rolePermission) {
                continue;
            }

            $roleAccessLevel = $rolePermission->access_level;

            // Check access level
            if ($accessLevel === 'full') {
                if ($roleAccessLevel === 'full') {
                    return true;
                }
            } elseif ($accessLevel === 'view') {
                if (in_array($roleAccessLevel, ['full', 'view'])) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Check if user can perform a specific action (create, update, delete).
     */
    public static function can(string $moduleName, string $action): bool
    {
        // For CRUD actions, require 'full' access
        if (in_array($action, ['create', 'update', 'delete', 'export', 'finalise', 'moderate'])) {
            return self::canAccess($moduleName, $action, 'full');
        }

        // For view actions, 'view' access is sufficient
        return self::canAccess($moduleName, $action, 'view');
    }

    /**
     * Get role permission with caching.
     */
    protected static function getRolePermission(int $roleId, int $permissionId): ?RolePermission
    {
        $cacheKey = "permission_{$roleId}_{$permissionId}";

        return Cache::remember($cacheKey, 3600, function () use ($roleId, $permissionId) {
            return RolePermission::where('role_id', $roleId)
                ->where('permission_id', $permissionId)
                ->first();
        });
    }

    /**
     * Clear permission cache for a role.
     */
    public static function clearCache(int $roleId): void
    {
        $permissions = Permission::all();

        foreach ($permissions as $permission) {
            Cache::forget("permission_{$roleId}_{$permission->id}");
        }

        Cache::forget("permissions_{$roleId}");
    }
}
