<?php

namespace App\Http\Controllers\Academic\IP;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use App\Models\WblGroup;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IpStudentAssignmentController extends Controller
{
    /**
     * Display the student assignment page.
     * For IP: Single lecturer assigned to all students, IC is set by student during registration.
     */
    public function index(Request $request): View
    {
        if (! auth()->user()->isAdmin() && ! auth()->user()->isIpCoordinator()) {
            abort(403, 'Unauthorized access.');
        }

        $query = Student::with(['group', 'academicTutor', 'industryCoach', 'company']);

        // Filter by group
        if ($request->filled('group')) {
            $query->where('group_id', $request->group);
        }

        // Filter by IC assignment status
        if ($request->filled('status')) {
            switch ($request->status) {
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

        // Get the currently assigned IP lecturer (check if all students have same lecturer)
        $currentLecturer = $this->getCurrentIpLecturer();

        // Calculate statistics
        $totalStudents = Student::count();
        $stats = [
            'total' => $totalStudents,
            'with_lecturer' => Student::whereNotNull('at_id')->count(),
            'ic_assigned' => Student::whereNotNull('ic_id')->count(),
            'ic_unassigned' => Student::whereNull('ic_id')->count(),
        ];

        return view('academic.ip.assign-students.index', compact(
            'students',
            'lecturers',
            'groups',
            'stats',
            'currentLecturer'
        ));
    }

    /**
     * Get the current IP lecturer assigned to students.
     * Returns the lecturer if all students have the same one, null otherwise.
     */
    private function getCurrentIpLecturer(): ?User
    {
        $lecturerIds = Student::whereNotNull('at_id')
            ->distinct()
            ->pluck('at_id')
            ->toArray();

        // If all students have the same lecturer
        if (count($lecturerIds) === 1) {
            return User::find($lecturerIds[0]);
        }

        // If no lecturer assigned or multiple different lecturers
        return null;
    }

    /**
     * Assign a single lecturer to ALL students (IP module specific).
     */
    public function assignLecturerToAll(Request $request): RedirectResponse
    {
        if (! auth()->user()->isAdmin() && ! auth()->user()->isIpCoordinator()) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'lecturer_id' => ['required', 'exists:users,id'],
        ]);

        $lecturer = User::findOrFail($validated['lecturer_id']);

        // Verify user is a lecturer
        if (! $lecturer->hasRole('lecturer') && $lecturer->role !== 'lecturer') {
            return redirect()->back()
                ->with('error', 'Selected user must have lecturer role.');
        }

        // Update all students with this lecturer
        $count = Student::query()->update(['at_id' => $lecturer->id]);

        return redirect()->back()
            ->with('success', "Lecturer {$lecturer->name} has been assigned to all {$count} students.");
    }

    /**
     * Clear lecturer assignment from all students.
     */
    public function clearLecturerFromAll(): RedirectResponse
    {
        if (! auth()->user()->isAdmin() && ! auth()->user()->isIpCoordinator()) {
            abort(403, 'Unauthorized access.');
        }

        $count = Student::whereNotNull('at_id')->update(['at_id' => null]);

        return redirect()->back()
            ->with('success', "Lecturer assignment cleared from {$count} students.");
    }

    /**
     * Export student list with assignments.
     */
    public function export(Request $request)
    {
        if (! auth()->user()->isAdmin() && ! auth()->user()->isIpCoordinator()) {
            abort(403, 'Unauthorized access.');
        }

        $students = Student::with(['group', 'academicTutor', 'industryCoach', 'company'])
            ->orderBy('name')
            ->get();

        // Return as CSV download
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="ip-student-assignments.csv"',
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
