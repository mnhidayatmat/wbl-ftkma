<?php

namespace App\Models\FYP;

use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FypRubricOverallFeedback extends Model
{
    use HasFactory;

    protected $table = 'fyp_rubric_overall_feedback';

    protected $fillable = [
        'student_id',
        'rubric_template_id',
        'overall_feedback',
        'strengths',
        'areas_for_improvement',
        'total_score',
        'percentage_score',
        'status',
        'evaluated_by',
        'submitted_at',
        'released_at',
    ];

    protected $casts = [
        'total_score' => 'decimal:2',
        'percentage_score' => 'decimal:2',
        'submitted_at' => 'datetime',
        'released_at' => 'datetime',
    ];

    /**
     * Status constants
     */
    public const STATUS_DRAFT = 'draft';

    public const STATUS_SUBMITTED = 'submitted';

    public const STATUS_RELEASED = 'released';

    /**
     * Get the student.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the rubric template.
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(FypRubricTemplate::class, 'rubric_template_id');
    }

    /**
     * Get the evaluator.
     */
    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluated_by');
    }

    /**
     * Scope to get feedback for a specific student.
     */
    public function scopeForStudent($query, int $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    /**
     * Scope to get released feedback.
     */
    public function scopeReleased($query)
    {
        return $query->where('status', self::STATUS_RELEASED);
    }

    /**
     * Calculate total and percentage scores from evaluations.
     */
    public function calculateScores(): void
    {
        $evaluations = FypRubricEvaluation::where('student_id', $this->student_id)
            ->where('rubric_template_id', $this->rubric_template_id)
            ->whereNotNull('weighted_score')
            ->get();

        $this->total_score = $evaluations->sum('weighted_score');

        // Get max possible score (100% would mean all elements at level 5)
        $template = $this->template;
        if ($template) {
            $maxPossible = $template->elements()->sum('weight_percentage');
            $this->percentage_score = $maxPossible > 0
                ? ($this->total_score / $maxPossible) * 100
                : 0;
        }
    }

    /**
     * Submit the evaluation.
     */
    public function submit(): void
    {
        $this->calculateScores();
        $this->status = self::STATUS_SUBMITTED;
        $this->submitted_at = now();
        $this->save();
    }

    /**
     * Release the evaluation to student.
     */
    public function release(): void
    {
        $this->status = self::STATUS_RELEASED;
        $this->released_at = now();
        $this->save();

        // Lock the template if not already locked
        if ($this->template && ! $this->template->is_locked) {
            $this->template->update(['is_locked' => true]);
        }
    }

    /**
     * Check if evaluation is complete (all elements assessed).
     */
    public function isComplete(): bool
    {
        if (! $this->template) {
            return false;
        }

        $totalElements = $this->template->elements()->active()->count();
        $evaluatedElements = FypRubricEvaluation::where('student_id', $this->student_id)
            ->where('rubric_template_id', $this->rubric_template_id)
            ->whereNotNull('selected_level')
            ->count();

        return $evaluatedElements >= $totalElements;
    }

    /**
     * Get completion percentage.
     */
    public function getCompletionPercentageAttribute(): float
    {
        if (! $this->template) {
            return 0;
        }

        $totalElements = $this->template->elements()->active()->count();
        if ($totalElements === 0) {
            return 100;
        }

        $evaluatedElements = FypRubricEvaluation::where('student_id', $this->student_id)
            ->where('rubric_template_id', $this->rubric_template_id)
            ->whereNotNull('selected_level')
            ->count();

        return ($evaluatedElements / $totalElements) * 100;
    }
}
