<?php

namespace App\Http\Controllers\Academic\LI;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use App\Models\WblGroup;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LiStudentAssignmentController extends Controller
{
    /**
     * Display the student assignment page with inline edit table.
     * For LI: Lecturer can be assigned individually per student, IC is set by student during registration.
     */
    public function index(Request $request): View
    {
        if (! auth()->user()->isAdmin() && ! auth()->user()->isLiCoordinator()) {
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
                case 'at_unassigned':
                    $query->whereNull('at_id');
                    break;
                case 'ic_assigned':
                    $query->whereNotNull('ic_id');
                    break;
                case 'ic_unassigned':
                    $query->whereNull('ic_id');
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

        // Get lecturers for dropdown (users with lecturer role)
        $lecturers = User::whereHas('roles', function ($q) {
            $q->where('name', 'lecturer');
        })->orWhere('role', 'lecturer')->orderBy('name')->get();

        // Get groups for filter
        $groups = WblGroup::orderBy('name')->get();

        // Calculate statistics
        $stats = [
            'total' => Student::count(),
            'at_assigned' => Student::whereNotNull('at_id')->count(),
            'at_unassigned' => Student::whereNull('at_id')->count(),
            'ic_assigned' => Student::whereNotNull('ic_id')->count(),
            'ic_unassigned' => Student::whereNull('ic_id')->count(),
        ];

        return view('academic.li.assign-students.index', compact(
            'students',
            'lecturers',
            'groups',
            'stats'
        ));
    }

    /**
     * Update single student lecturer assignment (inline edit).
     */
    public function update(Request $request, Student $student): RedirectResponse
    {
        if (! auth()->user()->isAdmin() && ! auth()->user()->isLiCoordinator()) {
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
                    return redirect()->back()
                        ->withQueryString()
                        ->with('error', 'Lecturer must have lecturer role.');
                }
            }
            $student->update(['at_id' => $validated['at_id'] ?: null]);
        }

        return redirect()->back()
            ->withQueryString()
            ->with('success', "Lecturer updated for {$student->name}.");
    }

    /**
     * Bulk update multiple students' lecturer assignments.
     */
    public function bulkUpdate(Request $request): RedirectResponse
    {
        if (! auth()->user()->isAdmin() && ! auth()->user()->isLiCoordinator()) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'student_ids' => ['required', 'array', 'min:1'],
            'student_ids.*' => ['exists:students,id'],
            'bulk_at_id' => ['required', 'exists:users,id'],
        ]);

        // Verify lecturer role
        $at = User::findOrFail($validated['bulk_at_id']);
        if (! $at->hasRole('lecturer') && $at->role !== 'lecturer') {
            return redirect()->back()
                ->withQueryString()
                ->with('error', 'Selected user must be a lecturer.');
        }

        $count = Student::whereIn('id', $validated['student_ids'])->update(['at_id' => $validated['bulk_at_id']]);

        return redirect()->back()
            ->withQueryString()
            ->with('success', "Lecturer assigned to {$count} student(s).");
    }

    /**
     * Clear lecturer assignment for a student.
     */
    public function clearAssignment(Request $request, Student $student): RedirectResponse
    {
        if (! auth()->user()->isAdmin() && ! auth()->user()->isLiCoordinator()) {
            abort(403, 'Unauthorized access.');
        }

        $student->update(['at_id' => null]);

        return redirect()->back()
            ->withQueryString()
            ->with('success', "Lecturer cleared for {$student->name}.");
    }

    /**
     * Export student list with assignments.
     */
    public function export(Request $request)
    {
        if (! auth()->user()->isAdmin() && ! auth()->user()->isLiCoordinator()) {
            abort(403, 'Unauthorized access.');
        }

        $students = Student::with(['group', 'academicTutor', 'industryCoach', 'company'])
            ->orderBy('name')
            ->get();

        // Return as CSV download
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="li-student-assignments.csv"',
        ];

        $callback = function () use ($students) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['No', 'Student Name', 'Matric No', 'Programme', 'Group', 'Lecturer', 'Industry Coach', 'Company']);

            foreach ($students as $index => $student) {
                fputcsv($file, [
                    $index + 1,
                    $student->name,
                    $student->matric_no,
                    $student->programme ?? 'N/A',
                    $student->group->name ?? 'N/A',
                    $student->academicTutor->name ?? 'Not Assigned',
                    $student->industryCoach->name ?? 'Not Assigned',
                    $student->company->company_name ?? 'N/A',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
