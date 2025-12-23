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

        // Check active role first (for role switching system)
        $activeRole = $user->getActiveRole();

        if ($activeRole) {
            // User is using role switching - check active role
            if (! in_array($activeRole, $roles) || ! $user->hasRole($activeRole)) {
                abort(403, 'Unauthorized access. Please switch to the correct role.');
            }
        } else {
            // Fallback to old role column for backward compatibility
            if (! in_array($user->role, $roles)) {
                // Also check if user has any of the required roles
                $hasRequiredRole = false;
                foreach ($roles as $role) {
                    if ($user->hasRole($role)) {
                        $hasRequiredRole = true;
                        break;
                    }
                }

                if (! $hasRequiredRole) {
                    abort(403, 'Unauthorized access.');
                }
            }
        }

        return $next($request);
    }
}
