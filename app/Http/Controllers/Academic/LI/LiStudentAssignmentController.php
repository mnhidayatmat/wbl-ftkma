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
     * Get the programme filter for WBL coordinators.
     */
    private function getWblCoordinatorProgrammeFilter(): ?string
    {
        $user = auth()->user();

        if ($user->isBtaWblCoordinator()) {
            return 'Bachelor of Mechanical Engineering Technology (Automotive) with Honours';
        } elseif ($user->isBtdWblCoordinator()) {
            return 'Bachelor of Mechanical Engineering Technology (Design and Analysis) with Honours';
        } elseif ($user->isBtgWblCoordinator()) {
            return 'Bachelor of Mechanical Engineering Technology (Oil and Gas) with Honours';
        }

        return null;
    }

    /**
     * Display the student assignment page with inline edit table.
     * For LI: Lecturer can be assigned individually per student, IC is set by student during registration.
     */
    public function index(Request $request): View
    {
        if (! auth()->user()->isAdmin() && ! auth()->user()->isLiCoordinator() && ! auth()->user()->isWblCoordinator()) {
            abort(403, 'Unauthorized access.');
        }

        $query = Student::with(['group', 'academicTutor', 'industryCoach.company']);

        // Filter by programme for WBL coordinators
        $programmeFilter = $this->getWblCoordinatorProgrammeFilter();
        if ($programmeFilter) {
            $query->where('programme', $programmeFilter);
        }

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

        // Calculate statistics (filtered by programme for WBL coordinators)
        $statsQuery = Student::query();
        if ($programmeFilter) {
            $statsQuery->where('programme', $programmeFilter);
        }
        $stats = [
            'total' => (clone $statsQuery)->count(),
            'at_assigned' => (clone $statsQuery)->whereNotNull('at_id')->count(),
            'at_unassigned' => (clone $statsQuery)->whereNull('at_id')->count(),
            'ic_assigned' => (clone $statsQuery)->whereNotNull('ic_id')->count(),
            'ic_unassigned' => (clone $statsQuery)->whereNull('ic_id')->count(),
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
        if (! auth()->user()->isAdmin() && ! auth()->user()->isLiCoordinator() && ! auth()->user()->isWblCoordinator()) {
            abort(403, 'Unauthorized access.');
        }

        // WBL coordinators can only update students from their programme
        $programmeFilter = $this->getWblCoordinatorProgrammeFilter();
        if ($programmeFilter && $student->programme !== $programmeFilter) {
            abort(403, 'You can only manage students from your programme.');
        }

        $validated = $request->validate([
            'at_id' => ['nullable', 'exists:users,id'],
        ]);

        // Handle AT assignment
        if ($request->has('at_id')) {
            if (! empty($validated['at_id'])) {
                $at = User::findOrFail($validated['at_id']);
                if (! $at->hasRole('lecturer') && $at->role !== 'lecturer') {
                    return redirect()->route('academic.li.assign-students.index', $request->query())
                        ->with('error', 'Supervisor LI must have lecturer role.');
                }
            }
            $student->update(['at_id' => $validated['at_id'] ?: null]);
        }

        return redirect()->route('academic.li.assign-students.index', $request->query())
            ->with('success', "Supervisor LI updated for {$student->name}.");
    }

    /**
     * Bulk update multiple students' Supervisor LI assignments.
     */
    public function bulkUpdate(Request $request): RedirectResponse
    {
        if (! auth()->user()->isAdmin() && ! auth()->user()->isLiCoordinator() && ! auth()->user()->isWblCoordinator()) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'student_ids' => ['required', 'array', 'min:1'],
            'student_ids.*' => ['exists:students,id'],
            'bulk_at_id' => ['required', 'exists:users,id'],
        ]);

        // WBL coordinators can only update students from their programme
        $programmeFilter = $this->getWblCoordinatorProgrammeFilter();
        $query = Student::whereIn('id', $validated['student_ids']);
        if ($programmeFilter) {
            $query->where('programme', $programmeFilter);
        }

        // Verify lecturer role
        $at = User::findOrFail($validated['bulk_at_id']);
        if (! $at->hasRole('lecturer') && $at->role !== 'lecturer') {
            return redirect()->route('academic.li.assign-students.index', $request->query())
                ->with('error', 'Selected user must be a lecturer.');
        }

        $count = $query->update(['at_id' => $validated['bulk_at_id']]);

        return redirect()->route('academic.li.assign-students.index', $request->query())
            ->with('success', "Supervisor LI assigned to {$count} student(s).");
    }

    /**
     * Clear Supervisor LI assignment for a student.
     */
    public function clearAssignment(Request $request, Student $student): RedirectResponse
    {
        if (! auth()->user()->isAdmin() && ! auth()->user()->isLiCoordinator() && ! auth()->user()->isWblCoordinator()) {
            abort(403, 'Unauthorized access.');
        }

        // WBL coordinators can only clear assignments for students from their programme
        $programmeFilter = $this->getWblCoordinatorProgrammeFilter();
        if ($programmeFilter && $student->programme !== $programmeFilter) {
            abort(403, 'You can only manage students from your programme.');
        }

        $student->update(['at_id' => null]);

        return redirect()->route('academic.li.assign-students.index', $request->query())
            ->with('success', "Supervisor LI cleared for {$student->name}.");
    }

    /**
     * Export student list with assignments.
     */
    public function export(Request $request)
    {
        if (! auth()->user()->isAdmin() && ! auth()->user()->isLiCoordinator() && ! auth()->user()->isWblCoordinator()) {
            abort(403, 'Unauthorized access.');
        }

        $query = Student::with(['group', 'academicTutor', 'industryCoach.company']);

        // Filter by programme for WBL coordinators
        $programmeFilter = $this->getWblCoordinatorProgrammeFilter();
        if ($programmeFilter) {
            $query->where('programme', $programmeFilter);
        }

        $students = $query->orderBy('name')->get();

        // Return as CSV download
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="li-student-assignments.csv"',
        ];

        $callback = function () use ($students) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['No', 'Student Name', 'Matric No', 'Programme', 'Group', 'Supervisor LI', 'Industry Coach', 'Company (IC)', 'Company Address']);

            foreach ($students as $index => $student) {
                fputcsv($file, [
                    $index + 1,
                    $student->name,
                    $student->matric_no,
                    $student->programme ?? 'N/A',
                    $student->group->name ?? 'N/A',
                    $student->academicTutor->name ?? 'Not Assigned',
                    $student->industryCoach->name ?? 'Not Assigned',
                    $student->industryCoach->company->company_name ?? 'N/A',
                    $student->industryCoach->company->address ?? 'N/A',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
