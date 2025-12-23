<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentResumeInspection extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'resume_file_path',
        'portfolio_file_path',
        'status',
        'coordinator_comment',
        'student_reply',
        'student_replied_at',
        'reviewed_by',
        'reviewed_at',
        'approved_at',
        'checklist_merged_pdf',
        'checklist_document_order',
        'checklist_resume_concise',
        'checklist_achievements_highlighted',
        'checklist_poster_includes_required',
        'checklist_poster_pages_limit',
        'checklist_own_work_ready',
        'checklist_confirmed_at',
        'checklist_ip_address',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'student_replied_at' => 'datetime',
        'approved_at' => 'datetime',
        'checklist_confirmed_at' => 'datetime',
        'checklist_merged_pdf' => 'boolean',
        'checklist_document_order' => 'boolean',
        'checklist_resume_concise' => 'boolean',
        'checklist_achievements_highlighted' => 'boolean',
        'checklist_poster_includes_required' => 'boolean',
        'checklist_poster_pages_limit' => 'boolean',
        'checklist_own_work_ready' => 'boolean',
    ];

    /**
     * Get the student that owns this resume inspection.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the user who reviewed this resume.
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Get the history logs for this inspection.
     */
    public function history(): HasMany
    {
        return $this->hasMany(ResumeInspectionHistory::class, 'inspection_id');
    }

    /**
     * Get the status display name.
     */
    public function getStatusDisplayAttribute(): string
    {
        return match($this->status) {
            'PENDING' => 'Pending Review',
            'PASSED' => 'Approved',
            'FAILED' => 'Rejected',
            'REVISION_REQUIRED' => 'Revision Required',
            default => 'Unknown',
        };
    }

    /**
     * Get the status badge color.
     */
    public function getStatusBadgeColorAttribute(): string
    {
        return match($this->status) {
            'PENDING' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
            'PASSED' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
            'FAILED' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
            'REVISION_REQUIRED' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200',
        };
    }

    /**
     * Check if resume is passed/approved.
     */
    public function isPassed(): bool
    {
        return $this->status === 'PASSED';
    }

    /**
     * Check if resume is approved (alias for isPassed).
     */
    public function isApproved(): bool
    {
        return $this->isPassed();
    }

    /**
     * Check if resume is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'PENDING';
    }

    /**
     * Check if revision is required.
     */
    public function isRevisionRequired(): bool
    {
        return $this->status === 'REVISION_REQUIRED';
    }

    /**
     * Check if resume is not submitted.
     */
    public function isNotSubmitted(): bool
    {
        return empty($this->resume_file_path);
    }

    /**
     * Check if all checklist items are confirmed.
     */
    public function isChecklistComplete(): bool
    {
        return $this->checklist_merged_pdf &&
               $this->checklist_document_order &&
               $this->checklist_resume_concise &&
               $this->checklist_achievements_highlighted &&
               $this->checklist_poster_includes_required &&
               $this->checklist_poster_pages_limit &&
               $this->checklist_own_work_ready;
    }

    /**
     * Get checklist completion count.
     */
    public function getChecklistCompletionCount(): int
    {
        $items = [
            $this->checklist_merged_pdf,
            $this->checklist_document_order,
            $this->checklist_resume_concise,
            $this->checklist_achievements_highlighted,
            $this->checklist_poster_includes_required,
            $this->checklist_poster_pages_limit,
            $this->checklist_own_work_ready,
        ];

        return count(array_filter($items));
    }
}
