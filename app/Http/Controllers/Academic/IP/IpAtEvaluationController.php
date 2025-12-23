<?php

namespace App\Http\Controllers\Academic\IP;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Student;
use App\Models\StudentAssessmentMark;
use App\Models\WblGroup;
use App\Traits\ChecksAssessmentWindow;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class IpAtEvaluationController extends Controller
{
    use ChecksAssessmentWindow;

    /**
     * Display the list of students for AT evaluation.
     */
    public function index(Request $request): View
    {
        // Authorization: Admin and AT (Academic Tutor) can access
        if (! auth()->user()->isAdmin() && ! auth()->user()->isLecturer()) {
            abort(403, 'Unauthorized access.');
        }

        // Get active assessments for IP course with AT evaluator role
        $atAssessments = Assessment::forCourse('IP')
            ->forEvaluator('at')
            ->active()
            ->get();

        $totalWeight = $atAssessments->sum('weight_percentage');

        // Build query for students
        $query = Student::with(['group', 'company', 'academicTutor', 'industryCoach']);

        // Admin can see all students, AT only sees assigned students
        if (auth()->user()->isLecturer() && ! auth()->user()->isAdmin()) {
            $query->where('at_id', auth()->id());
        }

        // Filter by active groups only
        $query->inActiveGroups();

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
            ->whereIn('assessment_id', $atAssessments->pluck('id'))
            ->get()
            ->groupBy('student_id');

        // Calculate evaluation status for each student
        $studentsWithStatus = $students->map(function ($student) use ($atAssessments, $allMarks) {
            $studentMarks = $allMarks->get($student->id, collect());
            $marksByAssessment = $studentMarks->keyBy('assessment_id');

            $totalMarks = 0;
            $completedCount = 0;
            $totalContribution = 0;

            foreach ($atAssessments as $assessment) {
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
            } elseif ($completedCount < $atAssessments->count()) {
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
            $student->total_assessments = $atAssessments->count();
            $student->last_updated = $studentMarks->max('updated_at');

            return $student;
        })->filter();

        // Get groups for filter dropdown (active groups only)
        $groups = WblGroup::where('status', 'ACTIVE')->orderBy('name')->get();

        return view('academic.ip.at.index', compact(
            'studentsWithStatus',
            'atAssessments',
            'totalWeight',
            'groups'
        ));
    }

    /**
     * Show the evaluation form for a specific student.
     */
    /**
     * Show the evaluation form for a specific student.
     */
    public function show(Student $student): View
    {
        // Check authorization - all authenticated users can view, but only assigned AT can edit
        if (! Gate::allows('view', $student)) {
            abort(403, 'You are not authorized to view this student.');
        }

        // Load relationships
        $student->load('academicTutor', 'industryCoach', 'group');

        // Get active assessments for IP course with AT evaluator role
        $atAssessments = Assessment::forCourse('IP')
            ->forEvaluator('at')
            ->active()
            ->with('components')
            ->orderBy('clo_code')
            ->orderBy('assessment_name')
            ->get();

        // Get existing marks for this student
        $marks = StudentAssessmentMark::where('student_id', $student->id)
            ->whereIn('assessment_id', $atAssessments->pluck('id'))
            ->get()
            ->keyBy('assessment_id');

        // Get existing component marks for this student
        $componentMarks = \App\Models\StudentAssessmentComponentMark::where('student_id', $student->id)
            ->whereIn('assessment_id', $atAssessments->pluck('id'))
            ->get();

        // Group assessments by CLO
        $assessmentsByClo = $atAssessments->groupBy('clo_code');

        // Calculate total contribution
        $totalContribution = 0;
        foreach ($marks as $mark) {
            if ($mark->mark !== null && $mark->max_mark > 0) {
                $totalContribution += ($mark->mark / $mark->max_mark) * $mark->assessment->weight_percentage;
            }
        }

        // Get IC marks for read-only display
        $icAssessments = Assessment::forCourse('IP')
            ->forEvaluator('ic')
            ->active()
            ->orderBy('clo_code')
            ->get();

        $icMarks = StudentAssessmentMark::where('student_id', $student->id)
            ->whereIn('assessment_id', $icAssessments->pluck('id'))
            ->get()
            ->keyBy('assessment_id');

        $icTotalContribution = 0;
        foreach ($icMarks as $mark) {
            if ($mark->mark !== null && $mark->max_mark > 0) {
                $icTotalContribution += ($mark->mark / $mark->max_mark) * $mark->assessment->weight_percentage;
            }
        }

        return view('academic.ip.at.show', compact(
            'student',
            'atAssessments',
            'assessmentsByClo',
            'marks',
            'componentMarks',
            'totalContribution',
            'icAssessments',
            'icMarks',
            'icTotalContribution'
        ));
    }

    /**
     * Store or update marks for a student.
     */
    public function store(Request $request, Student $student): RedirectResponse
    {
        // Check authorization using gate
        if (! Gate::allows('edit-at-marks', $student)) {
            $student->load('academicTutor');
            $assignedTo = $student->academicTutor ? $student->academicTutor->name.' (ID: '.$student->academicTutor->id.')' : 'No one';
            $currentUser = auth()->user()->name.' (ID: '.auth()->user()->id.')';
            $studentAtId = $student->at_id ?? 'NULL';
            abort(403, "You are not authorized to edit AT marks for this student. You are logged in as: {$currentUser}. This student ({$student->name}) is currently assigned to: {$assignedTo} (Student's at_id: {$studentAtId}). Please make sure you are logged in as the correct Academic Tutor, or contact an administrator to assign this student to you via the 'Assign Students' page.");
        }

        // Check if assessment window is open (Admin can bypass)
        if (! auth()->user()->isAdmin()) {
            $this->requireOpenWindow('at', 'IP');
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

        // Validate assessment belongs to IP and AT role
        if ($assessment->course_code !== 'IP' || $assessment->evaluator_role !== 'at') {
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

        return redirect()->route('academic.ip.at.show', $student)
            ->with('success', 'Assessment marks saved successfully.');
    }
}
