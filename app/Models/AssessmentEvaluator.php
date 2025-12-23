<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentEvaluator extends Model
{
    use HasFactory;

    protected $fillable = [
        'assessment_id',
        'evaluator_role',
        'total_score',
        'order',
    ];

    protected $casts = [
        'total_score' => 'decimal:2',
        'order' => 'integer',
    ];

    /**
     * Get the assessment.
     */
    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    /**
     * Scope to order evaluators.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('evaluator_role');
    }
}
