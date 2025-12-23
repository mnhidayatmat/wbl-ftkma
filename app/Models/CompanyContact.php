<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyContact extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'role',
        'email',
        'phone',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    /**
     * Get the company that owns the contact.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get role options.
     */
    public static function getRoleOptions(): array
    {
        return [
            'HR' => 'HR',
            'Supervisor' => 'Supervisor',
            'Manager' => 'Manager',
            'Industry Coach' => 'Industry Coach',
        ];
    }
}
