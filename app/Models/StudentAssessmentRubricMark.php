<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentAssessmentRubricMark extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'assessment_rubric_id',
        'rubric_score',
        'evaluated_by',
    ];

    protected $casts = [
        'rubric_score' => 'integer',
    ];

    /**
     * Get the student.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the assessment rubric.
     */
    public function rubric(): BelongsTo
    {
        return $this->belongsTo(AssessmentRubric::class, 'assessment_rubric_id');
    }

    /**
     * Get the evaluator who entered the mark.
     */
    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluated_by');
    }

    /**
     * Calculate the weighted contribution.
     */
    public function getWeightedContributionAttribute(): float
    {
        if (! $this->rubric) {
            return 0;
        }

        $rubricRange = $this->rubric->rubric_max - $this->rubric->rubric_min;
        if ($rubricRange == 0) {
            return 0;
        }

        // Normalize score to 0-1 range, then multiply by weight
        $normalizedScore = ($this->rubric_score - $this->rubric->rubric_min) / $rubricRange;

        return $normalizedScore * $this->rubric->weight_percentage;
    }
}
