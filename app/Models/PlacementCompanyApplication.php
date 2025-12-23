<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlacementCompanyApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'placement_tracking_id',
        'company_name',
        'application_deadline',
        'application_method',
        'application_method_other',
        'interviewed',
        'interviewed_at',
    ];

    protected $casts = [
        'application_deadline' => 'date',
        'interviewed' => 'boolean',
        'interviewed_at' => 'datetime',
    ];

    /**
     * Get the placement tracking.
     */
    public function placementTracking(): BelongsTo
    {
        return $this->belongsTo(StudentPlacementTracking::class, 'placement_tracking_id');
    }

    /**
     * Get application method display name.
     */
    public function getApplicationMethodDisplayAttribute(): string
    {
        if ($this->application_method === 'other' && $this->application_method_other) {
            return $this->application_method_other;
        }
        
        return match($this->application_method) {
            'through_coordinator' => 'Through Coordinator',
            'job_portal' => 'Job Portal',
            'company_website' => 'Company Website',
            'email' => 'Email',
            'career_fair' => 'Career Fair',
            'referral' => 'Referral',
            'other' => $this->application_method_other ?? 'Other',
            default => $this->application_method,
        };
    }
}
