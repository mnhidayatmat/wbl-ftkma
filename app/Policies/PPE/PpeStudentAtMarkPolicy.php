<?php

namespace App\Policies\PPE;

use App\Models\PPE\PpeStudentAtMark;
use App\Models\Student;
use App\Models\User;

class PpeStudentAtMarkPolicy
{
    /**
     * Determine whether the user can view any Lecturer marks.
     */
    public function viewAny(User $user): bool
    {
        // Admin, Lecturer, IC, and Student can view Lecturer marks (read-only for IC and Student)
        return $user->isAdmin() || $user->isLecturer() || $user->isIndustry() || $user->isStudent();
    }

    /**
     * Determine whether the user can view the Lecturer mark.
     */
    public function view(User $user, PpeStudentAtMark $ppeStudentAtMark): bool
    {
        $student = $ppeStudentAtMark->student;

        // Admin can view any
        if ($user->isAdmin()) {
            return true;
        }

        // Lecturer can view (group filtering handled in controller)
        if ($user->isLecturer()) {
            return true;
        }

        // IC can view marks for students assigned to them
        if ($user->isIndustry()) {
            return $student->ic_id === $user->id;
        }

        // Student can view their own marks
        if ($user->isStudent()) {
            return $student->user_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create Lecturer marks.
     */
    public function create(User $user, ?Student $student = null): bool
    {
        // Admin and Lecturer can create Lecturer marks
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isLecturer()) {
            return true; // Group filtering handled in controller
        }

        return false;
    }

    /**
     * Determine whether the user can update the Lecturer mark.
     */
    public function update(User $user, PpeStudentAtMark $ppeStudentAtMark): bool
    {
        $student = $ppeStudentAtMark->student;

        // Admin can update any
        if ($user->isAdmin()) {
            return true;
        }

        // Lecturer can update (group filtering handled in controller)
        if ($user->isLecturer()) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the Lecturer mark.
     */
    public function delete(User $user, PpeStudentAtMark $ppeStudentAtMark): bool
    {
        // Only Admin can delete
        return $user->isAdmin();
    }
}
