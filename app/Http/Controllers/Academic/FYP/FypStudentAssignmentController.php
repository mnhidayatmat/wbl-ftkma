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

        $query = Student::with(['group', 'academicTutor', 'industryCoach.company']);

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
     * Update single student AT assignment (inline edit).
     */
    public function update(Request $request, Student $student): RedirectResponse
    {
        if (! auth()->user()->isAdmin() && ! auth()->user()->isFypCoordinator()) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'at_id' => ['nullable', 'exists:users,id'],
        ]);

        // Handle AT assignment
        if ($request->has('at_id')) {
            if (! empty($validated['at_id'])) {
                $at = User::findOrFail($validated['at_id']);
                if (! $at->hasRole('lecturer') && $at->role !== 'lecturer') {
                    return redirect()->route('academic.fyp.assign-students.index', $request->query())
                        ->with('error', 'Academic Tutor must have lecturer role.');
                }
            }
            $student->update(['at_id' => $validated['at_id'] ?: null]);
        }

        return redirect()->route('academic.fyp.assign-students.index', $request->query())
            ->with('success', "Academic Tutor updated for {$student->name}.");
    }

    /**
     * Bulk update multiple students' AT assignments.
     */
    public function bulkUpdate(Request $request): RedirectResponse
    {
        if (! auth()->user()->isAdmin() && ! auth()->user()->isFypCoordinator()) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'student_ids' => ['required', 'array', 'min:1'],
            'student_ids.*' => ['exists:students,id'],
            'bulk_at_id' => ['required', 'exists:users,id'],
        ]);

        // Verify AT has lecturer role
        $at = User::findOrFail($validated['bulk_at_id']);
        if (! $at->hasRole('lecturer') && $at->role !== 'lecturer') {
            return redirect()->route('academic.fyp.assign-students.index', $request->query())
                ->with('error', 'Academic Tutor must have lecturer role.');
        }

        $count = Student::whereIn('id', $validated['student_ids'])->update(['at_id' => $validated['bulk_at_id']]);

        return redirect()->route('academic.fyp.assign-students.index', $request->query())
            ->with('success', "Academic Tutor assigned to {$count} student(s).");
    }

    /**
     * Clear AT assignment for a student.
     */
    public function clearAssignment(Request $request, Student $student): RedirectResponse
    {
        if (! auth()->user()->isAdmin() && ! auth()->user()->isFypCoordinator()) {
            abort(403, 'Unauthorized access.');
        }

        $student->update(['at_id' => null]);

        return redirect()->route('academic.fyp.assign-students.index', $request->query())
            ->with('success', "Academic Tutor cleared for {$student->name}.");
    }
}
