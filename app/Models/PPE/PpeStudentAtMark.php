<?php

namespace App\Models\PPE;

use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PpeStudentAtMark extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'assignment_id',
        'mark',
    ];

    protected $casts = [
        'mark' => 'decimal:2',
    ];

    /**
     * Get the student that owns the mark.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the assessment setting.
     */
    public function assessment(): BelongsTo
    {
        return $this->belongsTo(PpeAssessmentSetting::class, 'assignment_id');
    }
}
