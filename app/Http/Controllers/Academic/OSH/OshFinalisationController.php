<?php

namespace App\Http\Controllers\Academic\OSH;

use App\Http\Controllers\Controller;
use App\Models\OSH\OshAuditLog;
use App\Models\OSH\OshResultFinalisation;
use App\Models\Student;
use App\Models\WblGroup;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OshFinalisationController extends Controller
{
    /**
     * Display result finalisation overview.
     */
    public function index(Request $request): View
    {
        if (! auth()->user()->isAdmin() && ! auth()->user()->isOshCoordinator()) {
            abort(403, 'Unauthorized access.');
        }

        // Build query for students
        $query = Student::with(['group', 'company']);

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
                $finalisedStudentIds = OshResultFinalisation::where('is_finalised', true)
                    ->whereNotNull('student_id')
                    ->pluck('student_id');
                $query->whereIn('id', $finalisedStudentIds);
            } elseif ($request->status === 'not_finalised') {
                $finalisedStudentIds = OshResultFinalisation::where('is_finalised', true)
                    ->whereNotNull('student_id')
                    ->pluck('student_id');
                $query->whereNotIn('id', $finalisedStudentIds);
            }
        }

        // Get all students
        $students = $query->orderBy('name')->get();

        // Get finalisation records
        $finalisations = OshResultFinalisation::whereIn('student_id', $students->pluck('id'))
            ->where('is_finalised', true)
            ->with(['student', 'finaliser'])
            ->get()
            ->keyBy('student_id');

        // Get groups for filter
        $groups = WblGroup::orderBy('name')->get();

        // Get finalisation statistics
        $totalStudents = Student::count();
        $finalisedCount = OshResultFinalisation::where('is_finalised', true)
            ->whereNotNull('student_id')
            ->distinct('student_id')
            ->count();
        $notFinalisedCount = $totalStudents - $finalisedCount;

        return view('academic.osh.finalisation.index', compact(
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
        if (! auth()->user()->isAdmin() && ! auth()->user()->isOshCoordinator()) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        // Check if already finalised
        $existing = OshResultFinalisation::where('student_id', $student->id)
            ->where('is_finalised', true)
            ->first();

        if ($existing) {
            return redirect()->back()
                ->with('error', 'This student\'s results are already finalised.');
        }

        // Create finalisation record
        $finalisation = OshResultFinalisation::updateOrCreate(
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
        OshAuditLog::log(
            'result_finalised',
            'finalisation',
            "Results finalised for student {$student->name} ({$student->matric_no})",
            [
                'student_id' => $student->id,
                'finalisation_id' => $finalisation->id,
                'scope' => 'student',
            ],
            $student->id,
            null
        );

        return redirect()->route('academic.osh.finalisation.index')
            ->with('success', "Results for {$student->name} have been finalised successfully.");
    }

    /**
     * Finalise results for an entire group.
     */
    public function finaliseGroup(Request $request)
    {
        if (! auth()->user()->isAdmin() && ! auth()->user()->isOshCoordinator()) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'group_id' => ['required', 'exists:wbl_groups,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $group = WblGroup::findOrFail($validated['group_id']);

        // Get all students in the group
        $students = $group->students;

        if ($students->isEmpty()) {
            return redirect()->back()
                ->with('error', 'No students found in this group.');
        }

        $finalisedCount = 0;
        foreach ($students as $student) {
            // Check if already finalised
            $existing = OshResultFinalisation::where('student_id', $student->id)
                ->where('is_finalised', true)
                ->first();

            if (! $existing) {
                OshResultFinalisation::updateOrCreate(
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
        OshAuditLog::log(
            'result_finalised',
            'finalisation',
            "Results finalised for group {$group->name} ({$finalisedCount} students)",
            [
                'group_id' => $group->id,
                'finalisation_scope' => 'group',
                'students_finalised' => $finalisedCount,
            ],
            null,
            null
        );

        return redirect()->route('academic.osh.finalisation.index')
            ->with('success', "Results for {$finalisedCount} students in {$group->name} have been finalised successfully.");
    }

    /**
     * Finalise results for the entire course.
     */
    public function finaliseCourse(Request $request)
    {
        if (! auth()->user()->isAdmin() && ! auth()->user()->isOshCoordinator()) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:1000'],
            'confirm' => ['required', 'accepted'],
        ]);

        // Get all students
        $students = Student::all();

        if ($students->isEmpty()) {
            return redirect()->back()
                ->with('error', 'No students found.');
        }

        $finalisedCount = 0;
        foreach ($students as $student) {
            // Check if already finalised
            $existing = OshResultFinalisation::where('student_id', $student->id)
                ->where('is_finalised', true)
                ->first();

            if (! $existing) {
                OshResultFinalisation::updateOrCreate(
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
        OshAuditLog::log(
            'result_finalised',
            'finalisation',
            "Results finalised for entire OSH course ({$finalisedCount} students)",
            [
                'finalisation_scope' => 'course',
                'students_finalised' => $finalisedCount,
            ],
            null,
            null
        );

        return redirect()->route('academic.osh.finalisation.index')
            ->with('success', "Results for {$finalisedCount} students have been finalised successfully for the entire course.");
    }
}
