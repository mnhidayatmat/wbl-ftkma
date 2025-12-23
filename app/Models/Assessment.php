<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    ];

    protected $casts = [
        'weight_percentage' => 'decimal:2',
        'is_active' => 'boolean',
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
     * @param int $count Number of CLOs to generate
     * @return array Array of CLO codes
     */
    public static function generateCloCodes(int $count): array
    {
        $clos = [];
        for ($i = 1; $i <= $count; $i++) {
            $clos[] = 'CLO' . $i;
        }
        return $clos;
    }
}
