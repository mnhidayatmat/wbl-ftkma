<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkplaceIssueReport extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_id',
        'group_id',
        'title',
        'description',
        'category',
        'custom_category',
        'severity',
        'location',
        'incident_date',
        'incident_time',
        'status',
        'coordinator_comment',
        'resolution_notes',
        'assigned_to',
        'reviewed_by',
        'resolved_by',
        'closed_by',
        'submitted_at',
        'reviewed_at',
        'in_progress_at',
        'resolved_at',
        'closed_at',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'in_progress_at' => 'datetime',
        'resolved_at' => 'datetime',
        'closed_at' => 'datetime',
        'incident_date' => 'date',
    ];

    // Relationships
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(WblGroup::class, 'group_id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(WorkplaceIssueAttachment::class, 'issue_report_id');
    }

    public function history(): HasMany
    {
        return $this->hasMany(WorkplaceIssueReportHistory::class, 'issue_report_id');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function resolvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function closedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    // Helper Methods
    public function getCategoryDisplayAttribute(): string
    {
        return match ($this->category) {
            'safety_health' => 'Safety & Health Hazards',
            'harassment_discrimination' => 'Harassment & Discrimination',
            'work_environment' => 'Work Environment Issues',
            'supervision_guidance' => 'Supervision & Guidance Problems',
            'custom' => $this->custom_category ?? 'Other',
            default => 'Unknown',
        };
    }

    public function getSeverityDisplayAttribute(): string
    {
        return ucfirst($this->severity);
    }

    public function getSeverityBadgeColorAttribute(): string
    {
        return match ($this->severity) {
            'low' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
            'medium' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
            'high' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
            'critical' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200',
        };
    }

    public function getStatusDisplayAttribute(): string
    {
        return match ($this->status) {
            'new' => 'New',
            'under_review' => 'Under Review',
            'in_progress' => 'In Progress',
            'resolved' => 'Resolved',
            'closed' => 'Closed',
            default => 'Unknown',
        };
    }

    public function getStatusBadgeColorAttribute(): string
    {
        return match ($this->status) {
            'new' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
            'under_review' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
            'in_progress' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
            'resolved' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
            'closed' => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200',
        };
    }

    public function isNew(): bool
    {
        return $this->status === 'new';
    }

    public function isUnderReview(): bool
    {
        return $this->status === 'under_review';
    }

    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    public function isResolved(): bool
    {
        return $this->status === 'resolved';
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    public function canBeUpdatedByStudent(): bool
    {
        // Students can only view, not update after submission
        return false;
    }

    public function canBeUpdatedByCoordinator(): bool
    {
        // Coordinators can update if not closed
        return !$this->isClosed();
    }

    // Scope queries
    public function scopeByStudent($query, int $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeBySeverity($query, string $severity)
    {
        return $query->where('severity', $severity);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('submitted_at', 'desc');
    }

    public function scopeOpen($query)
    {
        return $query->whereIn('status', ['new', 'under_review', 'in_progress']);
    }
}
