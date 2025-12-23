<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class RoleSwitchController extends Controller
{
    /**
     * Switch the active role for the current user.
     */
    public function switch(Request $request)
    {
        $request->validate([
            'role' => 'required|string',
        ]);

        $user = Auth::user();
        $requestedRole = $request->input('role');

        // Verify user has this role
        if (!$user->hasRole($requestedRole)) {
            return redirect()->back()->withErrors([
                'role' => 'You do not have permission to switch to this role.'
            ]);
        }

        // For student role, verify user has a student profile
        if ($requestedRole === 'student' && !$user->student) {
            return redirect()->back()->withErrors([
                'role' => 'You need to have a student profile to switch to student role. Please contact the administrator if you believe this is an error.'
            ]);
        }

        // Set active role in session
        Session::put('active_role', $requestedRole);

        // Set session expiry (24 hours)
        Session::put('active_role_expires_at', now()->addHours(24));

        return redirect()->back()->with('success', 'Role switched successfully.');
    }

    /**
     * Get available roles for the current user.
     */
    public function getAvailableRoles()
    {
        $user = Auth::user();
        return response()->json([
            'roles' => $user->roles()->get(['id', 'name', 'display_name']),
            'active_role' => $user->getActiveRole(),
        ]);
    }
}
