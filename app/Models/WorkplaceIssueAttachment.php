<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkplaceIssueAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'issue_report_id',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'mime_type',
        'uploaded_by',
    ];

    public function issueReport(): BelongsTo
    {
        return $this->belongsTo(WorkplaceIssueReport::class, 'issue_report_id');
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getFormattedFileSizeAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, 2) . ' ' . $units[$pow];
    }
}
