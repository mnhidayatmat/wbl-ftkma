<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;

class GroupVisibilityController extends Controller
{
    /**
     * Toggle group visibility mode.
     * 'active_only' = Only show active groups to all users
     * 'all' = Show all groups based on role permissions
     */
    public function toggle(): RedirectResponse
    {
        // Only admin can toggle
        if (! auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $currentMode = Cache::get('system.group_visibility_mode', 'all');
        $newMode = $currentMode === 'active_only' ? 'all' : 'active_only';

        // Store in cache (persists until cache is cleared or expires)
        Cache::forever('system.group_visibility_mode', $newMode);

        $message = $newMode === 'active_only'
            ? 'Group visibility set to Active Only. All users will only see active groups.'
            : 'Group visibility set to All Groups. Users will see groups based on their role permissions.';

        return redirect()->back()->with('success', $message);
    }

    /**
     * Get current group visibility mode.
     * Static method for use in controllers and views.
     */
    public static function getVisibilityMode(): string
    {
        return Cache::get('system.group_visibility_mode', 'all');
    }

    /**
     * Check if system is in "active only" mode.
     */
    public static function isActiveOnlyMode(): bool
    {
        return self::getVisibilityMode() === 'active_only';
    }
}
