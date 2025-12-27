<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecruitmentHandover extends Model
{
    protected $fillable = [
        'company_id',
        'recruiter_emails',
        'student_ids',
        'student_count',
        'message',
        'handed_over_by',
        'filters_applied',
    ];

    protected $casts = [
        'recruiter_emails' => 'array',
        'student_ids' => 'array',
        'filters_applied' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Get the company this handover is for.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the user who created this handover.
     */
    public function handedOverBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handed_over_by');
    }

    /**
     * Get students involved in this handover.
     */
    public function students()
    {
        return Student::whereIn('id', $this->student_ids ?? [])->get();
    }

    /**
     * Get recruiter emails as comma-separated string.
     */
    public function getRecruiterEmailsStringAttribute(): string
    {
        return implode(', ', $this->recruiter_emails ?? []);
    }
}
