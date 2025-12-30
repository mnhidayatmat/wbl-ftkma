<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResumeInspectionHistory extends Model
{
    use HasFactory;

    protected $table = 'resume_inspection_history';

    protected $fillable = [
        'inspection_id',
        'reviewed_by',
        'action',
        'status',
        'comment',
        'previous_comment',
        'metadata',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the inspection this history belongs to.
     */
    public function inspection(): BelongsTo
    {
        return $this->belongsTo(StudentResumeInspection::class, 'inspection_id');
    }

    /**
     * Get the user who performed the action.
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Get action display label.
     */
    public function getActionLabelAttribute(): string
    {
        return match ($this->action) {
            'REVIEWED' => 'Reviewed',
            'APPROVED' => 'Approved',
            'REVISION_REQUESTED' => 'Revision Requested',
            'COMMENT_ADDED' => 'Comment Added',
            'COMMENT_UPDATED' => 'Comment Updated',
            'RESET' => 'Reset',
            'STUDENT_REPLY' => 'Student Reply',
            'STUDENT_REPLY_UPDATED' => 'Student Reply Updated',
            default => ucfirst(str_replace('_', ' ', strtolower($this->action))),
        };
    }

    /**
     * Check if this is a student action.
     */
    public function isStudentAction(): bool
    {
        return in_array($this->action, ['STUDENT_REPLY', 'STUDENT_REPLY_UPDATED'])
            || ($this->metadata['is_student_action'] ?? false);
    }

    /**
     * Get the actor name (student or reviewer).
     */
    public function getActorNameAttribute(): ?string
    {
        if ($this->isStudentAction()) {
            return $this->metadata['student_name'] ?? 'Student';
        }
        return $this->reviewer?->name;
    }

    /**
     * Get action icon color.
     */
    public function getActionIconColorAttribute(): string
    {
        return match ($this->action) {
            'APPROVED' => 'text-green-600 dark:text-green-400',
            'REVISION_REQUESTED' => 'text-orange-600 dark:text-orange-400',
            'COMMENT_ADDED', 'COMMENT_UPDATED' => 'text-blue-600 dark:text-blue-400',
            'RESET' => 'text-red-600 dark:text-red-400',
            'STUDENT_REPLY', 'STUDENT_REPLY_UPDATED' => 'text-purple-600 dark:text-purple-400',
            default => 'text-gray-600 dark:text-gray-400',
        };
    }

    /**
     * Get status badge color.
     */
    public function getStatusBadgeColorAttribute(): string
    {
        return match ($this->status) {
            'PENDING' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
            'PASSED' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
            'FAILED' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
            'REVISION_REQUIRED' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200',
        };
    }

    /**
     * Create a history log entry.
     */
    public static function log(
        int $inspectionId,
        string $action,
        ?string $status = null,
        ?string $comment = null,
        ?string $previousComment = null,
        array $metadata = []
    ): self {
        return self::create([
            'inspection_id' => $inspectionId,
            'reviewed_by' => auth()->id(),
            'action' => $action,
            'status' => $status,
            'comment' => $comment,
            'previous_comment' => $previousComment,
            'metadata' => $metadata,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
