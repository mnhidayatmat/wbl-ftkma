<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class CheckActiveRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$allowedRoles): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $activeRole = Session::get('active_role');

        // If no active role is set, use the first role the user has
        if (! $activeRole) {
            $firstRole = $user->roles()->first();
            if ($firstRole) {
                Session::put('active_role', $firstRole->name);
                Session::put('active_role_expires_at', now()->addHours(24));
                $activeRole = $firstRole->name;
            } else {
                // Fallback to old role column for backward compatibility
                $activeRole = $user->role;
                if ($activeRole && $activeRole !== 'student') {
                    Session::put('active_role', $activeRole === 'industry' ? 'ic' : $activeRole);
                    Session::put('active_role_expires_at', now()->addHours(24));
                }
            }
        }

        // Check if session expired
        $expiresAt = Session::get('active_role_expires_at');
        if ($expiresAt && now()->greaterThan($expiresAt)) {
            Session::forget('active_role');
            Session::forget('active_role_expires_at');

            return redirect()->route('dashboard')->with('warning', 'Your role session has expired. Please select a role again.');
        }

        // If specific roles are required, check them
        if (! empty($allowedRoles)) {
            // Verify user has the active role
            if (! $user->hasRole($activeRole)) {
                Session::forget('active_role');

                return redirect()->route('dashboard')
                    ->withErrors(['role' => 'Invalid active role. Please select a valid role.']);
            }

            // Check if active role is in allowed roles
            if (! in_array($activeRole, $allowedRoles)) {
                return redirect()->route('dashboard')
                    ->withErrors(['role' => 'You do not have permission to access this page with your current role. Please switch roles.']);
            }
        }

        return $next($request);
    }
}
