<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RolePermission extends Model
{
    use HasFactory;

    protected $fillable = [
        'role_id',
        'permission_id',
        'access_level',
    ];

    protected $casts = [
        'access_level' => 'string',
    ];

    /**
     * Get the role.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the permission.
     */
    public function permission(): BelongsTo
    {
        return $this->belongsTo(Permission::class);
    }

    /**
     * Check if access level allows full access.
     */
    public function allowsFullAccess(): bool
    {
        return $this->access_level === 'full';
    }

    /**
     * Check if access level allows view access.
     */
    public function allowsViewAccess(): bool
    {
        return in_array($this->access_level, ['full', 'view']);
    }
}
