<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Programme extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'short_code',
        'wbl_coordinator_role',
        'wbl_coordinator_name',
        'wbl_coordinator_email',
        'wbl_coordinator_phone',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get all students in this programme.
     */
    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'programme', 'name');
    }

    /**
     * Scope to get only active programmes.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order by name.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('name');
    }

    /**
     * Get the WBL Coordinator for this programme.
     */
    public function getWblCoordinator(): ?User
    {
        if (! $this->wbl_coordinator_role) {
            return null;
        }

        return User::whereHas('roles', function ($query) {
            $query->where('name', $this->wbl_coordinator_role);
        })->first();
    }

    /**
     * Get all active programmes for dropdown.
     */
    public static function getForDropdown(): array
    {
        return static::active()
            ->ordered()
            ->pluck('name', 'name')
            ->toArray();
    }
}
