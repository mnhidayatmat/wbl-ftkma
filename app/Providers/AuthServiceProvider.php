<?php

namespace App\Providers;

use App\Models\PPE\PpeAssessmentSetting;
use App\Models\PPE\PpeStudentAtMark;
use App\Models\PPE\PpeStudentIcMark;
use App\Models\Student;
use App\Policies\PPE\PpeAssessmentSettingPolicy;
use App\Policies\PPE\PpeStudentAtMarkPolicy;
use App\Policies\PPE\PpeStudentIcMarkPolicy;
use App\Policies\StudentPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        PpeStudentAtMark::class => PpeStudentAtMarkPolicy::class,
        PpeStudentIcMark::class => PpeStudentIcMarkPolicy::class,
        PpeAssessmentSetting::class => PpeAssessmentSettingPolicy::class,
        Student::class => StudentPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Register Gates
        Gate::define('edit-at-marks', function ($user, $student = null) {
            // Admin and FYP Coordinator can always edit any AT marks
            if ($user->role === 'admin' || $user->isFypCoordinator()) {
                return true;
            }

            // Lecturer can edit AT marks ONLY if student is assigned to them
            if ($user->role === 'lecturer' && $student) {
                // Student must be assigned to this lecturer
                return $student->at_id == $user->id;
            }

            // If no student provided, allow lecturer (controller will filter by at_id)
            if ($user->role === 'lecturer' && $student === null) {
                return true;
            }

            // All other cases: deny access
            return false;
        });

        Gate::define('edit-ic-marks', function ($user, $student = null) {
            // Admin and FYP Coordinator can edit any IC marks
            if ($user->isAdmin() || $user->isFypCoordinator()) {
                return true;
            }
            // IC can only edit marks for students assigned to them
            if ($user->isIndustry() && $student) {
                return $student->ic_id === $user->id;
            }

            return false;
        });

        Gate::define('edit-supervisor-li-marks', function ($user, $student) {
            if ($user->isAdmin()) {
                return true;
            }
            if ($user->isSupervisorLi()) {
                return \App\Models\StudentCourseAssignment::where('student_id', $student->id)
                    ->where('lecturer_id', $user->id)
                    ->where('course_type', 'Industrial Training')
                    ->exists();
            }

            return false;
        });

        Gate::define('edit-li-ic-marks', function ($user, $student) {
            if ($user->isAdmin()) {
                return true;
            }
            if ($user->isIndustry()) {
                return $student->ic_id === $user->id;
            }

            return false;
        });

        Gate::define('view-student', function ($user, $student) {
            // Admin can view any student
            if ($user->isAdmin()) {
                return true;
            }
            // Lecturer can view students in their assigned groups
            if ($user->isLecturer()) {
                return true; // Group filtering handled in controller
            }
            // IC can only view students assigned to them
            if ($user->isIndustry()) {
                return $student->ic_id === $user->id;
            }
            // Student can only view their own profile
            if ($user->isStudent()) {
                return $student->user_id === $user->id;
            }

            return false;
        });

        Gate::define('manage-settings', function ($user) {
            // Only Admin can manage PPE settings
            return $user->isAdmin();
        });

        Gate::define('edit-student-profile', function ($user, $student) {
            // Admin can edit any student profile
            if ($user->isAdmin()) {
                return true;
            }
            // Student can only edit their own profile
            if ($user->isStudent()) {
                return $student->user_id === $user->id;
            }

            return false;
        });
    }
}
