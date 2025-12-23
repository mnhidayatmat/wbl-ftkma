<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Models\RolePermission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class PermissionController extends Controller
{
    /**
     * Display the permission matrix.
     */
    public function index(Request $request): View
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        // Get all roles (excluding admin as it always has full access)
        $allRoles = Role::where('name', '!=', 'admin')
            ->orderBy('display_name')
            ->get();

        // Get selected role from request or default to first role
        $selectedRoleId = $request->get('role_id', $allRoles->first()?->id);
        $selectedRole = $selectedRoleId ? Role::find($selectedRoleId) : null;

        // Get all permissions grouped by module
        $permissions = Permission::orderBy('sort_order')
            ->orderBy('module_name')
            ->orderBy('action')
            ->get()
            ->groupBy('module_name');

        // Get role permissions for selected role only
        $rolePermissions = collect();
        if ($selectedRole) {
            $rolePermissions = RolePermission::where('role_id', $selectedRole->id)
                ->with('permission')
                ->get()
                ->keyBy('permission_id');
        }

        return view('admin.permissions.index', compact(
            'allRoles',
            'selectedRole',
            'permissions',
            'rolePermissions'
        ));
    }

    /**
     * Update a single permission for a role.
     */
    public function update(Request $request): RedirectResponse
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'permission_id' => 'required|exists:permissions,id',
            'access_level' => 'required|in:full,view,none',
        ]);

        RolePermission::updateOrCreate(
            [
                'role_id' => $request->role_id,
                'permission_id' => $request->permission_id,
            ],
            [
                'access_level' => $request->access_level,
            ]
        );

        // Clear permission cache
        Cache::forget('permissions_' . $request->role_id);

        return response()->json([
            'success' => true,
            'message' => 'Permission updated successfully.',
        ]);
    }

    /**
     * Bulk update permissions for a role.
     */
    public function bulkUpdate(Request $request): RedirectResponse
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'permissions' => 'required|array',
            'permissions.*.permission_id' => 'required|exists:permissions,id',
            'permissions.*.access_level' => 'required|in:full,view,none',
        ]);

        DB::transaction(function () use ($request) {
            foreach ($request->permissions as $permissionData) {
                RolePermission::updateOrCreate(
                    [
                        'role_id' => $request->role_id,
                        'permission_id' => $permissionData['permission_id'],
                    ],
                    [
                        'access_level' => $permissionData['access_level'],
                    ]
                );
            }

            // Clear permission cache for this role
            Cache::forget('permissions_' . $request->role_id);
        });

        return redirect()->route('admin.permissions.index', ['role_id' => $request->role_id])
            ->with('success', 'Permissions updated successfully.');
    }

    /**
     * Bulk update permissions for a specific module.
     */
    public function bulkUpdateModule(Request $request): RedirectResponse
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'module_name' => 'required|string',
            'access_level' => 'required|in:full,view,none',
        ]);

        $role = Role::findOrFail($request->role_id);
        
        // Prevent modifying admin role
        if ($role->name === 'admin') {
            return back()->with('error', 'Admin role cannot be modified.');
        }

        // Get all permissions for this module
        $permissions = Permission::where('module_name', $request->module_name)->get();

        DB::transaction(function () use ($role, $permissions, $request) {
            foreach ($permissions as $permission) {
                RolePermission::updateOrCreate(
                    [
                        'role_id' => $role->id,
                        'permission_id' => $permission->id,
                    ],
                    [
                        'access_level' => $request->access_level,
                    ]
                );
            }

            Cache::forget('permissions_' . $role->id);
        });

        return redirect()->route('admin.permissions.index', ['role_id' => $request->role_id])
            ->with('success', "All permissions for module '{$request->module_name}' set to '{$request->access_level}'.");
    }

    /**
     * Grant all permissions for a role (full access).
     */
    public function grantAll(Request $request): RedirectResponse
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);

        $role = Role::findOrFail($request->role_id);
        
        // Prevent modifying admin role
        if ($role->name === 'admin') {
            return back()->with('error', 'Admin role cannot be modified.');
        }

        $permissions = Permission::all();

        DB::transaction(function () use ($role, $permissions) {
            foreach ($permissions as $permission) {
                RolePermission::updateOrCreate(
                    [
                        'role_id' => $role->id,
                        'permission_id' => $permission->id,
                    ],
                    [
                        'access_level' => 'full',
                    ]
                );
            }

            Cache::forget('permissions_' . $role->id);
        });

        return redirect()->route('admin.permissions.index', ['role_id' => $role->id])
            ->with('success', "All permissions granted (full access) for {$role->display_name}.");
    }

    /**
     * Set all permissions to view-only for a role.
     */
    public function setViewOnly(Request $request): RedirectResponse
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);

        $role = Role::findOrFail($request->role_id);
        
        // Prevent modifying admin role
        if ($role->name === 'admin') {
            return back()->with('error', 'Admin role cannot be modified.');
        }

        $permissions = Permission::all();

        DB::transaction(function () use ($role, $permissions) {
            foreach ($permissions as $permission) {
                RolePermission::updateOrCreate(
                    [
                        'role_id' => $role->id,
                        'permission_id' => $permission->id,
                    ],
                    [
                        'access_level' => 'view',
                    ]
                );
            }

            Cache::forget('permissions_' . $role->id);
        });

        return redirect()->route('admin.permissions.index', ['role_id' => $role->id])
            ->with('success', "All permissions set to view-only for {$role->display_name}.");
    }

    /**
     * Revoke all permissions for a role.
     */
    public function revokeAll(Request $request): RedirectResponse
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);

        $role = Role::findOrFail($request->role_id);
        
        // Prevent modifying admin role
        if ($role->name === 'admin') {
            return back()->with('error', 'Admin role cannot be modified.');
        }

        RolePermission::where('role_id', $role->id)->delete();
        Cache::forget('permissions_' . $role->id);

        return redirect()->route('admin.permissions.index', ['role_id' => $role->id])
            ->with('success', "All permissions revoked for {$role->display_name}.");
    }
}
