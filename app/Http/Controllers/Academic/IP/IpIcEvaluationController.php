<?php

namespace App\Http\Controllers\Academic\IP;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Student;
use App\Models\StudentAssessmentMark;
use App\Models\StudentAssessmentRubricMark;
use App\Models\WblGroup;
use App\Traits\ChecksAssessmentWindow;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class IpIcEvaluationController extends Controller
{
    use ChecksAssessmentWindow;

    /**
     * Display the list of students for IC evaluation.
     */
    public function index(Request $request): View
    {
        // Authorization checked via middleware, but double-check here
        if (! auth()->user()->isAdmin() && ! auth()->user()->isIndustry()) {
            abort(403, 'Unauthorized access.');
        }

        // Get ALL active assessments for IP course that have IC evaluator in assessment_evaluators table
        $icAssessments = Assessment::forCourse('IP')
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

        // Get all rubric marks for these students (only IP IC assessments)
        $allRubricMarks = StudentAssessmentRubricMark::whereIn('student_id', $students->pluck('id'))
            ->whereHas('rubric.assessment', function ($q) {
                $q->where('course_code', 'IP')
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
                        // Use IC evaluator weight instead of full assessment weight if exists
                        $icEvaluator = $assessment->evaluators->firstWhere('evaluator_role', 'ic');
                        $icWeight = $icEvaluator ? $icEvaluator->total_score : $assessment->weight_percentage;
                        $totalContribution += ($mark->mark / $mark->max_mark) * $icWeight;
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

        return view('academic.ip.ic.index', compact(
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
        // Check authorization - Admin can view any, IC can view assigned students
        if (! auth()->user()->isAdmin()) {
            if (auth()->user()->isIndustry()) {
                if ($student->ic_id !== auth()->id()) {
                    abort(403, 'You are not authorized to view this student. This student is assigned to a different Industry Coach.');
                }
            } else {
                abort(403, 'Unauthorized access.');
            }
        }

        // Load relationships
        $student->load('academicTutor', 'industryCoach', 'group', 'company');

        // Get ALL active assessments for IP course that have IC evaluator role
        // Eager load rubrics and components
        $allIcAssessments = Assessment::forCourse('IP')
            ->whereHas('evaluators', function ($query) {
                $query->where('evaluator_role', 'ic');
            })
            ->active()
            ->with(['rubrics', 'components', 'evaluators'])
            ->orderBy('clo_code')
            ->orderBy('assessment_name')
            ->get();

        // Get existing rubric marks for this student (only IP IC assessments)
        $rubricMarks = StudentAssessmentRubricMark::where('student_id', $student->id)
            ->whereHas('rubric.assessment', function ($q) {
                $q->where('course_code', 'IP')
                    ->whereHas('evaluators', function ($eq) {
                        $eq->where('evaluator_role', 'ic');
                    });
            })
            ->with('rubric.assessment')
            ->get()
            ->keyBy('assessment_rubric_id');

        // Get existing marks for assessments
        $marks = StudentAssessmentMark::where('student_id', $student->id)
            ->whereIn('assessment_id', $allIcAssessments->pluck('id'))
            ->get()
            ->keyBy('assessment_id');

        // Get existing component marks
        $componentMarks = \App\Models\StudentAssessmentComponentMark::where('student_id', $student->id)
            ->whereIn('assessment_id', $allIcAssessments->pluck('id'))
            ->get();

        // Group ALL assessments by CLO for display
        $assessmentsByClo = $allIcAssessments->groupBy('clo_code');

        // Calculate total contribution from rubric marks
        $rubricContribution = 0;
        foreach ($rubricMarks as $mark) {
            $rubricContribution += $mark->weighted_contribution;
        }

        // Calculate total contribution from mark-based assessments (including component-based ones)
        $markContribution = 0;
        foreach ($marks as $mark) {
            if ($mark->mark !== null && $mark->max_mark > 0) {
                $assessment = $allIcAssessments->firstWhere('id', $mark->assessment_id);
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
        $atAssessments = Assessment::forCourse('IP')
            ->whereHas('evaluators', function ($query) {
                $query->where('evaluator_role', 'at');
            })
            ->active()
            ->get();

        $atMarks = StudentAssessmentMark::where('student_id', $student->id)
            ->whereIn('assessment_id', $atAssessments->pluck('id'))
            ->get()
            ->keyBy('assessment_id');

        $atTotalContribution = 0;
        foreach ($atMarks as $mark) {
            if ($mark->mark !== null && $mark->max_mark > 0) {
                $assessment = $atAssessments->firstWhere('id', $mark->assessment_id);
                if ($assessment) {
                    $atEvaluator = $assessment->evaluators->firstWhere('evaluator_role', 'at');
                    $atWeight = $atEvaluator ? $atEvaluator->total_score : $assessment->weight_percentage;
                    $atTotalContribution += ($mark->mark / $mark->max_mark) * $atWeight;
                }
            }
        }

        // Calculate total possible IC weight
        $totalIcWeight = $allIcAssessments->sum(function ($assessment) {
            $icEvaluator = $assessment->evaluators->firstWhere('evaluator_role', 'ic');

            return $icEvaluator ? $icEvaluator->total_score : 0;
        });

        return view('academic.ip.ic.show', compact(
            'student',
            'allIcAssessments',
            'assessmentsByClo',
            'rubricMarks',
            'marks',
            'componentMarks',
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

        // Check if assessment window is open (Admin can bypass)
        if (! auth()->user()->isAdmin()) {
            $this->requireOpenWindow('ic');
        }

        // Validate input
        $validated = $request->validate([
            'assessment_id' => ['required', 'exists:assessments,id'],
            'component_marks' => ['nullable', 'array'],
            'component_marks.*' => ['nullable', 'integer', 'min:1', 'max:5'],
            'component_remarks' => ['nullable', 'array'],
            'component_remarks.*' => ['nullable', 'string', 'max:1000'],
            'rubric_scores' => ['nullable', 'array'],
            'rubric_scores.*' => ['nullable', 'integer', 'min:1'],
            'mark' => ['nullable', 'numeric', 'min:0'],
            'max_mark' => ['nullable', 'numeric', 'min:0'],
            'remarks' => ['nullable', 'string', 'max:1000'],
        ]);

        $assessmentId = $validated['assessment_id'];
        $assessment = Assessment::with(['components', 'rubrics'])->findOrFail($assessmentId);

        // Validate assessment belongs to IP and has IC evaluator
        if ($assessment->course_code !== 'IP') {
            return redirect()->back()->with('error', 'Invalid assessment.');
        }

        // Check IC evaluator exists for this assessment
        $hasIcEvaluator = $assessment->evaluators()->where('evaluator_role', 'ic')->exists();
        if (! $hasIcEvaluator) {
            return redirect()->back()->with('error', 'This assessment is not assigned to IC evaluator.');
        }

        // Case 1: Component-Based Marking
        if ($assessment->components->isNotEmpty() && isset($validated['component_marks'])) {
            $totalWeightedScore = 0;
            $totalWeight = 0;

            foreach ($validated['component_marks'] as $componentId => $rubricScore) {
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
                $normalizedScore = $rubricScore / 5;
                $weightedScore = $normalizedScore * $componentWeight;

                $totalWeightedScore += $weightedScore;
                $totalWeight += $componentWeight;
            }

            // Calculate overall assessment mark (normalized to 0-5 scale)
            $overallMark = $totalWeight > 0 ? ($totalWeightedScore / $totalWeight) * 5 : null;
            $overallMark = $overallMark ? round($overallMark, 2) : null;

            // Save overall assessment mark
            StudentAssessmentMark::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'assessment_id' => $assessment->id,
                ],
                [
                    'mark' => $overallMark,
                    'max_mark' => 5,
                    'remarks' => $validated['remarks'] ?? null,
                    'evaluated_by' => auth()->id(),
                ]
            );
        }
        // Case 2: Legacy Rubric-Based Marking
        elseif ($assessment->rubrics->isNotEmpty() && isset($validated['rubric_scores'])) {
            foreach ($validated['rubric_scores'] as $rubricId => $score) {
                if ($score === null) {
                    continue;
                }

                $rubric = $assessment->rubrics->find($rubricId);
                if (! $rubric) {
                    continue;
                }

                // Validate score range
                if ($score < $rubric->rubric_min || $score > $rubric->rubric_max) {
                    return redirect()->back()->with('error', "Rubric score for {$rubric->question_title} is out of range.");
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

            // For legacy rubrics, sometimes we don't have a single "Overall Mark" in StudentAssessmentMark
            // unless the system expects one. In OSH/PPE refactor, we usually just store the rubric marks.
        }
        // Case 3: Simple Mark-Based Evaluation
        elseif (isset($validated['mark'])) {
            $mark = $validated['mark'];
            $maxMark = $validated['max_mark'] ?? 100;

            if ($mark !== null && $maxMark > 0 && $mark > $maxMark) {
                return redirect()->back()->with('error', "Mark for {$assessment->assessment_name} cannot exceed {$maxMark}.");
            }

            StudentAssessmentMark::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'assessment_id' => $assessment->id,
                ],
                [
                    'mark' => $mark,
                    'max_mark' => $maxMark,
                    'remarks' => $validated['remarks'] ?? null,
                    'evaluated_by' => auth()->id(),
                ]
            );
        }

        return redirect()->route('academic.ip.ic.show', $student)
            ->with('success', "{$assessment->assessment_name} evaluation saved successfully.")
            ->with('last_saved', now()->format('H:i:s'));
    }
}
