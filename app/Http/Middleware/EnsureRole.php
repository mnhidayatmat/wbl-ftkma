<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Check if user has any of the required roles
        $hasRequiredRole = false;
        foreach ($roles as $role) {
            if ($user->hasRole($role)) {
                $hasRequiredRole = true;
                break;
            }

            // Also check coordinator-specific methods
            if ($role === 'ip_coordinator' && $user->isIpCoordinator()) {
                $hasRequiredRole = true;
                break;
            }
            if ($role === 'fyp_coordinator' && $user->isFypCoordinator()) {
                $hasRequiredRole = true;
                break;
            }
            if ($role === 'osh_coordinator' && $user->isOshCoordinator()) {
                $hasRequiredRole = true;
                break;
            }
            if ($role === 'ppe_coordinator' && $user->isPpeCoordinator()) {
                $hasRequiredRole = true;
                break;
            }
            if ($role === 'li_coordinator' && $user->isLiCoordinator()) {
                $hasRequiredRole = true;
                break;
            }
            // WBL Coordinators
            if ($role === 'bta_wbl_coordinator' && $user->isBtaWblCoordinator()) {
                $hasRequiredRole = true;
                break;
            }
            if ($role === 'btd_wbl_coordinator' && $user->isBtdWblCoordinator()) {
                $hasRequiredRole = true;
                break;
            }
            if ($role === 'btg_wbl_coordinator' && $user->isBtgWblCoordinator()) {
                $hasRequiredRole = true;
                break;
            }
        }

        // If user has any required role, allow access
        if ($hasRequiredRole) {
            return $next($request);
        }

        // Fallback to old role column for backward compatibility
        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        abort(403, 'Unauthorized access.');
    }
}
