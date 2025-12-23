<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mou extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'status',
        'start_date',
        'end_date',
        'signed_date',
        'file_path',
        'remarks',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'signed_date' => 'date',
    ];

    /**
     * Get the company that owns the MoU.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the user who created the MoU.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the MoU.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get status options.
     */
    public static function getStatusOptions(): array
    {
        return [
            'Not Initiated' => 'Not Initiated',
            'In Progress' => 'In Progress',
            'Signed' => 'Signed',
            'Expired' => 'Expired',
            'Not Responding' => 'Not Responding',
        ];
    }

    /**
     * Check if MoU is expired.
     */
    public function isExpired(): bool
    {
        if (! $this->end_date) {
            return false;
        }

        return $this->end_date < now();
    }

    /**
     * Get status badge color.
     */
    public function getStatusBadgeColorAttribute(): string
    {
        return match ($this->status) {
            'Signed' => 'green',
            'In Progress' => 'yellow',
            'Not Responding' => 'red',
            'Not Initiated' => 'gray',
            'Expired' => 'black',
            default => 'gray',
        };
    }
}
