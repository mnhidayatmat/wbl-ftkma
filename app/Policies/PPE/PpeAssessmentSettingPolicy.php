<?php

namespace App\Policies\PPE;

use App\Models\PPE\PpeAssessmentSetting;
use App\Models\User;

class PpeAssessmentSettingPolicy
{
    /**
     * Determine whether the user can view any settings.
     */
    public function viewAny(User $user): bool
    {
        // All authenticated users can view settings (read-only for non-admin)
        return true;
    }

    /**
     * Determine whether the user can view the setting.
     */
    public function view(User $user, PpeAssessmentSetting $ppeAssessmentSetting): bool
    {
        return true; // Read-only for all
    }

    /**
     * Determine whether the user can create settings.
     */
    public function create(User $user): bool
    {
        // Only Admin can create settings
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the setting.
     */
    public function update(User $user, PpeAssessmentSetting $ppeAssessmentSetting): bool
    {
        // Only Admin can update settings
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the setting.
     */
    public function delete(User $user, PpeAssessmentSetting $ppeAssessmentSetting): bool
    {
        // Only Admin can delete settings
        return $user->isAdmin();
    }
}
