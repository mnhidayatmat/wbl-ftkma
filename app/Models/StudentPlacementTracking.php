<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentPlacementTracking extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'student_placement_tracking';

    protected $fillable = [
        'student_id',
        'group_id',
        'status',
        'sal_released_at',
        'sal_released_by',
        'sal_file_path',
        'scl_released_at',
        'scl_released_by',
        'scl_file_path',
        'confirmation_proof_path',
        'notes',
        'updated_by',
        'companies_applied_count',
        'first_application_date',
        'last_application_date',
        'application_methods',
        'application_notes',
        'applied_status_set_at',
        'applied_at',
        'interviewed_at',
        'offer_received_at',
        'accepted_at',
        'confirmed_at',
    ];

    protected function casts(): array
    {
        return [
            'sal_released_at' => 'datetime',
            'scl_released_at' => 'datetime',
            'applied_status_set_at' => 'datetime',
            'applied_at' => 'datetime',
            'interviewed_at' => 'datetime',
            'offer_received_at' => 'datetime',
            'accepted_at' => 'datetime',
            'confirmed_at' => 'datetime',
            'first_application_date' => 'date',
            'last_application_date' => 'date',
            'application_methods' => 'array',
            'companies_applied_count' => 'integer',
        ];
    }

    /**
     * Get the student.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the group.
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(WblGroup::class, 'group_id');
    }

    /**
     * Get the user who released SAL.
     */
    public function salReleasedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sal_released_by');
    }

    /**
     * Get the user who released SCL.
     */
    public function sclReleasedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'scl_released_by');
    }

    /**
     * Get the user who last updated.
     */
    public function updatedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get status display name.
     */
    public function getStatusDisplayAttribute(): string
    {
        // ACCEPTED status - show as "Accepted" (confirmation proof is separate)
        return match($this->status) {
            'NOT_APPLIED' => 'Resume Recommended',
            'SAL_RELEASED' => 'SAL Released',
            'APPLIED' => 'Applied',
            'INTERVIEWED' => 'Interviewed',
            'OFFER_RECEIVED' => 'Offer Received',
            'ACCEPTED' => 'Accepted',
            'SCL_RELEASED' => 'SCL Released',
            default => $this->status,
        };
    }

    /**
     * Get status badge color.
     */
    public function getStatusBadgeColorAttribute(): string
    {
        return match($this->status) {
            'NOT_APPLIED' => 'bg-gray-100 text-gray-800',
            'SAL_RELEASED' => 'bg-blue-100 text-blue-800',
            'APPLIED' => 'bg-yellow-100 text-yellow-800',
            'INTERVIEWED' => 'bg-purple-100 text-purple-800',
            'OFFER_RECEIVED' => 'bg-indigo-100 text-indigo-800',
            'ACCEPTED' => 'bg-green-100 text-green-800',
            'SCL_RELEASED' => 'bg-teal-100 text-teal-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Check if SAL can be released.
     */
    public function canReleaseSal(): bool
    {
        return in_array($this->status, ['NOT_APPLIED', 'SAL_RELEASED']);
    }

    /**
     * Check if SCL can be released.
     */
    public function canReleaseScl(): bool
    {
        return $this->status === 'ACCEPTED' && !empty($this->confirmation_proof_path);
    }

    /**
     * Get application evidence files.
     */
    public function applicationEvidence(): HasMany
    {
        return $this->hasMany(PlacementApplicationEvidence::class, 'placement_tracking_id');
    }

    /**
     * Get company applications.
     */
    public function companyApplications(): HasMany
    {
        return $this->hasMany(PlacementCompanyApplication::class, 'placement_tracking_id');
    }

    /**
     * Check if application data is complete.
     */
    public function hasApplicationData(): bool
    {
        return $this->companies_applied_count > 0 && !empty($this->first_application_date);
    }

    /**
     * Get days since applied status was set.
     */
    public function getDaysSinceAppliedAttribute(): ?int
    {
        if (!$this->applied_status_set_at) {
            return null;
        }
        return now()->diffInDays($this->applied_status_set_at);
    }

    /**
     * Get activity status indicator.
     */
    public function getActivityStatusAttribute(): string
    {
        if ($this->status !== 'APPLIED') {
            return 'none';
        }

        $daysSince = $this->days_since_applied;
        if ($daysSince === null) {
            return 'unknown';
        }

        if ($this->companies_applied_count === 0) {
            return 'no_activity';
        } elseif ($this->companies_applied_count <= 2) {
            return 'low_activity';
        } elseif ($daysSince > 14) {
            return 'inactive';
        } else {
            return 'active';
        }
    }
}
