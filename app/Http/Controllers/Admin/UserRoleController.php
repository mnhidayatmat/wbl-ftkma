<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserRoleController extends Controller
{
    /**
     * Display list of users with their roles.
     */
    public function index(Request $request): View
    {
        // Only admin can access
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $query = User::query();

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->with(['roles', 'student', 'assignedStudents', 'assignedStudentsAsAt'])->orderBy('name')->paginate(20)->withQueryString();

        // Count statistics
        $totalUsers = User::count();
        
        // Get all available roles for the edit modal, sorted by user count (least users first)
        $allRoles = Role::withCount('users')
            ->orderBy('users_count', 'asc') // Roles with fewer users first
            ->orderBy('display_name', 'asc') // Then alphabetically for ties
            ->get();

        return view('admin.users.roles', [
            'users' => $users,
            'totalUsers' => $totalUsers,
            'allRoles' => $allRoles,
        ]);
    }

    /**
     * Update user roles.
     */
    public function updateRoles(Request $request, User $user): RedirectResponse
    {
        // Only admin can access
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
        ]);

        // Sync user roles
        $user->roles()->sync($request->input('roles'));

        return back()->with('success', "Roles updated successfully for {$user->name}.");
    }

    /**
     * Delete a user account.
     */
    public function destroy(User $user): RedirectResponse
    {
        // Only admin can access
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        // Prevent admin from deleting themselves
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        // Prevent deleting the last admin user
        $adminCount = User::whereHas('roles', function($query) {
            $query->where('name', 'admin');
        })->count();

        if ($user->hasRole('admin') && $adminCount <= 1) {
            return back()->with('error', 'Cannot delete the last admin user. At least one admin account must exist.');
        }

        $userName = $user->name;
        
        // Check for important relationships before deletion
        $hasStudentProfile = $user->student()->exists();
        $assignedAsIc = $user->assignedStudents()->exists();
        $assignedAsAt = $user->assignedStudentsAsAt()->exists();
        
        // Delete user (cascade will handle related records if foreign keys are set up)
        // - Student profile will be deleted (cascade)
        // - IC/AT assignments will be set to null (set null)
        // - User roles will be deleted (cascade)
        $user->delete();

        $message = "User account '{$userName}' has been deleted successfully.";
        
        // Add information about what was affected
        $affectedItems = [];
        if ($hasStudentProfile) {
            $affectedItems[] = 'student profile';
        }
        if ($assignedAsIc) {
            $affectedItems[] = 'Industry Coach assignments';
        }
        if ($assignedAsAt) {
            $affectedItems[] = 'Academic Tutor assignments';
        }
        
        if (!empty($affectedItems)) {
            $message .= ' Related ' . implode(', ', $affectedItems) . ' have been removed or updated.';
        }

        return back()->with('success', $message);
    }
}
