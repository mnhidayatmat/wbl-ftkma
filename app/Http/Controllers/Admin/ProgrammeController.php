<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Programme;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProgrammeController extends Controller
{
    /**
     * Display a listing of programmes.
     */
    public function index(): View
    {
        $programmes = Programme::ordered()->get();

        return view('admin.programmes.index', compact('programmes'));
    }

    /**
     * Show the form for creating a new programme.
     */
    public function create(): View
    {
        return view('admin.programmes.create');
    }

    /**
     * Store a newly created programme.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:programmes,name',
            'short_code' => 'required|string|max:10|unique:programmes,short_code',
            'wbl_coordinator_role' => 'nullable|string|max:50',
            'wbl_coordinator_name' => 'nullable|string|max:255',
            'wbl_coordinator_email' => 'nullable|email|max:255',
            'wbl_coordinator_phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        Programme::create($validated);

        return redirect()->route('admin.programmes.index')
            ->with('success', 'Programme created successfully.');
    }

    /**
     * Show the form for editing the specified programme.
     */
    public function edit(Programme $programme): View
    {
        return view('admin.programmes.edit', compact('programme'));
    }

    /**
     * Update the specified programme.
     */
    public function update(Request $request, Programme $programme): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:programmes,name,'.$programme->id,
            'short_code' => 'required|string|max:10|unique:programmes,short_code,'.$programme->id,
            'wbl_coordinator_role' => 'nullable|string|max:50',
            'wbl_coordinator_name' => 'nullable|string|max:255',
            'wbl_coordinator_email' => 'nullable|email|max:255',
            'wbl_coordinator_phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $programme->update($validated);

        return redirect()->route('admin.programmes.index')
            ->with('success', 'Programme updated successfully.');
    }

    /**
     * Remove the specified programme.
     */
    public function destroy(Programme $programme): RedirectResponse
    {
        // Check if any students are using this programme
        $studentCount = $programme->students()->count();

        if ($studentCount > 0) {
            return redirect()->route('admin.programmes.index')
                ->with('error', "Cannot delete programme. {$studentCount} student(s) are enrolled in this programme.");
        }

        $programme->delete();

        return redirect()->route('admin.programmes.index')
            ->with('success', 'Programme deleted successfully.');
    }

    /**
     * Toggle the active status of a programme.
     */
    public function toggleActive(Programme $programme): RedirectResponse
    {
        $programme->update(['is_active' => ! $programme->is_active]);

        $status = $programme->is_active ? 'activated' : 'deactivated';

        return redirect()->route('admin.programmes.index')
            ->with('success', "Programme {$status} successfully.");
    }
}
