<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentRubric extends Model
{
    use HasFactory;

    protected $fillable = [
        'assessment_id',
        'question_code',
        'question_title',
        'question_description',
        'clo_code',
        'weight_percentage',
        'rubric_min',
        'rubric_max',
        'example_answer',
        'order',
    ];

    protected $casts = [
        'weight_percentage' => 'decimal:2',
        'rubric_min' => 'integer',
        'rubric_max' => 'integer',
        'order' => 'integer',
    ];

    /**
     * Get the assessment that owns this rubric.
     */
    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    /**
     * Scope to order by order field.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('question_code');
    }
}
