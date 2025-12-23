<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LecturerCourseAssignment;
use App\Models\Student;
use App\Models\StudentCourseAssignment;
use App\Models\User;
use App\Models\WblGroup;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentAssignmentController extends Controller
{
    /**
     * Display the student assignment page.
     */
    public function index(Request $request): View
    {
        // Only admin can access
        if (! auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $query = Student::with(['group', 'academicTutor', 'industryCoach']);

        // Filter by group
        if ($request->has('group') && $request->group) {
            $query->where('group_id', $request->group);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('matric_no', 'like', "%{$search}%");
            });
        }

        $students = $query->orderBy('name')->paginate(20)->withQueryString();

        // Get all lecturers (AT)
        $lecturers = User::where('role', 'lecturer')->orderBy('name')->get();

        // Get all industry coaches (IC)
        $industryCoaches = User::where('role', 'industry')->orderBy('name')->get();

        // Get all groups for filter (Admin can see all)
        $groups = WblGroup::orderBy('status')->orderBy('name')->get();

        // Get lecturer-course assignments for the lecturer assignment tab
        $lecturerAssignments = LecturerCourseAssignment::with('lecturer')
            ->orderBy('course_type')
            ->orderBy('lecturer_id')
            ->get()
            ->groupBy('course_type');

        // Define available courses
        $courses = [
            'FYP' => 'Final Year Project',
            'IP' => 'Internship Preparation',
            'OSH' => 'Occupational Safety & Health',
            'PPE' => 'Professional Practice & Ethics',
            'Industrial Training' => 'Industrial Training',
            'IC' => 'Industry Coach',
        ];

        // Get active course tab from request
        $activeCourse = $request->get('course', 'FYP');

        // Validate course
        if (! in_array($activeCourse, array_keys($courses))) {
            $activeCourse = 'FYP';
        }

        // Get student-course assignments for the active course (for current page students)
        $studentIds = $students->pluck('id');
        $studentCourseAssignments = StudentCourseAssignment::with('lecturer')
            ->where('course_type', $activeCourse)
            ->whereIn('student_id', $studentIds)
            ->get()
            ->keyBy('student_id');

        // Get lecturers assigned to this course (for filtering dropdown)
        $courseLecturers = LecturerCourseAssignment::with('lecturer')
            ->where('course_type', $activeCourse)
            ->get()
            ->pluck('lecturer')
            ->filter()
            ->unique('id');

        // If no lecturers assigned to course, show all lecturers
        if ($courseLecturers->isEmpty()) {
            $courseLecturers = $lecturers;
        }

        return view('admin.students.assign', compact(
            'students',
            'lecturers',
            'industryCoaches',
            'groups',
            'lecturerAssignments',
            'courses',
            'activeCourse',
            'studentCourseAssignments',
            'courseLecturers'
        ));
    }

    /**
     * Update student assignments (AT and IC).
     */
    public function update(Request $request, Student $student): RedirectResponse
    {
        // Only admin can update
        if (! auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        // Ensure both fields are present in the request, even if null
        $request->merge([
            'at_id' => $request->input('at_id', $student->at_id),
            'ic_id' => $request->input('ic_id', $student->ic_id),
        ]);

        $validated = $request->validate([
            'at_id' => ['nullable', 'exists:users,id'],
            'ic_id' => ['nullable', 'exists:users,id'],
        ]);

        // Safely extract values with null fallback
        $atId = $validated['at_id'] ?? null;
        $icId = $validated['ic_id'] ?? null;

        // Verify that at_id is a lecturer if provided
        if (! empty($atId)) {
            $at = User::findOrFail($atId);
            if ($at->role !== 'lecturer') {
                return redirect()->back()
                    ->with('error', 'Academic Tutor must be a lecturer.');
            }
        }

        // Verify that ic_id is an industry coach if provided
        if (! empty($icId)) {
            $ic = User::findOrFail($icId);
            if ($ic->role !== 'industry') {
                return redirect()->back()
                    ->with('error', 'Industry Coach must have industry role.');
            }
        }

        // Update with null-safe values
        $student->update([
            'at_id' => $atId ?: null,
            'ic_id' => $icId ?: null,
        ]);

        return redirect()->back()
            ->with('success', "Assignments updated for {$student->name}.");
    }

    /**
     * Bulk update assignments.
     */
    public function bulkUpdate(Request $request): RedirectResponse
    {
        // Only admin can update
        if (! auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'students' => ['required', 'array'],
            'students.*.id' => ['required', 'exists:students,id'],
            'students.*.at_id' => ['nullable', 'exists:users,id'],
            'students.*.ic_id' => ['nullable', 'exists:users,id'],
        ]);

        $updated = 0;
        foreach ($validated['students'] as $studentData) {
            $student = Student::findOrFail($studentData['id']);

            // Verify roles
            if (isset($studentData['at_id']) && $studentData['at_id']) {
                $at = User::findOrFail($studentData['at_id']);
                if ($at->role !== 'lecturer') {
                    continue;
                }
            }

            if (isset($studentData['ic_id']) && $studentData['ic_id']) {
                $ic = User::findOrFail($studentData['ic_id']);
                if ($ic->role !== 'industry') {
                    continue;
                }
            }

            $student->update([
                'at_id' => $studentData['at_id'] ?? null,
                'ic_id' => $studentData['ic_id'] ?? null,
            ]);
            $updated++;
        }

        return redirect()->back()
            ->with('success', "Updated assignments for {$updated} student(s).");
    }

    /**
     * Store lecturer-course assignment.
     */
    public function storeLecturerAssignment(Request $request): RedirectResponse
    {
        // Only admin can update
        if (! auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'lecturer_id' => ['required', 'exists:users,id'],
            'course_type' => ['required', 'in:FYP,IP,OSH,PPE,Industrial Training,IC'],
        ]);

        // Verify that user is a lecturer
        $lecturer = User::findOrFail($validated['lecturer_id']);
        if ($lecturer->role !== 'lecturer') {
            return redirect()->back()
                ->with('error', 'Selected user must be a lecturer.');
        }

        // Create or update assignment (unique constraint ensures one per lecturer-course)
        LecturerCourseAssignment::updateOrCreate(
            [
                'lecturer_id' => $validated['lecturer_id'],
                'course_type' => $validated['course_type'],
            ],
            [
                'lecturer_id' => $validated['lecturer_id'],
                'course_type' => $validated['course_type'],
            ]
        );

        return redirect()->back()
            ->with('success', "Lecturer assigned to {$validated['course_type']} successfully.");
    }

    /**
     * Remove lecturer-course assignment.
     */
    public function removeLecturerAssignment(LecturerCourseAssignment $assignment): RedirectResponse
    {
        // Only admin can remove
        if (! auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $courseType = $assignment->course_type;
        $lecturerName = $assignment->lecturer->name;

        $assignment->delete();

        return redirect()->back()
            ->with('success', "Removed {$lecturerName} from {$courseType}.");
    }

    /**
     * Update student-course assignment.
     */
    public function updateStudentCourseAssignment(Request $request, Student $student): RedirectResponse
    {
        // Only admin can update
        if (! auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'course_type' => ['required', 'in:FYP,IP,OSH,PPE,Industrial Training,IC'],
            'lecturer_id' => ['nullable', 'exists:users,id'],
        ]);

        // Verify that lecturer_id is a lecturer if provided
        if (! empty($validated['lecturer_id'])) {
            $lecturer = User::findOrFail($validated['lecturer_id']);
            if ($lecturer->role !== 'lecturer') {
                return redirect()->back()
                    ->with('error', 'Selected user must be a lecturer.');
            }
        }

        // Create or update assignment
        StudentCourseAssignment::updateOrCreate(
            [
                'student_id' => $student->id,
                'course_type' => $validated['course_type'],
            ],
            [
                'lecturer_id' => $validated['lecturer_id'] ?? null,
            ]
        );

        return redirect()->back()
            ->with('success', "Assignment updated for {$student->name}.");
    }

    /**
     * Remove student-course assignment.
     */
    public function removeStudentCourseAssignment(StudentCourseAssignment $assignment): RedirectResponse
    {
        // Only admin can remove
        if (! auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $studentName = $assignment->student->name;
        $courseType = $assignment->course_type;

        $assignment->delete();

        return redirect()->back()
            ->with('success', "Removed assignment for {$studentName} from {$courseType}.");
    }
}
