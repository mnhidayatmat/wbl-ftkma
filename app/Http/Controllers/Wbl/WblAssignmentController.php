<?php

namespace App\Http\Controllers\Wbl;

use App\Http\Controllers\Controller;
use App\Models\CourseSetting;
use App\Models\LecturerCourseAssignment;
use App\Models\Student;
use App\Models\StudentCourseAssignment;
use App\Models\User;
use App\Models\WblGroup;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WblAssignmentController extends Controller
{
    /**
     * Display the unified WBL assignment page with course tabs.
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        
        // Detect course from route name
        $routeName = $request->route()->getName();
        $activeCourse = null;
        
        // Map route names to course codes
        if (str_contains($routeName, 'ppe.assign-students')) {
            $activeCourse = 'PPE';
        } elseif (str_contains($routeName, 'fyp.assign-students')) {
            $activeCourse = 'FYP';
        } elseif (str_contains($routeName, 'ip.assign-students')) {
            $activeCourse = 'IP';
        } elseif (str_contains($routeName, 'osh.assign-students')) {
            $activeCourse = 'OSH';
        } elseif (str_contains($routeName, 'li.assign-students')) {
            $activeCourse = 'LI';
        } elseif (str_contains($routeName, 'academic.ppe.assign-students')) {
            $activeCourse = 'PPE';
        } else {
            // Fallback: use course from request or default
            $courses = $this->getAvailableCourses($user);
            $activeCourse = $request->get('course', array_key_first($courses));
            if (!isset($courses[$activeCourse])) {
                $activeCourse = array_key_first($courses);
            }
        }

        // Build student query
        $query = Student::with(['group']);

        // Filter by group
        if ($request->has('group') && $request->group) {
            $query->where('group_id', $request->group);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('matric_no', 'like', "%{$search}%");
            });
        }

        $students = $query->orderBy('name')->paginate(20)->withQueryString();

        // Get all groups for filter
        $groups = WblGroup::orderBy('name')->get();

        // Get assignment data based on active course
        $assignmentData = $this->getAssignmentData($activeCourse, $students);
        
        // For module-specific pages, we only show one course (no tabs)
        $courses = [$activeCourse => $this->getCourseDisplayName($activeCourse)];

        return view('wbl.assign.index', compact(
            'students',
            'groups',
            'courses',
            'activeCourse',
            'assignmentData',
            'user'
        ));
    }
    
    /**
     * Get course display name.
     */
    private function getCourseDisplayName(string $courseCode): string
    {
        return match($courseCode) {
            'PPE' => 'Professional Practice & Ethics',
            'FYP' => 'Final Year Project',
            'IP' => 'Internship Preparation',
            'OSH' => 'Occupational Safety & Health',
            'LI' => 'Industrial Training',
            'IC' => 'Industry Coach',
            default => $courseCode,
        };
    }

    /**
     * Update student assignment for a specific course.
     */
    public function update(Request $request, Student $student = null): RedirectResponse
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'course_type' => ['required', 'in:FYP,IP,OSH,PPE,LI,IC'],
            'assignee_id' => ['nullable', 'exists:users,id'],
        ]);

        $courseType = $validated['course_type'];
        $assigneeId = $validated['assignee_id'] ?? null;

        // Verify assignee role based on course type
        if ($assigneeId) {
            $assignee = User::findOrFail($assigneeId);
            $this->validateAssigneeRole($assignee, $courseType);
        }

        // Handle assignment based on course type
        if (in_array($courseType, ['IP', 'OSH', 'PPE'])) {
            // Single lecturer assignment for entire course
            $this->assignSingleLecturer($courseType, $assigneeId);
            return redirect()->back()
                ->with('success', ucfirst($courseType) . " lecturer assigned successfully.");
        } else {
            // Individual assignment per student
            if (!$student) {
                abort(404, 'Student not found.');
            }
            $this->assignStudentToCourse($student, $courseType, $assigneeId);
            return redirect()->back()
                ->with('success', "Assignment updated for {$student->name}.");
        }
    }

    /**
     * Remove student assignment.
     */
    public function remove(Request $request, Student $student = null): RedirectResponse
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'course_type' => ['required', 'in:FYP,IP,OSH,PPE,LI,IC'],
        ]);

        $courseType = $validated['course_type'];

        if (in_array($courseType, ['IP', 'OSH', 'PPE'])) {
            // Remove single lecturer assignment
            $courseSetting = CourseSetting::where('course_type', $courseType)->first();
            if ($courseSetting) {
                $courseSetting->update([
                    'lecturer_id' => null,
                    'updated_by' => auth()->id(),
                ]);
            }
            return redirect()->back()
                ->with('success', ucfirst($courseType) . " lecturer removed successfully.");
        } else {
            // Remove individual student assignment
            if (!$student) {
                abort(404, 'Student not found.');
            }
            $this->removeStudentAssignment($student, $courseType);
            return redirect()->back()
                ->with('success', "Assignment removed for {$student->name}.");
        }
    }

    /**
     * Get available courses based on user role.
     */
    private function getAvailableCourses(User $user): array
    {
        $allCourses = [
            'FYP' => 'Final Year Project',
            'IP' => 'Internship Preparation',
            'OSH' => 'Occupational Safety & Health',
            'PPE' => 'Professional Practice & Ethics',
            'LI' => 'Industrial Training',
            'IC' => 'Industry Coach',
        ];

        if ($user->isAdmin()) {
            return $allCourses;
        }

        $available = [];

        // AT sees FYP
        if ($user->isAt()) {
            $available['FYP'] = $allCourses['FYP'];
        }

        // Lecturer sees courses they're assigned to
        if ($user->isLecturer()) {
            $lecturerCourses = LecturerCourseAssignment::where('lecturer_id', $user->id)
                ->pluck('course_type')
                ->toArray();

            foreach ($lecturerCourses as $course) {
                if (isset($allCourses[$course])) {
                    $available[$course] = $allCourses[$course];
                }
            }
        }

        // Supervisor LI sees LI
        if ($user->isSupervisorLi()) {
            $available['LI'] = $allCourses['LI'];
        }

        // IC sees IC
        if ($user->isIndustry()) {
            $available['IC'] = $allCourses['IC'];
        }

        return $available ?: ['FYP' => $allCourses['FYP']]; // Default to FYP if nothing available
    }

    /**
     * Get assignment data for the active course.
     */
    private function getAssignmentData(string $courseType, $students): array
    {
        $data = [
            'assignees' => [],
            'assignments' => [],
            'assignment_type' => 'individual', // 'individual' or 'single_lecturer'
            'current_lecturer' => null, // For single lecturer courses
        ];

        switch ($courseType) {
            case 'FYP':
                // Individual AT assignment per student
                $data['assignment_type'] = 'individual';
                $data['assignees'] = User::where('role', 'lecturer')
                    ->orderBy('name')
                    ->get();
                
                // Get AT assignments (at_id) - map student_id => at_id
                $data['assignments'] = $students->mapWithKeys(function($student) {
                    return [$student->id => $student->at_id];
                })->toArray();
                break;

            case 'IP':
            case 'OSH':
            case 'PPE':
                // Single lecturer for entire course
                $data['assignment_type'] = 'single_lecturer';
                $data['assignees'] = User::where('role', 'lecturer')
                    ->orderBy('name')
                    ->get();
                
                // Get course setting for single lecturer
                $courseSetting = CourseSetting::where('course_type', $courseType)->first();
                $data['current_lecturer'] = $courseSetting?->lecturer_id;
                break;

            case 'LI':
                // Individual Supervisor LI assignment per student
                $data['assignment_type'] = 'individual';
                $data['assignees'] = User::whereIn('role', ['supervisor_li', 'lecturer'])
                    ->orderBy('name')
                    ->get();
                
                // Get LI assignments from student_course_assignments table
                $studentIds = $students->pluck('id');
                $studentAssignments = StudentCourseAssignment::with('lecturer')
                    ->where('course_type', 'Industrial Training')
                    ->whereIn('student_id', $studentIds)
                    ->get()
                    ->keyBy('student_id');

                // Map student_id => lecturer_id (supervisor_li_id stored as lecturer_id in this table)
                $data['assignments'] = $studentAssignments->mapWithKeys(function($assignment) {
                    return [$assignment->student_id => $assignment->lecturer_id];
                })->toArray();
                break;

            case 'IC':
                // Individual IC assignment per student
                $data['assignment_type'] = 'individual';
                $data['assignees'] = User::where('role', 'industry')
                    ->orderBy('name')
                    ->get();
                
                // Get IC assignments (ic_id) - map student_id => ic_id
                $data['assignments'] = $students->mapWithKeys(function($student) {
                    return [$student->id => $student->ic_id];
                })->toArray();
                break;
        }

        return $data;
    }

    /**
     * Validate assignee role for course type.
     */
    private function validateAssigneeRole(User $assignee, string $courseType): void
    {
        $validRoles = match($courseType) {
            'FYP' => ['lecturer'], // AT is a lecturer
            'IP', 'OSH', 'PPE' => ['lecturer'],
            'LI' => ['supervisor_li', 'lecturer'], // Can be supervisor_li or lecturer (stored as lecturer_id)
            'IC' => ['industry'],
            default => [],
        };

        if (!in_array($assignee->role, $validRoles)) {
            abort(422, "Invalid assignee role for {$courseType}.");
        }
    }

    /**
     * Assign single lecturer for entire course (IP, OSH, PPE).
     */
    private function assignSingleLecturer(string $courseType, ?int $lecturerId): void
    {
        $courseSetting = CourseSetting::getOrCreate($courseType);
        $courseSetting->update([
            'lecturer_id' => $lecturerId,
            'updated_by' => auth()->id(),
        ]);
    }

    /**
     * Assign student to course (individual assignments).
     */
    private function assignStudentToCourse(Student $student, string $courseType, ?int $assigneeId): void
    {
        switch ($courseType) {
            case 'FYP':
                $student->update(['at_id' => $assigneeId]);
                break;

            case 'LI':
                // Update supervisor_li assignment using student_course_assignments
                StudentCourseAssignment::updateOrCreate(
                    [
                        'student_id' => $student->id,
                        'course_type' => 'Industrial Training',
                    ],
                    [
                        'lecturer_id' => $assigneeId, // supervisor_li stored as lecturer_id
                    ]
                );
                break;

            case 'IC':
                $student->update(['ic_id' => $assigneeId]);
                break;
        }
    }

    /**
     * Remove student assignment (individual assignments only).
     */
    private function removeStudentAssignment(Student $student, string $courseType): void
    {
        switch ($courseType) {
            case 'FYP':
                $student->update(['at_id' => null]);
                break;

            case 'LI':
                // Remove supervisor_li assignment
                StudentCourseAssignment::where('student_id', $student->id)
                    ->where('course_type', 'Industrial Training')
                    ->delete();
                break;

            case 'IC':
                $student->update(['ic_id' => null]);
                break;
        }
    }
}
