<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'matric_no',
        'programme',
        'cgpa',
        'group_id',
        'company_id',
        'user_id',
        'image_path',
        'at_id',
        'ic_id',
        'background',
        'mobile_phone',
        'resume_pdf_path',
        'academic_advisor_id',
    ];

    protected $casts = [
        'cgpa' => 'decimal:2',
    ];

    /**
     * Get the group that owns the student.
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(WblGroup::class, 'group_id');
    }

    /**
     * Get the company that owns the student.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    /**
     * Get the placement tracking for the student.
     */
    public function placementTracking()
    {
        return $this->hasOne(StudentPlacementTracking::class);
    }

    /**
     * Get the resume inspection for the student.
     */
    public function resumeInspection()
    {
        return $this->hasOne(StudentResumeInspection::class);
    }

    /**
     * Get the Lecturer marks for the student (PPE).
     */
    public function ppeAtMarks(): HasMany
    {
        return $this->hasMany(\App\Models\PPE\PpeStudentAtMark::class);
    }

    /**
     * Get the IC marks for the student (PPE).
     */
    public function ppeIcMarks(): HasMany
    {
        return $this->hasMany(\App\Models\PPE\PpeStudentIcMark::class);
    }

    /**
     * Get the user that owns the student profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the academic tutor (AT) assigned to the student.
     */
    public function academicTutor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'at_id');
    }

    /**
     * Get the industry coach assigned to the student.
     */
    public function industryCoach(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ic_id');
    }

    /**
     * Get the academic advisor assigned to the student.
     */
    public function academicAdvisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'academic_advisor_id');
    }

    /**
     * Get course assignments for this student.
     */
    public function courseAssignments(): HasMany
    {
        return $this->hasMany(StudentCourseAssignment::class);
    }

    /**
     * Get course assignment for a specific course type.
     */
    public function getCourseAssignment(string $courseType): ?StudentCourseAssignment
    {
        return $this->courseAssignments()
            ->where('course_type', $courseType)
            ->first();
    }

    /**
     * Check if student is in an active group.
     */
    public function isInActiveGroup(): bool
    {
        return $this->group && $this->group->isActive();
    }

    /**
     * Check if student is in a completed group.
     */
    public function isInCompletedGroup(): bool
    {
        return $this->group && $this->group->isCompleted();
    }

    /**
     * Scope to get only students in active groups.
     */
    public function scopeInActiveGroups($query)
    {
        return $query->whereHas('group', function ($q) {
            $q->where('status', 'ACTIVE');
        });
    }

    /**
     * Scope to get only students in completed groups.
     */
    public function scopeInCompletedGroups($query)
    {
        return $query->whereHas('group', function ($q) {
            $q->where('status', 'COMPLETED');
        });
    }
}
