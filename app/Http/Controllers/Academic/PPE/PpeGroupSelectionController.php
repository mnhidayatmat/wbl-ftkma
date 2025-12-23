<?php

namespace App\Http\Controllers\Academic\PPE;

use App\Http\Controllers\Controller;
use App\Models\WblGroup;
use Illuminate\View\View;

class PpeGroupSelectionController extends Controller
{
    /**
     * Display the group selection page.
     */
    public function index(): View
    {
        $user = auth()->user();

        // Admin and Coordinator can see all groups, others only active
        $groupsQuery = WblGroup::withCount('students');
        if (! $user->isAdmin() && ! $user->isCoordinator()) {
            $groupsQuery->where('status', 'ACTIVE');
        }
        $groups = $groupsQuery->orderBy('status')->orderBy('name')->get();

        return view('academic.ppe.groups.index', compact('groups'));
    }
}
