<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserVerificationController extends Controller
{
    /**
     * Display list of users with verification status.
     */
    public function index(Request $request): View
    {
        // Only admin can access
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $query = User::query();

        // Filter by verification status
        if ($request->has('status')) {
            if ($request->status === 'unverified') {
                $query->whereNull('email_verified_at');
            } elseif ($request->status === 'verified') {
                $query->whereNotNull('email_verified_at');
            }
        }

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->with('roles')->orderBy('name')->paginate(20)->withQueryString();

        // Count statistics
        $totalUsers = User::count();
        $verifiedUsers = User::whereNotNull('email_verified_at')->count();
        $unverifiedUsers = User::whereNull('email_verified_at')->count();
        
        // Get all available roles for the edit modal
        $allRoles = Role::orderBy('display_name')->get();

        return view('admin.users.verification', [
            'users' => $users,
            'totalUsers' => $totalUsers,
            'verifiedUsers' => $verifiedUsers,
            'unverifiedUsers' => $unverifiedUsers,
            'allRoles' => $allRoles,
        ]);
    }

    /**
     * Send verification email to a specific user.
     */
    public function sendVerificationEmail(User $user): RedirectResponse
    {
        // Only admin can access
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        // Check if already verified
        if ($user->hasVerifiedEmail()) {
            return back()->with('info', 'User email is already verified.');
        }

        // Send verification email
        $user->sendEmailVerificationNotification();

        return back()->with('success', "Verification email sent successfully to {$user->email}.");
    }

    /**
     * Send verification emails to all unverified admin users.
     */
    public function sendToAllUnverified(): RedirectResponse
    {
        // Only admin can access
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $unverifiedUsers = User::whereNull('email_verified_at')->get();

        if ($unverifiedUsers->isEmpty()) {
            return back()->with('info', 'All users are already verified.');
        }

        $count = 0;
        foreach ($unverifiedUsers as $user) {
            $user->sendEmailVerificationNotification();
            $count++;
        }

        return back()->with('success', "Verification emails sent to {$count} unverified user(s).");
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
}
