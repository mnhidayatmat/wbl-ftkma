<?php

namespace App\Models\FYP;

use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FypRubricEvaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'rubric_template_id',
        'rubric_element_id',
        'selected_level_id',
        'selected_level',
        'score',
        'weighted_score',
        'remarks',
        'evaluated_by',
        'evaluated_at',
    ];

    protected $casts = [
        'selected_level' => 'integer',
        'score' => 'decimal:2',
        'weighted_score' => 'decimal:2',
        'evaluated_at' => 'datetime',
    ];

    /**
     * Get the student being evaluated.
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
     * Get the rubric element.
     */
    public function element(): BelongsTo
    {
        return $this->belongsTo(FypRubricElement::class, 'rubric_element_id');
    }

    /**
     * Get the selected level descriptor.
     */
    public function selectedDescriptor(): BelongsTo
    {
        return $this->belongsTo(FypRubricLevelDescriptor::class, 'selected_level_id');
    }

    /**
     * Get the evaluator.
     */
    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluated_by');
    }

    /**
     * Scope to get evaluations for a specific student.
     */
    public function scopeForStudent($query, int $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    /**
     * Scope to get evaluations for a specific template.
     */
    public function scopeForTemplate($query, int $templateId)
    {
        return $query->where('rubric_template_id', $templateId);
    }

    /**
     * Calculate and set weighted score based on selected level.
     */
    public function calculateWeightedScore(): void
    {
        if (! $this->element || ! $this->selected_level) {
            $this->weighted_score = 0;

            return;
        }

        $descriptor = $this->element->getDescriptorForLevel($this->selected_level);
        if (! $descriptor) {
            $this->weighted_score = 0;

            return;
        }

        $this->score = $descriptor->score_value;

        // Weighted score = (score / max_score) * element_weight
        $maxScore = 5; // Fixed max level
        $this->weighted_score = ($this->score / $maxScore) * $this->element->weight_percentage;
    }

    /**
     * Boot method to auto-calculate weighted score.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($evaluation) {
            $evaluation->calculateWeightedScore();

            if ($evaluation->selected_level && ! $evaluation->evaluated_at) {
                $evaluation->evaluated_at = now();
            }
        });
    }

    /**
     * Get the level label for display.
     */
    public function getLevelLabelAttribute(): ?string
    {
        if (! $this->selected_level) {
            return null;
        }

        return FypRubricTemplate::PERFORMANCE_LEVELS[$this->selected_level] ?? null;
    }
}
