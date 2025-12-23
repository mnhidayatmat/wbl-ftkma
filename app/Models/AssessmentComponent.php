<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentComponent extends Model
{
    use HasFactory;

    protected $fillable = [
        'assessment_id',
        'component_name',
        'criteria_keywords',
        'clo_code',
        'weight_percentage',
        'min_score',
        'max_score',
        'example_answer',
        'order',
        'rubric_scale_min',
        'rubric_scale_max',
        'duration_label',
    ];

    protected $casts = [
        'weight_percentage' => 'decimal:2',
        'min_score' => 'decimal:2',
        'max_score' => 'decimal:2',
        'order' => 'integer',
        'rubric_scale_min' => 'integer',
        'rubric_scale_max' => 'integer',
    ];

    /**
     * Get the assessment that owns this component.
     */
    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    /**
     * Get all student marks for this component.
     */
    public function studentMarks()
    {
        return $this->hasMany(StudentAssessmentComponentMark::class, 'assessment_component_id');
    }

    /**
     * Scope to order components by their order field.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}
