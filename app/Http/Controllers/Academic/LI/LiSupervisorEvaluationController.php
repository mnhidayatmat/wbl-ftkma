<?php

namespace App\Http\Controllers\Academic\LI;

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

class LiSupervisorEvaluationController extends Controller
{
    use ChecksAssessmentWindow;

    /**
     * Display the list of students for Supervisor evaluation.
     */
    public function index(Request $request): View
    {
        // Only Admin, LI Coordinator, and Supervisor LI can access Supervisor evaluation
        if (! auth()->user()->isAdmin() && ! auth()->user()->isLiCoordinator() && ! auth()->user()->isSupervisorLi()) {
            abort(403, 'Unauthorized access.');
        }

        // Get active assessments for LI course with lecturer evaluator role (Supervisor LI)
        $assessments = Assessment::forCourse('LI')
            ->forEvaluator('lecturer')
            ->active()
            ->get();

        $totalWeight = $assessments->sum('weight_percentage');

        // Build query for students
        $query = Student::with(['group', 'company', 'academicTutor', 'industryCoach']);

        // Admin and LI Coordinator can see all students, Supervisor only sees assigned students
        if (auth()->user()->isSupervisorLi() && ! auth()->user()->isAdmin() && ! auth()->user()->isLiCoordinator()) {
            $studentIds = \App\Models\StudentCourseAssignment::where('lecturer_id', auth()->id())
                ->where('course_type', 'Industrial Training')
                ->pluck('student_id');
            $query->whereIn('id', $studentIds);
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

        return view('academic.li.lecturer.index', compact(
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
        // Check authorization: Admin or assigned Supervisor using Gate
        if (! Gate::allows('edit-supervisor-li-marks', $student)) {
            abort(403, 'You are not authorized to edit Supervisor marks for this student.');
        }

        // Load relationships
        $student->load('academicTutor', 'industryCoach', 'group', 'company');

        // Get active assessments for LI course with lecturer evaluator role (Supervisor LI)
        $assessments = Assessment::forCourse('LI')
            ->forEvaluator('lecturer')
            ->active()
            ->with(['components', 'rubrics'])
            ->orderBy('clo_code')
            ->orderBy('assessment_name')
            ->get();

        // Group assessments by CLO
        $assessmentsByClo = $assessments->groupBy('clo_code');

        // Get existing marks for this student
        $marks = StudentAssessmentMark::where('student_id', $student->id)
            ->whereIn('assessment_id', $assessments->pluck('id'))
            ->get()
            ->keyBy('assessment_id');

        // Get existing component marks for this student
        $componentMarks = \App\Models\StudentAssessmentComponentMark::where('student_id', $student->id)
            ->whereIn('assessment_id', $assessments->pluck('id'))
            ->get();

        // Get existing rubric marks for this student
        $rubricMarks = \App\Models\StudentAssessmentRubricMark::where('student_id', $student->id)
            ->whereIn('assessment_rubric_id', $assessments->flatMap->rubrics->pluck('id'))
            ->get()
            ->keyBy('assessment_rubric_id');

        // Calculate total contribution
        $totalContribution = 0;
        foreach ($marks as $mark) {
            if ($mark->mark !== null && $mark->max_mark > 0) {
                // Find weight from assessment
                $assessment = $assessments->find($mark->assessment_id);
                $weight = $assessment ? $assessment->weight_percentage : 0;
                $totalContribution += ($mark->mark / $mark->max_mark) * $weight;
            }
        }

        // Get IC marks for read-only display
        $icAssessments = Assessment::forCourse('LI')
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
                $assessment = $icAssessments->find($mark->assessment_id);
                $weight = $assessment ? $assessment->weight_percentage : 0;
                $icTotalContribution += ($mark->mark / $mark->max_mark) * $weight;
            }
        }

        return view('academic.li.lecturer.show', compact(
            'student',
            'assessments',
            'assessmentsByClo',
            'marks',
            'componentMarks',
            'rubricMarks',
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
        // Check authorization using Gate
        if (! Gate::allows('edit-supervisor-li-marks', $student)) {
            abort(403, 'You are not authorized to edit Supervisor marks for this student.');
        }

        // Check if assessment window is open (Admin and LI Coordinator can bypass)
        if (! auth()->user()->isAdmin() && ! auth()->user()->isLiCoordinator()) {
            $this->requireOpenWindow('supervisor');
        }

        $validated = $request->validate([
            'assessment_id' => ['required', 'exists:assessments,id'],
            'component_marks' => ['nullable', 'array'],
            'component_marks.*' => ['nullable', 'integer', 'min:1', 'max:5'],
            'component_remarks' => ['nullable', 'array'],
            'component_remarks.*' => ['nullable', 'string', 'max:1000'],
            'rubric_scores' => ['nullable', 'array'],
            'rubric_scores.*' => ['nullable', 'integer'],
            'mark' => ['nullable', 'numeric', 'min:0'],
            'max_mark' => ['nullable', 'numeric', 'min:0.1'],
            'remarks' => ['nullable', 'string', 'max:1000'],
        ]);

        $assessment = Assessment::with(['components', 'rubrics'])->findOrFail($validated['assessment_id']);

        // Handle component-based marking (1-5 scale)
        if ($assessment->components->isNotEmpty() && isset($validated['component_marks'])) {
            $totalWeightedScore = 0;
            $totalWeight = 0;

            foreach ($validated['component_marks'] as $componentId => $score) {
                if ($score === null) {
                    continue;
                }

                $component = $assessment->components->find($componentId);
                if (! $component) {
                    continue;
                }

                $cRemarks = $validated['component_remarks'][$componentId] ?? null;

                \App\Models\StudentAssessmentComponentMark::updateOrCreate(
                    [
                        'student_id' => $student->id,
                        'assessment_id' => $assessment->id,
                        'component_id' => $componentId,
                    ],
                    [
                        'rubric_score' => $score,
                        'remarks' => $cRemarks,
                        'evaluated_by' => auth()->id(),
                    ]
                );

                $normalizedScore = $score / 5;
                $totalWeightedScore += ($normalizedScore * $component->weight_percentage);
                $totalWeight += $component->weight_percentage;
            }

            $overallMark = $totalWeight > 0 ? ($totalWeightedScore / $totalWeight) * 5 : 0;

            StudentAssessmentMark::updateOrCreate(
                ['student_id' => $student->id, 'assessment_id' => $assessment->id],
                [
                    'mark' => $overallMark,
                    'max_mark' => 5,
                    'remarks' => $validated['remarks'] ?? null,
                    'evaluated_by' => auth()->id(),
                ]
            );

        } elseif ($assessment->rubrics->isNotEmpty() && isset($validated['rubric_scores'])) {
            // Handle legacy rubric marking
            foreach ($validated['rubric_scores'] as $rubricId => $score) {
                if ($score === null) {
                    continue;
                }

                \App\Models\StudentAssessmentRubricMark::updateOrCreate(
                    ['student_id' => $student->id, 'assessment_rubric_id' => $rubricId],
                    ['rubric_score' => $score, 'evaluated_by' => auth()->id()]
                );
            }

            // For legacy rubrics, often the total is calculated elsewhere or just stored as rubric marks
            // But we can store a dummy/total mark if needed.

        } elseif (isset($validated['mark'])) {
            // Handle simple mark-based
            StudentAssessmentMark::updateOrCreate(
                ['student_id' => $student->id, 'assessment_id' => $assessment->id],
                [
                    'mark' => $validated['mark'],
                    'max_mark' => $validated['max_mark'] ?? 100,
                    'remarks' => $validated['remarks'] ?? null,
                    'evaluated_by' => auth()->id(),
                ]
            );
        }

        return redirect()->route('academic.li.lecturer.show', $student)
            ->with('success', 'Marks saved successfully.');
    }
}
