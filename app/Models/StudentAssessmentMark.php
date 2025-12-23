<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentAssessmentMark extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'assessment_id',
        'mark',
        'max_mark',
        'remarks',
        'evaluated_by',
    ];

    protected $casts = [
        'mark' => 'decimal:2',
        'max_mark' => 'decimal:2',
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
        if ($this->mark === null || $this->max_mark == 0) {
            return 0;
        }
        
        return ($this->mark / $this->max_mark) * $this->assessment->weight_percentage;
    }
}
