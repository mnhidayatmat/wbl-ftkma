<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Moa extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'moa_type',
        'course_code',
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
     * Get the company that owns the MoA.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the students linked to this MoA.
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'moa_student');
    }

    /**
     * Get the user who created the MoA.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the MoA.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get MoA type options.
     */
    public static function getMoaTypeOptions(): array
    {
        return [
            'Programme-based' => 'Programme-based',
            'Student-based' => 'Student-based',
        ];
    }

    /**
     * Get status options.
     */
    public static function getStatusOptions(): array
    {
        return [
            'Draft' => 'Draft',
            'In Progress' => 'In Progress',
            'Signed' => 'Signed',
            'Expired' => 'Expired',
        ];
    }

    /**
     * Get course code options.
     */
    public static function getCourseCodeOptions(): array
    {
        return [
            'PPE' => 'PPE',
            'IP' => 'IP',
            'OSH' => 'OSH',
            'FYP' => 'FYP',
            'LI' => 'LI',
        ];
    }

    /**
     * Check if MoA is expired.
     */
    public function isExpired(): bool
    {
        if (!$this->end_date) {
            return false;
        }
        return $this->end_date < now();
    }

    /**
     * Get status badge color.
     */
    public function getStatusBadgeColorAttribute(): string
    {
        return match($this->status) {
            'Signed' => 'green',
            'In Progress' => 'yellow',
            'Draft' => 'gray',
            'Expired' => 'red',
            default => 'gray',
        };
    }
}
