<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'module_name',
        'action',
        'display_name',
        'description',
        'sort_order',
    ];

    /**
     * Get all roles that have this permission.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_permissions')
            ->withPivot('access_level')
            ->withTimestamps();
    }

    /**
     * Get permission by module and action.
     */
    public static function findByModuleAndAction(string $moduleName, string $action): ?self
    {
        return static::where('module_name', $moduleName)
            ->where('action', $action)
            ->first();
    }

    /**
     * Get all permissions grouped by module.
     */
    public static function getGroupedByModule(): array
    {
        return static::orderBy('sort_order')
            ->orderBy('module_name')
            ->orderBy('action')
            ->get()
            ->groupBy('module_name')
            ->toArray();
    }
}
