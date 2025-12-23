<?php

namespace App\Http\Controllers;

use App\Models\WblGroup;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        
        // Admin and Coordinator can view all groups
        // Others can only view active groups
        $query = WblGroup::withCount('students');
        
        if ($user->isAdmin() || $user->isCoordinator()) {
            // Filter by status if provided
            if ($request->has('status') && in_array($request->status, ['ACTIVE', 'COMPLETED'])) {
                $query->where('status', $request->status);
            }
        } else {
            // Lecturer/AT/IC/Supervisor LI can only see active groups
            $query->where('status', 'ACTIVE');
        }
        
        $groups = $query->orderBy('status')->orderBy('end_date', 'desc')->paginate(15)->withQueryString();
        
        // Statistics
        $stats = [
            'total' => WblGroup::count(),
            'active' => WblGroup::where('status', 'ACTIVE')->count(),
            'completed' => WblGroup::where('status', 'COMPLETED')->count(),
        ];

        return view('groups.index', compact('groups', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        // Only admin can create groups
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access. Only administrators can create groups.');
        }

        return view('groups.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // Only admin can create groups
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access. Only administrators can create groups.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
        ]);

        $validated['status'] = 'ACTIVE'; // New groups are always active

        WblGroup::create($validated);

        return redirect()->route('admin.groups.index')
            ->with('success', 'Group created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(WblGroup $group): View
    {
        $user = auth()->user();
        
        // Check access based on role
        if (!$user->isAdmin() && !$user->isCoordinator()) {
            // Lecturer/AT/IC/Supervisor LI can only view active groups
            if (!$group->isActive()) {
                abort(403, 'You can only view active groups.');
            }
        }
        
        $group->load('students.company');

        return view('groups.show', compact('group'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(WblGroup $group): View
    {
        // Only admin can edit groups
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access. Only administrators can edit groups.');
        }

        // Prevent editing completed groups (use reopen instead)
        if ($group->isCompleted()) {
            return redirect()->route('admin.groups.index')
                ->with('error', 'Cannot edit completed groups. Please reopen the group first if you need to make changes.');
        }

        return view('groups.edit', compact('group'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, WblGroup $group): RedirectResponse
    {
        // Only admin can update groups
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access. Only administrators can update groups.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
        ]);

        $group->update($validated);

        return redirect()->route('admin.groups.index')
            ->with('success', 'Group updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WblGroup $group): RedirectResponse
    {
        // Only admin can delete groups
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access. Only administrators can delete groups.');
        }

        $group->delete();

        return redirect()->route('admin.groups.index')
            ->with('success', 'Group deleted successfully.');
    }

    /**
     * Mark a group as completed/archived.
     */
    public function markCompleted(WblGroup $group): RedirectResponse
    {
        // Only admin can mark groups as completed
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access. Only administrators can close groups.');
        }

        if ($group->isCompleted()) {
            return back()->with('error', 'This group is already completed.');
        }

        $group->update([
            'status' => 'COMPLETED',
            'completed_at' => now(),
        ]);

        return back()->with('success', "Group '{$group->name}' has been marked as completed. Students in this group will have limited access.");
    }

    /**
     * Reopen a completed group.
     */
    public function reopen(WblGroup $group): RedirectResponse
    {
        // Only admin can reopen groups
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access. Only administrators can reopen groups.');
        }

        if ($group->isActive()) {
            return back()->with('error', 'This group is already active.');
        }

        $group->update([
            'status' => 'ACTIVE',
            'completed_at' => null,
        ]);

        return back()->with('success', "Group '{$group->name}' has been reopened and is now active.");
    }
}

