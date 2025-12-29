<?php

namespace App\Http\Controllers\Academic\FYP;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use App\Models\WblGroup;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FypStudentAssignmentController extends Controller
{
    /**
     * Display the student assignment page with inline edit table.
     */
    public function index(Request $request): View
    {
        if (! auth()->user()->isAdmin() && ! auth()->user()->isFypCoordinator()) {
            abort(403, 'Unauthorized access.');
        }

        $query = Student::with(['group', 'academicTutor', 'industryCoach', 'company']);

        // Filter by group
        if ($request->filled('group')) {
            $query->where('group_id', $request->group);
        }

        // Filter by assignment status
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'at_assigned':
                    $query->whereNotNull('at_id');
                    break;
                case 'ic_assigned':
                    $query->whereNotNull('ic_id');
                    break;
                case 'fully_assigned':
                    $query->whereNotNull('at_id')->whereNotNull('ic_id');
                    break;
                case 'unassigned':
                    $query->whereNull('at_id')->whereNull('ic_id');
                    break;
                case 'partial':
                    $query->where(function ($q) {
                        $q->where(function ($q2) {
                            $q2->whereNull('at_id')->whereNotNull('ic_id');
                        })->orWhere(function ($q2) {
                            $q2->whereNotNull('at_id')->whereNull('ic_id');
                        });
                    });
                    break;
            }
        }

        // Search by name or matric number
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('matric_no', 'like', "%{$search}%");
            });
        }

        // Pagination
        $perPage = $request->get('per_page', 20);
        if ($perPage === 'all') {
            $students = $query->orderBy('name')->get();
        } else {
            $students = $query->orderBy('name')->paginate((int) $perPage)->withQueryString();
        }

        // Get lecturers for AT dropdown (users with lecturer role)
        $lecturers = User::whereHas('roles', function ($q) {
            $q->where('name', 'lecturer');
        })->orWhere('role', 'lecturer')->orderBy('name')->get();

        // Get industry coaches for IC dropdown (users with industry role)
        $industryCoaches = User::whereHas('roles', function ($q) {
            $q->where('name', 'industry');
        })->orWhere('role', 'industry')->orderBy('name')->get();

        // Get groups for filter
        $groups = WblGroup::orderBy('name')->get();

        // Calculate statistics
        $stats = [
            'total' => Student::count(),
            'at_assigned' => Student::whereNotNull('at_id')->count(),
            'ic_assigned' => Student::whereNotNull('ic_id')->count(),
            'fully_assigned' => Student::whereNotNull('at_id')->whereNotNull('ic_id')->count(),
            'unassigned' => Student::whereNull('at_id')->whereNull('ic_id')->count(),
        ];

        return view('academic.fyp.assign-students.index', compact(
            'students',
            'lecturers',
            'industryCoaches',
            'groups',
            'stats'
        ));
    }

    /**
     * Update single student AT/IC assignment (inline edit).
     */
    public function update(Request $request, Student $student): RedirectResponse
    {
        if (! auth()->user()->isAdmin() && ! auth()->user()->isFypCoordinator()) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'at_id' => ['nullable', 'exists:users,id'],
            'ic_id' => ['nullable', 'exists:users,id'],
        ]);

        $updateData = [];

        // Handle AT assignment
        if ($request->has('at_id')) {
            if (! empty($validated['at_id'])) {
                $at = User::findOrFail($validated['at_id']);
                if (! $at->hasRole('lecturer') && $at->role !== 'lecturer') {
                    return redirect()->back()
                        ->withQueryString()
                        ->with('error', 'Academic Tutor must be a lecturer.');
                }
            }
            $updateData['at_id'] = $validated['at_id'] ?: null;
        }

        // Handle IC assignment
        if ($request->has('ic_id')) {
            if (! empty($validated['ic_id'])) {
                $ic = User::findOrFail($validated['ic_id']);
                if (! $ic->hasRole('industry') && $ic->role !== 'industry') {
                    return redirect()->back()
                        ->withQueryString()
                        ->with('error', 'Industry Coach must have industry role.');
                }
            }
            $updateData['ic_id'] = $validated['ic_id'] ?: null;
        }

        if (! empty($updateData)) {
            $student->update($updateData);
        }

        return redirect()->back()
            ->withQueryString()
            ->with('success', "Assignment updated for {$student->name}.");
    }

    /**
     * Bulk update multiple students' AT/IC assignments.
     */
    public function bulkUpdate(Request $request): RedirectResponse
    {
        if (! auth()->user()->isAdmin() && ! auth()->user()->isFypCoordinator()) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'student_ids' => ['required', 'array', 'min:1'],
            'student_ids.*' => ['exists:students,id'],
            'bulk_at_id' => ['nullable', 'exists:users,id'],
            'bulk_ic_id' => ['nullable', 'exists:users,id'],
        ]);

        if (empty($validated['bulk_at_id']) && empty($validated['bulk_ic_id'])) {
            return redirect()->back()
                ->withQueryString()
                ->with('error', 'Please select an AT or IC to assign.');
        }

        $updateData = [];

        // Verify and set AT
        if (! empty($validated['bulk_at_id'])) {
            $at = User::findOrFail($validated['bulk_at_id']);
            if (! $at->hasRole('lecturer') && $at->role !== 'lecturer') {
                return redirect()->back()
                    ->withQueryString()
                    ->with('error', 'Academic Tutor must be a lecturer.');
            }
            $updateData['at_id'] = $validated['bulk_at_id'];
        }

        // Verify and set IC
        if (! empty($validated['bulk_ic_id'])) {
            $ic = User::findOrFail($validated['bulk_ic_id']);
            if (! $ic->hasRole('industry') && $ic->role !== 'industry') {
                return redirect()->back()
                    ->withQueryString()
                    ->with('error', 'Industry Coach must have industry role.');
            }
            $updateData['ic_id'] = $validated['bulk_ic_id'];
        }

        $count = Student::whereIn('id', $validated['student_ids'])->update($updateData);

        return redirect()->back()
            ->withQueryString()
            ->with('success', "Updated assignments for {$count} student(s).");
    }

    /**
     * Clear assignments for a student.
     */
    public function clearAssignment(Request $request, Student $student): RedirectResponse
    {
        if (! auth()->user()->isAdmin() && ! auth()->user()->isFypCoordinator()) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'clear' => ['required', 'in:at,ic,both'],
        ]);

        $updateData = [];
        switch ($validated['clear']) {
            case 'at':
                $updateData['at_id'] = null;
                break;
            case 'ic':
                $updateData['ic_id'] = null;
                break;
            case 'both':
                $updateData['at_id'] = null;
                $updateData['ic_id'] = null;
                break;
        }

        $student->update($updateData);

        return redirect()->back()
            ->withQueryString()
            ->with('success', "Assignment cleared for {$student->name}.");
    }
}
