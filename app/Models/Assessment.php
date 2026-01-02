<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Assessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_code',
        'assessment_name',
        'description',
        'assessment_type',
        'clo_code',
        'weight_percentage',
        'evaluator_role',
        'is_active',
        'created_by',
        // Submission Settings
        'requires_submission',
        'submission_deadline',
        'allowed_file_types',
        'max_file_size_mb',
        'max_attempts',
        'allow_late_submission',
        'late_penalty_per_day',
        'max_late_days',
        'require_declaration',
        'submission_instructions',
    ];

    protected $casts = [
        'weight_percentage' => 'decimal:2',
        'is_active' => 'boolean',
        // Submission Settings Casts
        'requires_submission' => 'boolean',
        'submission_deadline' => 'datetime',
        'allowed_file_types' => 'array',
        'max_file_size_mb' => 'integer',
        'max_attempts' => 'integer',
        'allow_late_submission' => 'boolean',
        'late_penalty_per_day' => 'decimal:2',
        'max_late_days' => 'integer',
        'require_declaration' => 'boolean',
    ];

    /**
     * Default submission presets by assessment type.
     */
    public const SUBMISSION_PRESETS = [
        'Assignment' => [
            'requires_submission' => true,
            'allowed_file_types' => ['pdf', 'docx', 'doc', 'zip'],
            'max_file_size_mb' => 10,
        ],
        'Report' => [
            'requires_submission' => true,
            'allowed_file_types' => ['pdf', 'docx', 'doc'],
            'max_file_size_mb' => 25,
        ],
        'Presentation' => [
            'requires_submission' => true,
            'allowed_file_types' => ['pptx', 'ppt', 'pdf'],
            'max_file_size_mb' => 50,
        ],
        'Project' => [
            'requires_submission' => true,
            'allowed_file_types' => ['pdf', 'docx', 'doc', 'zip'],
            'max_file_size_mb' => 50,
        ],
        'Oral' => [
            'requires_submission' => false,
            'allowed_file_types' => [],
            'max_file_size_mb' => 10,
        ],
        'Rubric' => [
            'requires_submission' => false,
            'allowed_file_types' => [],
            'max_file_size_mb' => 10,
        ],
        'Logbook' => [
            'requires_submission' => false,
            'allowed_file_types' => [],
            'max_file_size_mb' => 10,
        ],
        'Observation' => [
            'requires_submission' => false,
            'allowed_file_types' => [],
            'max_file_size_mb' => 10,
        ],
        'Quiz' => [
            'requires_submission' => false,
            'allowed_file_types' => [],
            'max_file_size_mb' => 10,
        ],
        'Test' => [
            'requires_submission' => false,
            'allowed_file_types' => [],
            'max_file_size_mb' => 10,
        ],
        'Final Exam' => [
            'requires_submission' => false,
            'allowed_file_types' => [],
            'max_file_size_mb' => 10,
        ],
    ];

    /**
     * Get the admin user who created this assessment.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all student marks for this assessment.
     */
    public function studentMarks()
    {
        return $this->hasMany(StudentAssessmentMark::class);
    }

    /**
     * Get all rubric questions for this assessment (if Oral/Rubric type).
     */
    public function rubrics()
    {
        return $this->hasMany(AssessmentRubric::class)->ordered();
    }

    /**
     * Get all CLOs for this assessment (for FYP multiple CLO support).
     */
    public function clos()
    {
        return $this->hasMany(AssessmentClo::class)->orderBy('order');
    }

    /**
     * Get all components for this assessment (sub-questions/elements).
     */
    public function components()
    {
        return $this->hasMany(AssessmentComponent::class)->ordered();
    }

    /**
     * Get all evaluators for this assessment (for multiple evaluator support).
     */
    public function evaluators()
    {
        return $this->hasMany(AssessmentEvaluator::class)->ordered();
    }

    /**
     * Get the rubric report for this assessment.
     */
    public function rubricReport(): HasOne
    {
        return $this->hasOne(AssessmentRubricReport::class);
    }

    /**
     * Get all student submissions for this assessment.
     */
    public function submissions(): HasMany
    {
        return $this->hasMany(StudentSubmission::class);
    }

    /**
     * Check if this assessment requires student submission.
     */
    public function requiresSubmission(): bool
    {
        return (bool) $this->requires_submission;
    }

    /**
     * Check if submission window is currently open.
     */
    public function isSubmissionOpen(): bool
    {
        if (! $this->requires_submission) {
            return false;
        }

        if (! $this->submission_deadline) {
            return true; // No deadline means always open
        }

        return now()->lt($this->submission_deadline);
    }

    /**
     * Check if a submission at given time would be late.
     */
    public function isLateSubmission(?Carbon $submittedAt = null): bool
    {
        if (! $this->submission_deadline) {
            return false;
        }

        $submittedAt = $submittedAt ?? now();

        return $submittedAt->gt($this->submission_deadline);
    }

    /**
     * Calculate late penalty percentage for a submission.
     */
    public function calculateLatePenalty(?Carbon $submittedAt = null): float
    {
        if (! $this->allow_late_submission || ! $this->late_penalty_per_day) {
            return 0;
        }

        if (! $this->isLateSubmission($submittedAt)) {
            return 0;
        }

        $submittedAt = $submittedAt ?? now();
        $daysLate = (int) ceil($submittedAt->diffInHours($this->submission_deadline) / 24);

        // Cap at max late days if set
        if ($this->max_late_days && $daysLate > $this->max_late_days) {
            return 100; // 100% penalty = no marks
        }

        $penalty = $daysLate * (float) $this->late_penalty_per_day;

        return min($penalty, 100); // Cap at 100%
    }

    /**
     * Check if late submissions are still accepted.
     */
    public function acceptsLateSubmission(): bool
    {
        if (! $this->allow_late_submission) {
            return false;
        }

        if (! $this->max_late_days || ! $this->submission_deadline) {
            return true;
        }

        $maxLateDate = $this->submission_deadline->copy()->addDays($this->max_late_days);

        return now()->lt($maxLateDate);
    }

    /**
     * Get allowed file extensions as array.
     */
    public function getAllowedExtensions(): array
    {
        return $this->allowed_file_types ?? [];
    }

    /**
     * Get allowed MIME types based on file extensions.
     */
    public function getAllowedMimeTypes(): array
    {
        $mimeMap = [
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'ppt' => 'application/vnd.ms-powerpoint',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'zip' => 'application/zip',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
        ];

        $mimes = [];
        foreach ($this->getAllowedExtensions() as $ext) {
            if (isset($mimeMap[$ext])) {
                $mimes[] = $mimeMap[$ext];
            }
        }

        return array_unique($mimes);
    }

    /**
     * Get max file size in bytes.
     */
    public function getMaxFileSizeBytes(): int
    {
        return ($this->max_file_size_mb ?? 10) * 1024 * 1024;
    }

    /**
     * Get submission preset for an assessment type.
     */
    public static function getSubmissionPreset(string $type): array
    {
        return self::SUBMISSION_PRESETS[$type] ?? [
            'requires_submission' => false,
            'allowed_file_types' => [],
            'max_file_size_mb' => 10,
        ];
    }

    /**
     * Check if this assessment is rubric-based.
     */
    public function isRubricBased(): bool
    {
        return in_array($this->assessment_type, ['Oral', 'Rubric', 'Report', 'Logbook']);
    }

    /**
     * Check if this assessment is Logbook type.
     */
    public function isLogbookType(): bool
    {
        return $this->assessment_type === 'Logbook';
    }

    /**
     * Scope to filter by course code.
     */
    public function scopeForCourse($query, string $courseCode)
    {
        return $query->where('course_code', $courseCode);
    }

    /**
     * Scope to filter by evaluator role.
     */
    public function scopeForEvaluator($query, string $role)
    {
        return $query->where('evaluator_role', $role);
    }

    /**
     * Scope to get only active assessments.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get course codes as constants.
     */
    public static function getCourseCodes(): array
    {
        return [
            'PPE' => 'Professional Practice & Ethics',
            'IP' => 'Internship Preparation',
            'OSH' => 'Occupational Safety & Health',
            'FYP' => 'Final Year Project',
            'LI' => 'Industrial Training',
        ];
    }

    /**
     * Get assessment types.
     */
    public static function getAssessmentTypes(): array
    {
        return [
            'Assignment' => 'Assignment',
            'Report' => 'Report',
            'Presentation' => 'Presentation',
            'Oral' => 'Oral Examination',
            'Rubric' => 'Rubric Evaluation',
            'Logbook' => 'Logbook',
            'Observation' => 'Observation',
            'Project' => 'Project',
            'Quiz' => 'Quiz',
            'Test' => 'Test',
            'Final Exam' => 'Final Exam',
        ];
    }

    /**
     * Get evaluator roles.
     */
    public static function getEvaluatorRoles(): array
    {
        return [
            'lecturer' => 'Lecturer',
            'at' => 'Academic Tutor (AT)',
            'ic' => 'Industry Coach (IC)',
            'supervisor_li' => 'Supervisor LI',
        ];
    }

    /**
     * Get CLO codes for a course.
     *
     * Now uses database-driven CLO counts from course_clo_settings table.
     * Admins can configure the number of CLOs per course via the CLO-PLO settings page.
     */
    public static function getCloCodes(string $courseCode): array
    {
        return CourseCloSetting::getCloCodes($courseCode);
    }

    /**
     * Get CLO count for a course.
     */
    public static function getCloCount(string $courseCode): int
    {
        return CourseCloSetting::getCloCount($courseCode);
    }

    /**
     * Generate CLO codes dynamically (helper method for future use).
     *
     * @param  int  $count  Number of CLOs to generate
     * @return array Array of CLO codes
     */
    public static function generateCloCodes(int $count): array
    {
        $clos = [];
        for ($i = 1; $i <= $count; $i++) {
            $clos[] = 'CLO'.$i;
        }

        return $clos;
    }
}
