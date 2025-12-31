<?php

namespace App\Http\Controllers\Academic\LI;

use App\Http\Controllers\Controller;
use App\Models\LI\LiAuditLog;
use App\Models\LI\LiResultFinalisation;
use App\Models\Student;
use App\Models\WblGroup;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LiFinalisationController extends Controller
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
     * Display result finalisation overview.
     */
    public function index(Request $request): View
    {
        if (! auth()->user()->isAdmin() && ! auth()->user()->isLiCoordinator() && ! auth()->user()->isWblCoordinator()) {
            abort(403, 'Unauthorized access.');
        }

        // Build query for students
        $query = Student::with(['group', 'company']);

        // Filter by programme for WBL coordinators
        $programmeFilter = $this->getWblCoordinatorProgrammeFilter();
        if ($programmeFilter) {
            $query->where('programme', $programmeFilter);
        }

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('matric_no', 'like', "%{$search}%");
            });
        }

        // Apply group filter
        if ($request->filled('group')) {
            $query->where('group_id', $request->group);
        }

        // Apply finalisation status filter
        if ($request->filled('status')) {
            if ($request->status === 'finalised') {
                $finalisedStudentIds = LiResultFinalisation::where('is_finalised', true)
                    ->whereNotNull('student_id')
                    ->pluck('student_id');
                $query->whereIn('id', $finalisedStudentIds);
            } elseif ($request->status === 'not_finalised') {
                $finalisedStudentIds = LiResultFinalisation::where('is_finalised', true)
                    ->whereNotNull('student_id')
                    ->pluck('student_id');
                $query->whereNotIn('id', $finalisedStudentIds);
            }
        }

        // Get all students
        $students = $query->orderBy('name')->get();

        // Get finalisation records
        $finalisations = LiResultFinalisation::whereIn('student_id', $students->pluck('id'))
            ->where('is_finalised', true)
            ->with(['student', 'finaliser'])
            ->get()
            ->keyBy('student_id');

        // Get groups for filter
        $groups = WblGroup::orderBy('name')->get();

        // Get finalisation statistics
        $totalStudents = Student::count();
        $finalisedCount = LiResultFinalisation::where('is_finalised', true)
            ->whereNotNull('student_id')
            ->distinct('student_id')
            ->count();
        $notFinalisedCount = $totalStudents - $finalisedCount;

        return view('academic.li.finalisation.index', compact(
            'students',
            'finalisations',
            'groups',
            'totalStudents',
            'finalisedCount',
            'notFinalisedCount'
        ));
    }

    /**
     * Finalise results for a specific student.
     */
    public function finaliseStudent(Request $request, Student $student)
    {
        if (! auth()->user()->isAdmin() && ! auth()->user()->isLiCoordinator() && ! auth()->user()->isWblCoordinator()) {
            abort(403, 'Unauthorized access.');
        }

        // WBL coordinators can only finalise students from their programme
        $programmeFilter = $this->getWblCoordinatorProgrammeFilter();
        if ($programmeFilter && $student->programme !== $programmeFilter) {
            abort(403, 'You can only finalise students from your programme.');
        }

        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        // Check if already finalised
        $existing = LiResultFinalisation::where('student_id', $student->id)
            ->where('is_finalised', true)
            ->first();

        if ($existing) {
            return redirect()->back()
                ->with('error', 'This student\'s results are already finalised.');
        }

        // Create finalisation record
        $finalisation = LiResultFinalisation::updateOrCreate(
            ['student_id' => $student->id],
            [
                'finalisation_scope' => 'student',
                'is_finalised' => true,
                'notes' => $validated['notes'] ?? null,
                'finalised_by' => auth()->id(),
                'finalised_at' => now(),
            ]
        );

        // Log audit
        LiAuditLog::log(
            'result_finalised',
            'finalisation',
            "Results finalised for student {$student->name} ({$student->matric_no})",
            [
                'student_id' => $student->id,
                'finalisation_id' => $finalisation->id,
                'scope' => 'student',
            ],
            $student->id
        );

        return redirect()->route('academic.li.finalisation.index')
            ->with('success', "Results for {$student->name} have been finalised successfully.");
    }

    /**
     * Finalise results for an entire group.
     */
    public function finaliseGroup(Request $request)
    {
        if (! auth()->user()->isAdmin() && ! auth()->user()->isLiCoordinator() && ! auth()->user()->isWblCoordinator()) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'group_id' => ['required', 'exists:wbl_groups,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $group = WblGroup::findOrFail($validated['group_id']);

        // Get students in the group (filtered by programme for WBL coordinators)
        $programmeFilter = $this->getWblCoordinatorProgrammeFilter();
        $studentsQuery = $group->students();
        if ($programmeFilter) {
            $studentsQuery->where('programme', $programmeFilter);
        }
        $students = $studentsQuery->get();

        if ($students->isEmpty()) {
            return redirect()->back()
                ->with('error', 'No students found in this group.');
        }

        $finalisedCount = 0;
        foreach ($students as $student) {
            // Check if already finalised
            $existing = LiResultFinalisation::where('student_id', $student->id)
                ->where('is_finalised', true)
                ->first();

            if (! $existing) {
                LiResultFinalisation::updateOrCreate(
                    ['student_id' => $student->id],
                    [
                        'group_id' => $group->id,
                        'finalisation_scope' => 'group',
                        'is_finalised' => true,
                        'notes' => $validated['notes'] ?? null,
                        'finalised_by' => auth()->id(),
                        'finalised_at' => now(),
                    ]
                );
                $finalisedCount++;
            }
        }

        // Log audit
        LiAuditLog::log(
            'result_finalised',
            'finalisation',
            "Results finalised for group {$group->name} ({$finalisedCount} students)",
            [
                'group_id' => $group->id,
                'finalisation_scope' => 'group',
                'students_finalised' => $finalisedCount,
            ]
        );

        return redirect()->route('academic.li.finalisation.index')
            ->with('success', "Results for {$finalisedCount} students in {$group->name} have been finalised successfully.");
    }

    /**
     * Finalise results for the entire course.
     */
    public function finaliseCourse(Request $request)
    {
        if (! auth()->user()->isAdmin() && ! auth()->user()->isLiCoordinator() && ! auth()->user()->isWblCoordinator()) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:1000'],
            'confirm' => ['required', 'accepted'],
        ]);

        // Get students (filtered by programme for WBL coordinators)
        $programmeFilter = $this->getWblCoordinatorProgrammeFilter();
        $studentsQuery = Student::query();
        if ($programmeFilter) {
            $studentsQuery->where('programme', $programmeFilter);
        }
        $students = $studentsQuery->get();

        if ($students->isEmpty()) {
            return redirect()->back()
                ->with('error', 'No students found.');
        }

        $finalisedCount = 0;
        foreach ($students as $student) {
            // Check if already finalised
            $existing = LiResultFinalisation::where('student_id', $student->id)
                ->where('is_finalised', true)
                ->first();

            if (! $existing) {
                LiResultFinalisation::updateOrCreate(
                    ['student_id' => $student->id],
                    [
                        'finalisation_scope' => 'course',
                        'is_finalised' => true,
                        'notes' => $validated['notes'] ?? null,
                        'finalised_by' => auth()->id(),
                        'finalised_at' => now(),
                    ]
                );
                $finalisedCount++;
            }
        }

        // Log audit
        LiAuditLog::log(
            'result_finalised',
            'finalisation',
            "Results finalised for entire Industrial Training course ({$finalisedCount} students)",
            [
                'finalisation_scope' => 'course',
                'students_finalised' => $finalisedCount,
            ]
        );

        return redirect()->route('academic.li.finalisation.index')
            ->with('success', "Results for {$finalisedCount} students have been finalised successfully for the entire course.");
    }
}
