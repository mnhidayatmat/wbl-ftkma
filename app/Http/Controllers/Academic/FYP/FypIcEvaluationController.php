<?php

namespace App\Http\Controllers\Academic\FYP;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\AssessmentRubric;
use App\Models\Student;
use App\Models\StudentAssessmentMark;
use App\Models\StudentAssessmentRubricMark;
use App\Models\WblGroup;
use App\Traits\ChecksAssessmentWindow;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class FypIcEvaluationController extends Controller
{
    use ChecksAssessmentWindow;

    /**
     * Display the list of students for IC evaluation.
     */
    public function index(Request $request): View
    {
        // Authorization checked via middleware, but double-check here
        if (! auth()->user()->isAdmin() && ! auth()->user()->isIndustry() && ! auth()->user()->isFypCoordinator()) {
            abort(403, 'Unauthorized access.');
        }

        // Get ALL active assessments for FYP course that have IC evaluator in assessment_evaluators table
        $icAssessments = Assessment::forCourse('FYP')
            ->whereHas('evaluators', function ($query) {
                $query->where('evaluator_role', 'ic');
            })
            ->active()
            ->with(['rubrics', 'evaluators'])
            ->get();

        // Separate rubric-based and mark-based assessments
        $rubricAssessments = $icAssessments->filter(fn ($a) => in_array($a->assessment_type, ['Oral', 'Rubric']) && $a->rubrics->count() > 0);
        $markAssessments = $icAssessments->filter(fn ($a) => ! in_array($a->assessment_type, ['Oral', 'Rubric']) || $a->rubrics->count() === 0);

        // Build query for students
        $query = Student::with(['group', 'company', 'academicTutor', 'industryCoach']);

        // Admin can see all students, IC only sees assigned students
        if (auth()->user()->isIndustry() && ! auth()->user()->isAdmin()) {
            $query->where('ic_id', auth()->id());
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

        // Get all rubric marks for these students (only FYP IC assessments)
        $allRubricMarks = StudentAssessmentRubricMark::whereIn('student_id', $students->pluck('id'))
            ->whereHas('rubric.assessment', function ($q) {
                $q->where('course_code', 'FYP')
                    ->whereHas('evaluators', function ($eq) {
                        $eq->where('evaluator_role', 'ic');
                    });
            })
            ->with('rubric.assessment')
            ->get()
            ->groupBy('student_id');

        // Get all mark-based assessment marks for these students
        $allMarks = StudentAssessmentMark::whereIn('student_id', $students->pluck('id'))
            ->whereIn('assessment_id', $markAssessments->pluck('id'))
            ->get()
            ->groupBy('student_id');

        // Calculate total items (rubric questions + mark-based assessments)
        $totalRubricQuestions = $rubricAssessments->sum(fn ($a) => $a->rubrics->count());
        $totalMarkAssessments = $markAssessments->count();
        $totalItems = $totalRubricQuestions + $totalMarkAssessments;

        // Calculate evaluation status for each student
        $studentsWithStatus = $students->map(function ($student) use ($allRubricMarks, $allMarks, $markAssessments, $totalItems) {
            $studentRubricMarks = $allRubricMarks->get($student->id, collect());
            $studentMarks = $allMarks->get($student->id, collect());

            $completedRubrics = $studentRubricMarks->count();
            $completedMarks = $studentMarks->filter(fn ($m) => $m->mark !== null)->count();
            $completedCount = $completedRubrics + $completedMarks;

            $totalContribution = 0;

            // Calculate contribution from rubric marks
            foreach ($studentRubricMarks as $rubricMark) {
                $totalContribution += $rubricMark->weighted_contribution;
            }

            // Calculate contribution from mark-based assessments
            foreach ($studentMarks as $mark) {
                if ($mark->mark !== null && $mark->max_mark > 0) {
                    $assessment = $markAssessments->firstWhere('id', $mark->assessment_id);
                    if ($assessment) {
                        $totalContribution += ($mark->mark / $mark->max_mark) * $assessment->weight_percentage;
                    }
                }
            }

            // Determine status
            if ($completedCount == 0) {
                $status = 'not_started';
                $statusLabel = 'Not Started';
            } elseif ($completedCount < $totalItems) {
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
            $student->completed_questions = $completedCount;
            $student->total_questions = $totalItems;
            $student->last_updated = max($studentRubricMarks->max('updated_at'), $studentMarks->max('updated_at'));

            return $student;
        })->filter();

        // Get groups for filter dropdown
        $groups = WblGroup::orderBy('name')->get();

        // Calculate total IC weight from evaluators table
        $totalIcWeight = $icAssessments->sum(function ($assessment) {
            $icEvaluator = $assessment->evaluators->firstWhere('evaluator_role', 'ic');

            return $icEvaluator ? $icEvaluator->total_score : 0;
        });

        return view('academic.fyp.ic.index', compact(
            'studentsWithStatus',
            'groups',
            'totalIcWeight'
        ));
    }

    /**
     * Show the evaluation form for a specific student.
     */
    public function show(Student $student): View
    {
        // Check authorization - Admin and FYP Coordinator can view any, IC can view assigned students
        if (! auth()->user()->isAdmin() && ! auth()->user()->isFypCoordinator()) {
            if (auth()->user()->isIndustry()) {
                if ($student->ic_id !== auth()->id()) {
                    abort(403, 'You are not authorized to view this student. This student is assigned to a different Industry Coach.');
                }
            } else {
                abort(403, 'Unauthorized access.');
            }
        }

        // Load relationships
        $student->load('academicTutor', 'industryCoach', 'group');

        // Get ALL active assessments for FYP course that have IC evaluator in assessment_evaluators table
        $allIcAssessments = Assessment::forCourse('FYP')
            ->whereHas('evaluators', function ($query) {
                $query->where('evaluator_role', 'ic');
            })
            ->active()
            ->with(['rubrics', 'components', 'evaluators'])
            ->get();

        // Separate rubric-based and mark-based assessments
        $rubricAssessments = $allIcAssessments->filter(fn ($a) => in_array($a->assessment_type, ['Oral', 'Rubric']) && $a->rubrics->count() > 0);
        $markAssessments = $allIcAssessments->filter(fn ($a) => ! in_array($a->assessment_type, ['Oral', 'Rubric']) || $a->rubrics->count() === 0);

        // Get existing rubric marks for this student (only FYP IC assessments)
        $rubricMarks = StudentAssessmentRubricMark::where('student_id', $student->id)
            ->whereHas('rubric.assessment', function ($q) {
                $q->where('course_code', 'FYP')
                    ->whereHas('evaluators', function ($eq) {
                        $eq->where('evaluator_role', 'ic');
                    });
            })
            ->with('rubric.assessment')
            ->get()
            ->keyBy('assessment_rubric_id');

        // Get existing marks for mark-based assessments
        $marks = StudentAssessmentMark::where('student_id', $student->id)
            ->whereIn('assessment_id', $markAssessments->pluck('id'))
            ->get()
            ->keyBy('assessment_id');

        // Group mark-based assessments by phase for display
        $assessmentsByPhase = collect([
            'Mid-Term' => collect(),
            'Final' => collect(),
            'Progress' => collect(),
        ]);

        foreach ($markAssessments as $assessment) {
            $name = $assessment->assessment_name;
            if (str_contains($name, 'Mid-Term')) {
                $assessmentsByPhase['Mid-Term']->push($assessment);
            } elseif (str_contains($name, 'Final')) {
                $assessmentsByPhase['Final']->push($assessment);
            } elseif (str_contains($name, 'Progress') || str_contains($name, 'Logbook')) {
                $assessmentsByPhase['Progress']->push($assessment);
            } else {
                $assessmentsByPhase['Progress']->push($assessment);
            }
        }

        // Sort assessments within each phase: Report first, then Oral/Presentation
        foreach ($assessmentsByPhase as $phase => $phaseAssessments) {
            $assessmentsByPhase[$phase] = $phaseAssessments->sortBy(function ($a) {
                if (str_contains($a->assessment_name, 'Report')) {
                    return 1;
                }
                if (str_contains($a->assessment_name, 'Oral') || str_contains($a->assessment_name, 'Presentation')) {
                    return 2;
                }
                if (str_contains($a->assessment_name, 'Logbook')) {
                    return 3;
                }

                return 4;
            })->values();
        }

        // Remove empty phases
        $assessmentsByPhase = $assessmentsByPhase->filter(fn ($phase) => $phase->isNotEmpty());

        // Group assessments by base name (e.g., "Mid-Term Report" combines all CLOs)
        $groupedAssessments = collect([]);
        foreach ($assessmentsByPhase as $phase => $phaseAssessments) {
            $grouped = $phaseAssessments->groupBy(function ($a) {
                // Remove CLO suffix to get base name
                return preg_replace('/\s*\(CLO\d+\)\s*$/', '', $a->assessment_name);
            });
            $groupedAssessments[$phase] = $grouped;
        }

        // Group rubric assessments by CLO for display
        $assessmentsByClo = $rubricAssessments->groupBy('clo_code');

        // Calculate total contribution from rubric marks
        $rubricContribution = 0;
        foreach ($rubricMarks as $mark) {
            $rubricContribution += $mark->weighted_contribution;
        }

        // Calculate total contribution from mark-based assessments (using IC evaluator weight)
        $markContribution = 0;
        foreach ($marks as $mark) {
            if ($mark->mark !== null && $mark->max_mark > 0) {
                $assessment = $markAssessments->firstWhere('id', $mark->assessment_id);
                if ($assessment) {
                    // Use IC evaluator weight instead of full assessment weight
                    $icEvaluator = $assessment->evaluators->firstWhere('evaluator_role', 'ic');
                    $icWeight = $icEvaluator ? $icEvaluator->total_score : $assessment->weight_percentage;
                    $markContribution += ($mark->mark / $mark->max_mark) * $icWeight;
                }
            }
        }

        $totalContribution = $rubricContribution + $markContribution;

        // Get AT marks for read-only display
        $atAssessments = Assessment::forCourse('FYP')
            ->forEvaluator('at')
            ->active()
            ->get();

        $atMarks = StudentAssessmentMark::where('student_id', $student->id)
            ->whereIn('assessment_id', $atAssessments->pluck('id'))
            ->get()
            ->keyBy('assessment_id');

        $atTotalContribution = 0;
        foreach ($atMarks as $mark) {
            if ($mark->mark !== null && $mark->max_mark > 0) {
                $atTotalContribution += ($mark->mark / $mark->max_mark) * $mark->assessment->weight_percentage;
            }
        }

        // Calculate total IC weight from evaluators table
        $totalIcWeight = $allIcAssessments->sum(function ($assessment) {
            $icEvaluator = $assessment->evaluators->firstWhere('evaluator_role', 'ic');

            return $icEvaluator ? $icEvaluator->total_score : 0;
        });

        return view('academic.fyp.ic.show', compact(
            'student',
            'allIcAssessments',
            'rubricAssessments',
            'markAssessments',
            'assessmentsByPhase',
            'groupedAssessments',
            'assessmentsByClo',
            'rubricMarks',
            'marks',
            'totalContribution',
            'totalIcWeight',
            'atAssessments',
            'atMarks',
            'atTotalContribution'
        ));
    }

    /**
     * Store or update marks and rubric scores for a student.
     */
    public function store(Request $request, Student $student): RedirectResponse
    {
        // Check authorization using gate
        if (! Gate::allows('edit-ic-marks', $student)) {
            abort(403, 'You are not authorized to edit IC marks for this student.');
        }

        // Check if assessment window is open (Admin and FYP Coordinator can bypass)
        if (! auth()->user()->isAdmin() && ! auth()->user()->isFypCoordinator()) {
            $this->requireOpenWindow('ic');
        }

        // Get all IC assessments (via assessment_evaluators table)
        $allIcAssessments = Assessment::forCourse('FYP')
            ->whereHas('evaluators', function ($query) {
                $query->where('evaluator_role', 'ic');
            })
            ->active()
            ->with(['rubrics', 'components'])
            ->get();

        // Separate rubric-based and mark-based assessments
        $rubricAssessments = $allIcAssessments->filter(fn ($a) => in_array($a->assessment_type, ['Oral', 'Rubric']) && $a->rubrics->count() > 0);
        $markAssessments = $allIcAssessments->filter(fn ($a) => ! in_array($a->assessment_type, ['Oral', 'Rubric']) || $a->rubrics->count() === 0);

        $allRubricIds = $rubricAssessments->flatMap->rubrics->pluck('id');
        $allMarkAssessmentIds = $markAssessments->pluck('id');

        // Validate input
        $validated = $request->validate([
            'rubric_scores' => ['nullable', 'array'],
            'rubric_scores.*' => ['nullable', 'integer', 'min:1'],
            'marks' => ['nullable', 'array'],
            'marks.*' => ['nullable', 'numeric', 'min:0'],
            'max_marks' => ['nullable', 'array'],
            'max_marks.*' => ['nullable', 'numeric', 'min:0'],
            'component_marks' => ['nullable', 'array'],
            'component_marks.*' => ['nullable', 'integer', 'min:1', 'max:5'],
            'component_remarks' => ['nullable', 'array'],
            'component_remarks.*' => ['nullable', 'string', 'max:1000'],
            'assessment_id' => ['nullable', 'exists:assessments,id'],
            'remarks' => ['nullable', 'array'],
            'remarks.*' => ['nullable', 'string', 'max:1000'],
        ]);

        // Handle component-based marking (rubric style)
        if ($request->has('component_marks') && $request->filled('assessment_id')) {
            $assessmentId = $validated['assessment_id'];
            $assessment = Assessment::with('components')->findOrFail($assessmentId);

            // Validate assessment belongs to FYP and has IC evaluator
            if ($assessment->course_code !== 'FYP') {
                return redirect()->back()
                    ->with('error', 'Invalid assessment.')
                    ->withInput();
            }

            // Check IC evaluator exists for this assessment
            $hasIcEvaluator = $assessment->evaluators()->where('evaluator_role', 'ic')->exists();
            if (! $hasIcEvaluator) {
                return redirect()->back()
                    ->with('error', 'This assessment is not assigned to IC evaluator.')
                    ->withInput();
            }

            $totalWeightedScore = 0;
            $totalWeight = 0;

            foreach ($validated['component_marks'] as $componentId => $rubricScore) {
                // Handle temporary component IDs (for components without database entries)
                if (str_starts_with((string) $componentId, 'temp_')) {
                    continue; // Skip temporary components
                }

                $component = $assessment->components->find($componentId);
                if (! $component) {
                    continue;
                }

                $componentRemarks = $validated['component_remarks'][$componentId] ?? null;

                // Save component mark
                \App\Models\StudentAssessmentComponentMark::updateOrCreate(
                    [
                        'student_id' => $student->id,
                        'assessment_id' => $assessment->id,
                        'component_id' => $componentId,
                    ],
                    [
                        'rubric_score' => $rubricScore,
                        'remarks' => $componentRemarks,
                        'evaluated_by' => auth()->id(),
                    ]
                );

                // Calculate weighted contribution
                $componentWeight = $component->weight_percentage ?? 0;
                $normalizedScore = ($rubricScore / 5) * 100; // Convert 1-5 to percentage
                $weightedScore = ($normalizedScore / 100) * $componentWeight;

                $totalWeightedScore += $weightedScore;
                $totalWeight += $componentWeight;
            }

            // Calculate overall assessment mark (average of all components, normalized to 0-5 scale)
            $overallMark = $totalWeight > 0 ? ($totalWeightedScore / $totalWeight) * 5 : null;
            $overallMark = $overallMark ? round($overallMark, 2) : null;

            // Get overall remarks
            $overallRemarks = $validated['remarks'][$assessment->id] ?? null;

            // Save overall assessment mark
            StudentAssessmentMark::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'assessment_id' => $assessment->id,
                ],
                [
                    'mark' => $overallMark,
                    'max_mark' => 5,
                    'remarks' => $overallRemarks,
                    'evaluated_by' => auth()->id(),
                ]
            );

            return redirect()->route('academic.fyp.ic.show', $student)
                ->with('success', "{$assessment->assessment_name} evaluation saved successfully.")
                ->with('last_saved', now()->format('H:i:s'));
        }

        // Save rubric scores
        if (! empty($validated['rubric_scores'])) {
            foreach ($validated['rubric_scores'] as $rubricId => $score) {
                if ($score === null) {
                    continue;
                }

                // Verify rubric belongs to IC assessment
                if (! $allRubricIds->contains($rubricId)) {
                    continue;
                }

                $rubric = AssessmentRubric::findOrFail($rubricId);

                // Validate score is within rubric range
                if ($score < $rubric->rubric_min || $score > $rubric->rubric_max) {
                    return redirect()->back()
                        ->with('error', "Rubric score for {$rubric->question_title} must be between {$rubric->rubric_min} and {$rubric->rubric_max}.")
                        ->withInput();
                }

                StudentAssessmentRubricMark::updateOrCreate(
                    [
                        'student_id' => $student->id,
                        'assessment_rubric_id' => $rubricId,
                    ],
                    [
                        'rubric_score' => $score,
                        'evaluated_by' => auth()->id(),
                    ]
                );
            }
        }

        // Save mark-based assessment scores
        if (! empty($validated['marks'])) {
            foreach ($validated['marks'] as $assessmentId => $mark) {
                // Verify assessment belongs to IC assessments
                if (! $allIcAssessments->pluck('id')->contains($assessmentId)) {
                    continue;
                }

                $assessment = Assessment::findOrFail($assessmentId);
                $maxMark = $validated['max_marks'][$assessmentId] ?? 100;
                $remarks = $validated['remarks'][$assessmentId] ?? null;

                // Validate mark doesn't exceed max_mark
                if ($mark !== null && $maxMark > 0 && $mark > $maxMark) {
                    return redirect()->back()
                        ->with('error', "Mark for {$assessment->assessment_name} cannot exceed {$maxMark}.")
                        ->withInput();
                }

                StudentAssessmentMark::updateOrCreate(
                    [
                        'student_id' => $student->id,
                        'assessment_id' => $assessmentId,
                    ],
                    [
                        'mark' => $mark ?: null,
                        'max_mark' => $maxMark,
                        'remarks' => $remarks,
                        'evaluated_by' => auth()->id(),
                    ]
                );
            }
        }

        return redirect()->route('academic.fyp.ic.show', $student)
            ->with('success', 'Evaluation saved successfully.')
            ->with('last_saved', now()->format('H:i:s'));
    }

    /**
     * Show the rubric evaluation form for a specific assessment.
     */
    public function rubric(Student $student, Assessment $assessment): View
    {
        // Check authorization
        if (! auth()->user()->isAdmin()) {
            if (auth()->user()->isIndustry()) {
                if ($student->ic_id !== auth()->id()) {
                    abort(403, 'You are not authorized to view this student.');
                }
            } else {
                abort(403, 'Unauthorized access.');
            }
        }

        // Verify assessment belongs to FYP IC
        if ($assessment->course_code !== 'FYP' || $assessment->evaluator_role !== 'ic') {
            abort(404, 'Assessment not found.');
        }

        // Load student relationships
        $student->load('academicTutor', 'industryCoach', 'group', 'company');

        // Load rubrics for this assessment
        $assessment->load('rubrics');

        // Get existing rubric marks
        $rubricMarks = StudentAssessmentRubricMark::where('student_id', $student->id)
            ->whereIn('assessment_rubric_id', $assessment->rubrics->pluck('id'))
            ->get()
            ->keyBy('assessment_rubric_id');

        // Calculate total contribution
        $totalContribution = 0;
        foreach ($rubricMarks as $mark) {
            $totalContribution += $mark->weighted_contribution;
        }

        $canEdit = Gate::allows('edit-ic-marks', $student);

        return view('academic.fyp.ic.rubric', compact(
            'student',
            'assessment',
            'rubricMarks',
            'totalContribution',
            'canEdit'
        ));
    }

    /**
     * Store rubric scores for a specific assessment.
     */
    public function storeRubric(Request $request, Student $student, Assessment $assessment): RedirectResponse
    {
        // Check authorization
        if (! Gate::allows('edit-ic-marks', $student)) {
            abort(403, 'You are not authorized to edit IC marks for this student.');
        }

        // Check if assessment window is open (Admin and FYP Coordinator can bypass)
        if (! auth()->user()->isAdmin() && ! auth()->user()->isFypCoordinator()) {
            $this->requireOpenWindow('ic');
        }

        // Verify assessment belongs to FYP IC
        if ($assessment->course_code !== 'FYP' || $assessment->evaluator_role !== 'ic') {
            abort(404, 'Assessment not found.');
        }

        $validated = $request->validate([
            'rubric_scores' => ['required', 'array'],
            'rubric_scores.*' => ['nullable', 'integer', 'min:1'],
        ]);

        $rubricIds = $assessment->rubrics->pluck('id');

        foreach ($validated['rubric_scores'] as $rubricId => $score) {
            if ($score === null) {
                continue;
            }

            // Verify rubric belongs to this assessment
            if (! $rubricIds->contains($rubricId)) {
                continue;
            }

            $rubric = AssessmentRubric::findOrFail($rubricId);

            // Validate score is within range
            if ($score < $rubric->rubric_min || $score > $rubric->rubric_max) {
                return redirect()->back()
                    ->with('error', "Score for {$rubric->question_title} must be between {$rubric->rubric_min} and {$rubric->rubric_max}.")
                    ->withInput();
            }

            StudentAssessmentRubricMark::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'assessment_rubric_id' => $rubricId,
                ],
                [
                    'rubric_score' => $score,
                    'evaluated_by' => auth()->id(),
                ]
            );
        }

        return redirect()->route('academic.fyp.ic.show', $student)
            ->with('success', "{$assessment->assessment_name} rubrics saved successfully.");
    }
}
