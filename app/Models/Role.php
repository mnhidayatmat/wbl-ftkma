<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'description',
    ];

    /**
     * Get all users with this role.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_roles');
    }

    /**
     * Get role by name.
     */
    public static function findByName(string $name): ?self
    {
        return static::where('name', $name)->first();
    }

    /**
     * Get all permissions for this role.
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions')
            ->withPivot('access_level')
            ->withTimestamps();
    }

    /**
     * Get role permission for a specific permission.
     */
    public function getPermission(string $moduleName, string $action): ?RolePermission
    {
        $permission = Permission::findByModuleAndAction($moduleName, $action);
        
        if (!$permission) {
            return null;
        }

        return RolePermission::where('role_id', $this->id)
            ->where('permission_id', $permission->id)
            ->first();
    }
}
