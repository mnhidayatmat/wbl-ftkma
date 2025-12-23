<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentAssessmentComponentMark extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'assessment_id',
        'component_id',
        'rubric_score',
        'remarks',
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
     * Get the assessment.
     */
    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    /**
     * Get the component.
     */
    public function component(): BelongsTo
    {
        return $this->belongsTo(AssessmentComponent::class, 'component_id');
    }

    /**
     * Get the evaluator who entered the mark.
     */
    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluated_by');
    }

    /**
     * Calculate the weighted contribution for this component.
     */
    public function getWeightedContributionAttribute(): float
    {
        if (!$this->component || $this->rubric_score === null) {
            return 0;
        }
        
        // Normalize score (1-5) to percentage, then multiply by component weight
        $normalizedScore = ($this->rubric_score / 5) * 100;
        return ($normalizedScore / 100) * ($this->component->weight_percentage ?? 0);
    }
}
