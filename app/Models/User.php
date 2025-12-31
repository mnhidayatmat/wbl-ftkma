<?php

namespace App\Models;

use App\Notifications\ResetPassword;
use App\Notifications\VerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmail);
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role', // Keep for backward compatibility
        'company_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get all roles assigned to this user.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    /**
     * Check if user has a specific role.
     */
    public function hasRole(string $roleName): bool
    {
        return $this->roles()->where('name', $roleName)->exists();
    }

    /**
     * Check if user has any of the given roles.
     */
    public function hasAnyRole(array $roleNames): bool
    {
        return $this->roles()->whereIn('name', $roleNames)->exists();
    }

    /**
     * Get all role names for this user.
     */
    public function getRoleNames(): array
    {
        return $this->roles()->pluck('name')->toArray();
    }

    /**
     * Get active role from session (for role switching).
     */
    public function getActiveRole(): ?string
    {
        return session('active_role');
    }

    /**
     * Check if user is currently acting as a specific role.
     */
    public function isActingAs(string $roleName): bool
    {
        $activeRole = $this->getActiveRole();

        return $activeRole === $roleName && $this->hasRole($roleName);
    }

    /**
     * Check if user is admin (backward compatibility + active role check)
     */
    public function isAdmin(): bool
    {
        $activeRole = $this->getActiveRole();
        if ($activeRole) {
            return $activeRole === 'admin' && $this->hasRole('admin');
        }

        // Fallback to old role column for backward compatibility
        return $this->role === 'admin' || $this->hasRole('admin');
    }

    /**
     * Check if user is lecturer (backward compatibility + active role check)
     */
    public function isLecturer(): bool
    {
        $activeRole = $this->getActiveRole();
        if ($activeRole) {
            return $activeRole === 'lecturer' && $this->hasRole('lecturer');
        }

        return $this->role === 'lecturer' || $this->hasRole('lecturer');
    }

    /**
     * Check if user is industry/IC (backward compatibility + active role check)
     */
    public function isIndustry(): bool
    {
        $activeRole = $this->getActiveRole();
        if ($activeRole) {
            return $activeRole === 'ic' && $this->hasRole('ic');
        }

        return $this->role === 'industry' || $this->hasRole('ic');
    }

    /**
     * Check if user is student (backward compatibility)
     */
    public function isStudent(): bool
    {
        $activeRole = $this->getActiveRole();
        if ($activeRole) {
            return $activeRole === 'student' && $this->hasRole('student');
        }

        return $this->role === 'student' || $this->hasRole('student');
    }

    /**
     * Check if user is supervisor LI (backward compatibility + active role check)
     */
    public function isSupervisorLi(): bool
    {
        $activeRole = $this->getActiveRole();
        if ($activeRole) {
            return $activeRole === 'supervisor_li' && $this->hasRole('supervisor_li');
        }

        return $this->role === 'supervisor_li' || $this->hasRole('supervisor_li');
    }

    /**
     * Check if user is AT (backward compatibility + active role check)
     */
    public function isAt(): bool
    {
        $activeRole = $this->getActiveRole();
        if ($activeRole) {
            return $activeRole === 'at' && $this->hasRole('at');
        }

        return $this->role === 'at' || $this->hasRole('at');
    }

    /**
     * Check if user is IC (backward compatibility + active role check)
     */
    public function isIc(): bool
    {
        $activeRole = $this->getActiveRole();
        if ($activeRole) {
            return $activeRole === 'ic' && $this->hasRole('ic');
        }

        return $this->role === 'industry' || $this->hasRole('ic');
    }

    /**
     * Check if user is coordinator (new role)
     */
    public function isCoordinator(): bool
    {
        $activeRole = $this->getActiveRole();
        if ($activeRole) {
            return $activeRole === 'coordinator' && $this->hasRole('coordinator');
        }

        return $this->hasRole('coordinator');
    }

    /**
     * Check if user is FYP Coordinator
     */
    public function isFypCoordinator(): bool
    {
        $activeRole = $this->getActiveRole();
        if ($activeRole) {
            return $activeRole === 'fyp_coordinator' && $this->hasRole('fyp_coordinator');
        }

        return $this->hasRole('fyp_coordinator');
    }

    /**
     * Check if user is IP Coordinator
     */
    public function isIpCoordinator(): bool
    {
        $activeRole = $this->getActiveRole();
        if ($activeRole) {
            return $activeRole === 'ip_coordinator' && $this->hasRole('ip_coordinator');
        }

        return $this->hasRole('ip_coordinator');
    }

    /**
     * Check if user is OSH Coordinator
     */
    public function isOshCoordinator(): bool
    {
        $activeRole = $this->getActiveRole();
        if ($activeRole) {
            return $activeRole === 'osh_coordinator' && $this->hasRole('osh_coordinator');
        }

        return $this->hasRole('osh_coordinator');
    }

    /**
     * Check if user is PPE Coordinator
     */
    public function isPpeCoordinator(): bool
    {
        $activeRole = $this->getActiveRole();
        if ($activeRole) {
            return $activeRole === 'ppe_coordinator' && $this->hasRole('ppe_coordinator');
        }

        return $this->hasRole('ppe_coordinator');
    }

    /**
     * Check if user is LI Coordinator (Industrial Training)
     */
    public function isLiCoordinator(): bool
    {
        $activeRole = $this->getActiveRole();
        if ($activeRole) {
            return $activeRole === 'li_coordinator' && $this->hasRole('li_coordinator');
        }

        return $this->hasRole('li_coordinator');
    }

    /**
     * Check if user is BTA WBL Coordinator
     */
    public function isBtaWblCoordinator(): bool
    {
        $activeRole = $this->getActiveRole();
        if ($activeRole) {
            return $activeRole === 'bta_wbl_coordinator' && $this->hasRole('bta_wbl_coordinator');
        }

        return $this->hasRole('bta_wbl_coordinator');
    }

    /**
     * Check if user is BTD WBL Coordinator
     */
    public function isBtdWblCoordinator(): bool
    {
        $activeRole = $this->getActiveRole();
        if ($activeRole) {
            return $activeRole === 'btd_wbl_coordinator' && $this->hasRole('btd_wbl_coordinator');
        }

        return $this->hasRole('btd_wbl_coordinator');
    }

    /**
     * Check if user is BTG WBL Coordinator
     */
    public function isBtgWblCoordinator(): bool
    {
        $activeRole = $this->getActiveRole();
        if ($activeRole) {
            return $activeRole === 'btg_wbl_coordinator' && $this->hasRole('btg_wbl_coordinator');
        }

        return $this->hasRole('btg_wbl_coordinator');
    }

    /**
     * Check if user is any WBL Coordinator (BTA, BTD, BTG)
     */
    public function isWblCoordinator(): bool
    {
        return $this->isBtaWblCoordinator() || $this->isBtdWblCoordinator() || $this->isBtgWblCoordinator();
    }

    /**
     * Check if user is any module coordinator
     */
    public function isModuleCoordinator(): bool
    {
        return $this->isFypCoordinator() || $this->isIpCoordinator() ||
               $this->isOshCoordinator() || $this->isPpeCoordinator() ||
               $this->isLiCoordinator() || $this->isWblCoordinator();
    }

    /**
     * Helper method to check role (for backward compatibility)
     */
    public function isRole(string $role): bool
    {
        $activeRole = $this->getActiveRole();
        if ($activeRole) {
            return $activeRole === $role && $this->hasRole($role);
        }

        return $this->role === $role || $this->hasRole($role);
    }

    /**
     * Get the student profile for this user.
     */
    public function student()
    {
        return $this->hasOne(Student::class);
    }

    /**
     * Get students assigned to this industry coach.
     */
    public function assignedStudents()
    {
        return $this->hasMany(Student::class, 'ic_id');
    }

    /**
     * Get students assigned to this academic tutor.
     */
    public function assignedStudentsAsAt()
    {
        return $this->hasMany(Student::class, 'at_id');
    }

    /**
     * Get course assignments for this lecturer.
     */
    public function courseAssignments()
    {
        return $this->hasMany(LecturerCourseAssignment::class, 'lecturer_id');
    }

    /**
     * Check if lecturer is assigned to a specific course.
     */
    public function isAssignedToCourse(string $courseType): bool
    {
        return $this->courseAssignments()
            ->where('course_type', $courseType)
            ->exists();
    }

    /**
     * Get the company this user belongs to (for IC users).
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    /**
     * Check if user has permission for a module and action.
     */
    public function hasPermission(string $moduleName, string $action, string $accessLevel = 'view'): bool
    {
        // Admin always has full access
        if ($this->isAdmin()) {
            return true;
        }

        return \App\Helpers\PermissionHelper::canAccess($moduleName, $action, $accessLevel);
    }

    /**
     * Check if user can perform a specific action on a module.
     * Note: This is a custom method, not Laravel's authorization can() method.
     */
    public function canPerform(string $moduleName, string $action): bool
    {
        return \App\Helpers\PermissionHelper::can($moduleName, $action);
    }
}
