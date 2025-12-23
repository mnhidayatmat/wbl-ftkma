<?php

namespace App\Models\PPE;

use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PpeStudentIcMark extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'clo',
        'question_no',
        'rubric_value',
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
}
