<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;

class StudentPolicy
{
    /**
     * Determine whether the user can view any students.
     */
    public function viewAny(User $user): bool
    {
        // Admin and Lecturer can view all students
        // IC can view assigned students (filtered in controller)
        // Student can view their own (filtered in controller)
        return true;
    }

    /**
     * Determine whether the user can view the student.
     */
    public function view(User $user, Student $student): bool
    {
        // Admin can view any
        if ($user->isAdmin()) {
            return true;
        }

        // Lecturer can view students assigned to them
        if ($user->isLecturer()) {
            return $student->at_id === $user->id;
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
    }

    /**
     * Determine whether the user can create students.
     */
    public function create(User $user): bool
    {
        // Only Admin can create students directly
        // Students create their own profile via registration
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the student.
     */
    public function update(User $user, Student $student): bool
    {
        // Admin can update any student
        if ($user->isAdmin()) {
            return true;
        }

        // Student can only update their own profile
        if ($user->isStudent()) {
            return $student->user_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the student.
     */
    public function delete(User $user, Student $student): bool
    {
        // Only Admin can delete students
        return $user->isAdmin();
    }
}
