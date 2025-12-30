<?php

namespace App\Http\Controllers\Academic\PPE;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\CourseSetting;
use App\Models\Student;
use App\Models\StudentAssessmentMark;
use App\Models\WblGroup;
use App\Traits\ChecksAssessmentWindow;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class PpeAtEvaluationController extends Controller
{
    use ChecksAssessmentWindow;

    /**
     * Display the list of students for Lecturer evaluation.
     * Note: In PPE context, "AT" refers to Lecturer (Course Lecturer) evaluation.
     */
    public function index(Request $request): View
    {
        // Only Admin, Lecturer, and PPE Coordinator can access Lecturer evaluation
        if (! auth()->user()->isAdmin() && ! auth()->user()->isLecturer() && ! auth()->user()->isPpeCoordinator()) {
            abort(403, 'Unauthorized access.');
        }

        // Get active assessments for PPE course with lecturer evaluator role
        $assessments = Assessment::forCourse('PPE')
            ->forEvaluator('lecturer')
            ->active()
            ->get();

        $totalWeight = $assessments->sum('weight_percentage');

        // Build query for students
        $query = Student::with(['group', 'company', 'academicTutor', 'industryCoach']);

        // Admin and PPE Coordinator can see all students, Lecturer sees only students if they are the assigned PPE lecturer
        if (auth()->user()->isLecturer() && ! auth()->user()->isAdmin() && ! auth()->user()->isPpeCoordinator()) {
            // PPE uses single lecturer from course_settings
            $ppeSetting = CourseSetting::where('course_type', 'PPE')->first();
            if ($ppeSetting && $ppeSetting->lecturer_id === auth()->id()) {
                // This lecturer is assigned to PPE, show all students
                // No additional filter needed
            } else {
                // This lecturer is not assigned to PPE, show no students
                $query->whereRaw('1 = 0'); // Force empty result
            }
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

        // Get all students
        $students = $query->orderBy('name')->get();

        // Get all marks for these students
        $allMarks = StudentAssessmentMark::whereIn('student_id', $students->pluck('id'))
            ->whereIn('assessment_id', $assessments->pluck('id'))
            ->get()
            ->groupBy('student_id');

        // Calculate evaluation status for each student
        $studentsWithStatus = $students->map(function ($student) use ($assessments, $allMarks) {
            $studentMarks = $allMarks->get($student->id, collect());
            $marksByAssessment = $studentMarks->keyBy('assessment_id');

            $totalMarks = 0;
            $completedCount = 0;
            $totalContribution = 0;

            foreach ($assessments as $assessment) {
                $mark = $marksByAssessment->get($assessment->id);
                if ($mark && $mark->mark !== null) {
                    $completedCount++;
                    if ($mark->max_mark > 0) {
                        $totalContribution += ($mark->mark / $mark->max_mark) * $assessment->weight_percentage;
                    }
                }
            }

            // Determine status
            if ($completedCount == 0) {
                $status = 'not_started';
                $statusLabel = 'Not Started';
            } elseif ($completedCount < $assessments->count()) {
                $status = 'in_progress';
                $statusLabel = 'In Progress';
            } else {
                $status = 'completed';
                $statusLabel = 'Completed';
            }

            // Apply status filter
            $statusFilter = request('status');
            if ($statusFilter && $status !== $statusFilter) {
                return null;
            }

            $student->evaluation_status = $status;
            $student->evaluation_status_label = $statusLabel;
            $student->total_contribution = $totalContribution;
            $student->completed_assessments = $completedCount;
            $student->total_assessments = $assessments->count();
            $student->last_updated = $studentMarks->max('updated_at');

            return $student;
        })->filter();

        // Get groups for filter dropdown
        $groups = WblGroup::orderBy('name')->get();

        return view('academic.ppe.lecturer.index', compact(
            'studentsWithStatus',
            'assessments',
            'totalWeight',
            'groups'
        ));
    }

    /**
     * Show the evaluation form for a specific student.
     */
    public function show(Student $student): View
    {
        // Load relationships for display
        $student->load('academicTutor', 'industryCoach', 'group');

        // Check authorization: Admin, PPE Coordinator (view only), or assigned PPE lecturer
        if (! auth()->user()->isAdmin() && ! auth()->user()->isPpeCoordinator()) {
            if (auth()->user()->isLecturer()) {
                // PPE uses single lecturer from course_settings
                $ppeSetting = CourseSetting::where('course_type', 'PPE')->first();
                if (! $ppeSetting || $ppeSetting->lecturer_id !== auth()->id()) {
                    abort(403, 'You are not authorized to edit Lecturer marks for PPE. You are not the assigned PPE lecturer. Please contact an administrator.');
                }
            } else {
                abort(403, 'Unauthorized access.');
            }
        }

        // Get active assessments for PPE course with lecturer evaluator role
        $assessments = Assessment::forCourse('PPE')
            ->forEvaluator('lecturer')
            ->active()
            ->with('components')
            ->orderBy('clo_code')
            ->orderBy('assessment_name')
            ->get();

        // Get existing marks for this student
        $marks = StudentAssessmentMark::where('student_id', $student->id)
            ->whereIn('assessment_id', $assessments->pluck('id'))
            ->get()
            ->keyBy('assessment_id');

        // Get existing component marks for this student
        $componentMarks = \App\Models\StudentAssessmentComponentMark::where('student_id', $student->id)
            ->whereIn('assessment_id', $assessments->pluck('id'))
            ->get();

        // Group assessments by CLO
        $assessmentsByClo = $assessments->groupBy('clo_code');

        // Calculate total contribution
        $totalContribution = 0;
        foreach ($marks as $mark) {
            if ($mark->mark !== null && $mark->max_mark > 0) {
                $totalContribution += ($mark->mark / $mark->max_mark) * $mark->assessment->weight_percentage;
            }
        }

        return view('academic.ppe.lecturer.show', compact(
            'student',
            'assessments',
            'assessmentsByClo',
            'marks',
            'componentMarks',
            'totalContribution'
        ));
    }

    /**
     * Store or update marks for a student.
     */
    public function store(Request $request, Student $student): RedirectResponse
    {
        // Check authorization using gate (this handles all role checks)
        if (! Gate::allows('edit-at-marks', $student)) {
            $student->load('academicTutor');
            $assignedTo = $student->academicTutor ? $student->academicTutor->name.' (ID: '.$student->academicTutor->id.')' : 'No one';
            $currentUser = auth()->user()->name.' (ID: '.auth()->user()->id.')';
            $studentAtId = $student->at_id ?? 'NULL';
            abort(403, "You are not authorized to edit Lecturer marks for this student. You are logged in as: {$currentUser}. This student ({$student->name}) is currently assigned to: {$assignedTo} (Student's at_id: {$studentAtId}). Please make sure you are logged in as the correct Lecturer, or contact an administrator to assign this student to you via the 'Assign Students' page.");
        }

        // Check if assessment window is open (Admin can bypass)
        if (! auth()->user()->isAdmin()) {
            $this->requireOpenWindow('lecturer');
        }

        $validated = $request->validate([
            'assessment_id' => ['required', 'exists:assessments,id'],
            'component_marks' => ['nullable', 'array'],
            'component_marks.*' => ['nullable', 'integer', 'min:1', 'max:5'],
            'component_remarks' => ['nullable', 'array'],
            'component_remarks.*' => ['nullable', 'string', 'max:1000'],
            'remarks' => ['nullable', 'string', 'max:1000'],
        ]);

        $assessmentId = $validated['assessment_id'];
        $assessment = Assessment::with('components')->findOrFail($assessmentId);

        // Validate assessment belongs to PPE and lecturer role
        if ($assessment->course_code !== 'PPE' || $assessment->evaluator_role !== 'lecturer') {
            return redirect()->back()->with('error', 'Invalid assessment.');
        }

        // Handle component-based marking
        if ($assessment->components->isNotEmpty() && isset($validated['component_marks'])) {
            $totalWeightedScore = 0;
            $totalWeight = 0;

            foreach ($validated['component_marks'] as $componentId => $score) {
                $component = $assessment->components->find($componentId);
                if (! $component) {
                    continue;
                }

                $remarks = $validated['component_remarks'][$componentId] ?? null;

                // Save component mark
                \App\Models\StudentAssessmentComponentMark::updateOrCreate(
                    [
                        'student_id' => $student->id,
                        'assessment_id' => $assessment->id,
                        'component_id' => $componentId,
                    ],
                    [
                        'rubric_score' => $score,
                        'remarks' => $remarks,
                        'evaluated_by' => auth()->id(),
                    ]
                );

                // Calculate weighted contribution
                // Score is 1-5. Normalized = (Score / 5)
                // Contribution = Normalized * Weight
                $normalizedScore = $score / 5;
                $weightedScore = $normalizedScore * $component->weight_percentage;

                $totalWeightedScore += $weightedScore;
                $totalWeight += $component->weight_percentage;
            }

            // Calculate overall mark for this assessment (0-5 scale)
            $overallMark = $totalWeight > 0 ? ($totalWeightedScore / $totalWeight) * 5 : 0;

            // Save overall mark
            StudentAssessmentMark::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'assessment_id' => $assessment->id,
                ],
                [
                    'mark' => $overallMark,
                    'max_mark' => 5, // Standardized 0-5 scale for rubric assessments
                    'remarks' => $validated['remarks'] ?? null,
                    'evaluated_by' => auth()->id(),
                ]
            );

        } else {
            return redirect()->back()->with('error', 'This assessment does not have components configured for rubric grading.');
        }

        return redirect()->route('academic.ppe.lecturer.show', $student)
            ->with('success', 'Assessment marks saved successfully.');
    }
}
