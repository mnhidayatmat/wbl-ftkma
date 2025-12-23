<?php

namespace App\Models\FYP;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FypRubricTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'assessment_type',
        'phase',
        'evaluator_role',
        'course_code',
        'description',
        'total_weight',
        'component_marks',
        'is_active',
        'is_locked',
        'created_by',
    ];

    protected $casts = [
        'total_weight' => 'decimal:2',
        'component_marks' => 'decimal:2',
        'is_active' => 'boolean',
        'is_locked' => 'boolean',
    ];

    /**
     * Evaluator roles
     */
    public const EVALUATOR_ROLES = [
        'at' => 'Academic Tutor (AT)',
        'ic' => 'Industry Coach (IC)',
    ];

    /**
     * Performance levels with labels
     */
    public const PERFORMANCE_LEVELS = [
        1 => 'AWARE',
        2 => 'LIMITED',
        3 => 'FAIR',
        4 => 'GOOD',
        5 => 'EXCELLENT',
    ];

    /**
     * Assessment types
     */
    public const ASSESSMENT_TYPES = [
        'Written' => 'Written Report',
        'Oral' => 'Oral Presentation',
    ];

    /**
     * Phases
     */
    public const PHASES = [
        'Mid-Term' => 'Mid-Term',
        'Final' => 'Final',
    ];

    /**
     * Get the creator of this template.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all elements for this rubric template.
     */
    public function elements(): HasMany
    {
        return $this->hasMany(FypRubricElement::class, 'rubric_template_id')->orderBy('order');
    }

    /**
     * Get all evaluations for this rubric template.
     */
    public function evaluations(): HasMany
    {
        return $this->hasMany(FypRubricEvaluation::class, 'rubric_template_id');
    }

    /**
     * Get all overall feedback for this rubric template.
     */
    public function overallFeedback(): HasMany
    {
        return $this->hasMany(FypRubricOverallFeedback::class, 'rubric_template_id');
    }

    /**
     * Scope to filter active templates.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by course.
     */
    public function scopeForCourse($query, string $courseCode)
    {
        return $query->where('course_code', $courseCode);
    }

    /**
     * Scope to filter by assessment type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('assessment_type', $type);
    }

    /**
     * Scope to filter by phase.
     */
    public function scopeForPhase($query, string $phase)
    {
        return $query->where('phase', $phase);
    }

    /**
     * Scope to filter by evaluator role.
     */
    public function scopeForEvaluator($query, string $role)
    {
        return $query->where('evaluator_role', $role);
    }

    /**
     * Get the full name combining phase and type.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->phase} {$this->assessment_type} - {$this->name}";
    }

    /**
     * Calculate total weight from elements.
     */
    public function calculateTotalWeight(): float
    {
        return $this->elements()->sum('weight_percentage');
    }

    /**
     * Check if total weight equals 100%.
     */
    public function isWeightValid(): bool
    {
        return abs($this->calculateTotalWeight() - 100) < 0.01;
    }

    /**
     * Get grouped elements by CLO.
     */
    public function getElementsByClo()
    {
        return $this->elements()->get()->groupBy('clo_code');
    }
}
