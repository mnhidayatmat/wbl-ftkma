<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentCourseAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'course_type',
        'lecturer_id',
    ];

    /**
     * Get the student assigned to this course.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the lecturer assigned to this student-course.
     */
    public function lecturer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'lecturer_id');
    }

    /**
     * Get course type display name.
     */
    public function getCourseTypeDisplayAttribute(): string
    {
        return match ($this->course_type) {
            'FYP' => 'Final Year Project',
            'IP' => 'Internship Preparation',
            'OSH' => 'Occupational Safety & Health',
            'PPE' => 'Professional Practice & Ethics',
            'Industrial Training' => 'Industrial Training',
            'IC' => 'Industry Coach',
            default => $this->course_type,
        };
    }
}
