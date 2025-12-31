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
        'ic_number',
        'parent_name',
        'parent_phone_number',
        'next_of_kin',
        'next_of_kin_phone_number',
        'home_address',
        'resume_pdf_path',
        'academic_advisor_id',
        'skills',
        'interests',
        'preferred_industry',
        'preferred_location',
    ];

    protected $casts = [
        'cgpa' => 'decimal:2',
        'skills' => 'array',
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

    /**
     * Get skills as comma-separated string.
     */
    public function getSkillsStringAttribute(): string
    {
        return $this->skills ? implode(', ', $this->skills) : '';
    }

    /**
     * Check if student has a specific skill.
     */
    public function hasSkill(string $skill): bool
    {
        if (! $this->skills) {
            return false;
        }

        return in_array(strtolower($skill), array_map('strtolower', $this->skills));
    }

    /**
     * Scope to filter students by skills (match any).
     */
    public function scopeWithSkills($query, array $skills)
    {
        if (empty($skills)) {
            return $query;
        }

        return $query->where(function ($q) use ($skills) {
            foreach ($skills as $skill) {
                $q->orWhereJsonContains('skills', $skill);
            }
        });
    }

    /**
     * Scope to filter students by CGPA range.
     */
    public function scopeWithCgpaRange($query, ?float $min = null, ?float $max = null)
    {
        if ($min !== null) {
            $query->where('cgpa', '>=', $min);
        }

        if ($max !== null) {
            $query->where('cgpa', '<=', $max);
        }

        return $query;
    }

    /**
     * Scope to filter students ready for recruitment.
     */
    public function scopeRecruitmentReady($query)
    {
        return $query->whereHas('resumeInspection', function ($q) {
            $q->where('status', 'RECOMMENDED');
        })->whereNotNull('resume_pdf_path');
    }

    /**
     * Programme name to short code mapping.
     */
    public const PROGRAMME_SHORT_CODES = [
        'Bachelor of Mechanical Engineering Technology (Automotive) with Honours' => 'BTA',
        'Bachelor of Mechanical Engineering Technology (Design and Analysis) with Honours' => 'BTD',
        'Bachelor of Mechanical Engineering Technology (Oil and Gas) with Honours' => 'BTG',
    ];

    /**
     * Programme short code to WBL Coordinator role mapping.
     */
    public const PROGRAMME_WBL_COORDINATOR_ROLES = [
        'BTA' => 'bta_wbl_coordinator',
        'BTD' => 'btd_wbl_coordinator',
        'BTG' => 'btg_wbl_coordinator',
    ];

    /**
     * Get the short code for a programme name.
     */
    public static function getProgrammeShortCode(?string $programme): string
    {
        if (! $programme) {
            return 'N/A';
        }

        return self::PROGRAMME_SHORT_CODES[$programme] ?? $programme;
    }

    /**
     * Get the short programme code attribute.
     */
    public function getProgrammeShortAttribute(): string
    {
        return self::getProgrammeShortCode($this->programme);
    }

    /**
     * Get the WBL Coordinator role name for a programme.
     */
    public static function getWblCoordinatorRole(?string $programme): ?string
    {
        if (! $programme) {
            return null;
        }

        $shortCode = self::getProgrammeShortCode($programme);

        return self::PROGRAMME_WBL_COORDINATOR_ROLES[$shortCode] ?? null;
    }

    /**
     * Get the WBL Coordinator user for a student's programme.
     */
    public static function getWblCoordinator(?string $programme): ?User
    {
        $roleName = self::getWblCoordinatorRole($programme);

        if (! $roleName) {
            return null;
        }

        return User::whereHas('roles', function ($query) use ($roleName) {
            $query->where('name', $roleName);
        })->first();
    }

    /**
     * Get this student's WBL Coordinator.
     */
    public function getWblCoordinatorAttribute(): ?User
    {
        return self::getWblCoordinator($this->programme);
    }
}
