<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IpLogbookEvaluation extends Model
{
    use HasFactory;

    protected $table = 'ip_logbook_evaluations';

    protected $fillable = [
        'student_id',
        'month',
        'score',
        'remarks',
        'evaluated_by',
    ];

    protected $casts = [
        'month' => 'integer',
        'score' => 'integer',
    ];

    /**
     * Get the student for this evaluation.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the evaluator (IC) who evaluated this logbook.
     */
    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluated_by');
    }

    /**
     * Get the score label (POOR to EXCELLENT).
     */
    public function getScoreLabelAttribute(): string
    {
        if ($this->score === null) {
            return 'Not Evaluated';
        }

        return match (true) {
            $this->score <= 2 => 'Poor',
            $this->score <= 4 => 'Below Average',
            $this->score <= 6 => 'Average',
            $this->score <= 8 => 'Good',
            default => 'Excellent',
        };
    }

    /**
     * Scope to get evaluations for a specific student.
     */
    public function scopeForStudent($query, int $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    /**
     * Calculate total score for a student (sum of all periods).
     */
    public static function getTotalScore(int $studentId): int
    {
        return static::forStudent($studentId)->sum('score') ?? 0;
    }

    /**
     * Calculate average score for a student.
     */
    public static function getAverageScore(int $studentId): float
    {
        $evaluations = static::forStudent($studentId)->whereNotNull('score')->get();

        if ($evaluations->isEmpty()) {
            return 0;
        }

        return $evaluations->avg('score');
    }
}
