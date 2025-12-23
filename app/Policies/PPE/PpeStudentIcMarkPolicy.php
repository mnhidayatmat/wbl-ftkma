<?php

namespace App\Policies\PPE;

use App\Models\PPE\PpeStudentIcMark;
use App\Models\Student;
use App\Models\User;

class PpeStudentIcMarkPolicy
{
    /**
     * Determine whether the user can view any IC marks.
     */
    public function viewAny(User $user): bool
    {
        // All roles can view IC marks (read-only for Lecturer and Student)
        return $user->isAdmin() || $user->isLecturer() || $user->isIndustry() || $user->isStudent();
    }

    /**
     * Determine whether the user can view the IC mark.
     */
    public function view(User $user, PpeStudentIcMark $ppeStudentIcMark): bool
    {
        $student = $ppeStudentIcMark->student;

        // Admin can view any
        if ($user->isAdmin()) {
            return true;
        }

        // Lecturer can view (read-only)
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
     * Determine whether the user can create IC marks.
     */
    public function create(User $user, ?Student $student = null): bool
    {
        // Admin can create any
        if ($user->isAdmin()) {
            return true;
        }

        // IC can create marks for students assigned to them
        if ($user->isIndustry() && $student) {
            return $student->ic_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can update the IC mark.
     */
    public function update(User $user, PpeStudentIcMark $ppeStudentIcMark): bool
    {
        $student = $ppeStudentIcMark->student;

        // Admin can update any
        if ($user->isAdmin()) {
            return true;
        }

        // IC can only update marks for students assigned to them
        if ($user->isIndustry()) {
            return $student->ic_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the IC mark.
     */
    public function delete(User $user, PpeStudentIcMark $ppeStudentIcMark): bool
    {
        // Only Admin can delete
        return $user->isAdmin();
    }
}
