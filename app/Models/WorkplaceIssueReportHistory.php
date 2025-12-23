<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkplaceIssueReportHistory extends Model
{
    use HasFactory;

    protected $table = 'workplace_issue_report_history';

    protected $fillable = [
        'issue_report_id',
        'user_id',
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

    public function issueReport(): BelongsTo
    {
        return $this->belongsTo(WorkplaceIssueReport::class, 'issue_report_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getActionLabelAttribute(): string
    {
        return match ($this->action) {
            'CREATED' => 'Issue Created',
            'STATUS_CHANGED' => 'Status Changed',
            'COMMENT_ADDED' => 'Comment Added',
            'COMMENT_UPDATED' => 'Comment Updated',
            'ASSIGNED' => 'Issue Assigned',
            'REVIEWED' => 'Issue Reviewed',
            'IN_PROGRESS' => 'Marked In Progress',
            'RESOLVED' => 'Issue Resolved',
            'CLOSED' => 'Issue Closed',
            'REOPENED' => 'Issue Reopened',
            'ATTACHMENT_ADDED' => 'Attachment Added',
            default => ucfirst(str_replace('_', ' ', strtolower($this->action))),
        };
    }

    public function getActionIconColorAttribute(): string
    {
        return match ($this->action) {
            'CREATED' => 'text-purple-600 dark:text-purple-400',
            'STATUS_CHANGED' => 'text-blue-600 dark:text-blue-400',
            'COMMENT_ADDED', 'COMMENT_UPDATED' => 'text-indigo-600 dark:text-indigo-400',
            'ASSIGNED' => 'text-cyan-600 dark:text-cyan-400',
            'REVIEWED' => 'text-blue-600 dark:text-blue-400',
            'IN_PROGRESS' => 'text-yellow-600 dark:text-yellow-400',
            'RESOLVED' => 'text-green-600 dark:text-green-400',
            'CLOSED' => 'text-gray-600 dark:text-gray-400',
            'REOPENED' => 'text-orange-600 dark:text-orange-400',
            'ATTACHMENT_ADDED' => 'text-teal-600 dark:text-teal-400',
            default => 'text-gray-600 dark:text-gray-400',
        };
    }

    /**
     * Static method to create a history log entry.
     */
    public static function log(
        int $issueReportId,
        string $action,
        ?string $status = null,
        ?string $comment = null,
        ?string $previousComment = null,
        array $metadata = []
    ): self {
        return self::create([
            'issue_report_id' => $issueReportId,
            'user_id' => auth()->id(),
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
