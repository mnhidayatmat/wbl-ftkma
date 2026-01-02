<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'assessment_id',
        'file_path',
        'file_name',
        'original_name',
        'file_size',
        'mime_type',
        'attempt_number',
        'is_late',
        'late_penalty_applied',
        'declaration_accepted',
        'student_remarks',
        'status',
        'submitted_at',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'attempt_number' => 'integer',
        'is_late' => 'boolean',
        'late_penalty_applied' => 'decimal:2',
        'declaration_accepted' => 'boolean',
        'submitted_at' => 'datetime',
    ];

    /**
     * Status constants.
     */
    public const STATUS_DRAFT = 'draft';

    public const STATUS_SUBMITTED = 'submitted';

    public const STATUS_EVALUATED = 'evaluated';

    /**
     * Get the student who made this submission.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the assessment this submission is for.
     */
    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    /**
     * Get file size in human readable format.
     */
    public function getFileSizeHumanAttribute(): string
    {
        $bytes = $this->file_size;

        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2).' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2).' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2).' KB';
        }

        return $bytes.' bytes';
    }

    /**
     * Get status badge configuration.
     */
    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            self::STATUS_DRAFT => [
                'label' => 'Draft',
                'color' => 'gray',
                'bg' => 'bg-gray-100 dark:bg-gray-700',
                'text' => 'text-gray-700 dark:text-gray-300',
            ],
            self::STATUS_SUBMITTED => [
                'label' => $this->is_late ? 'Submitted (Late)' : 'Submitted',
                'color' => $this->is_late ? 'yellow' : 'blue',
                'bg' => $this->is_late ? 'bg-yellow-100 dark:bg-yellow-900/30' : 'bg-blue-100 dark:bg-blue-900/30',
                'text' => $this->is_late ? 'text-yellow-700 dark:text-yellow-300' : 'text-blue-700 dark:text-blue-300',
            ],
            self::STATUS_EVALUATED => [
                'label' => 'Evaluated',
                'color' => 'green',
                'bg' => 'bg-green-100 dark:bg-green-900/30',
                'text' => 'text-green-700 dark:text-green-300',
            ],
            default => [
                'label' => ucfirst($this->status),
                'color' => 'gray',
                'bg' => 'bg-gray-100 dark:bg-gray-700',
                'text' => 'text-gray-700 dark:text-gray-300',
            ],
        };
    }

    /**
     * Get file icon based on mime type.
     */
    public function getFileIconAttribute(): string
    {
        return match (true) {
            str_contains($this->mime_type, 'pdf') => 'file-pdf',
            str_contains($this->mime_type, 'word') => 'file-word',
            str_contains($this->mime_type, 'presentation') || str_contains($this->mime_type, 'powerpoint') => 'file-powerpoint',
            str_contains($this->mime_type, 'spreadsheet') || str_contains($this->mime_type, 'excel') => 'file-excel',
            str_contains($this->mime_type, 'zip') => 'file-zip',
            str_contains($this->mime_type, 'image') => 'file-image',
            default => 'file',
        };
    }

    /**
     * Scope to get only submitted (not draft) submissions.
     */
    public function scopeSubmitted($query)
    {
        return $query->where('status', '!=', self::STATUS_DRAFT);
    }

    /**
     * Scope to get late submissions.
     */
    public function scopeLate($query)
    {
        return $query->where('is_late', true);
    }

    /**
     * Scope to get submissions for a specific student.
     */
    public function scopeForStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    /**
     * Scope to get latest submission per student.
     */
    public function scopeLatestAttempt($query)
    {
        return $query->orderBy('attempt_number', 'desc');
    }

    /**
     * Get the latest submission for a student on an assessment.
     */
    public static function getLatestForStudent(int $studentId, int $assessmentId): ?self
    {
        return static::where('student_id', $studentId)
            ->where('assessment_id', $assessmentId)
            ->orderBy('attempt_number', 'desc')
            ->first();
    }

    /**
     * Get remaining attempts for a student on an assessment.
     */
    public static function getRemainingAttempts(int $studentId, int $assessmentId): int
    {
        $assessment = Assessment::find($assessmentId);
        if (! $assessment) {
            return 0;
        }

        $usedAttempts = static::where('student_id', $studentId)
            ->where('assessment_id', $assessmentId)
            ->count();

        return max(0, ($assessment->max_attempts ?? 1) - $usedAttempts);
    }

    /**
     * Check if student can submit more attempts.
     */
    public static function canSubmit(int $studentId, int $assessmentId): bool
    {
        return static::getRemainingAttempts($studentId, $assessmentId) > 0;
    }
}
