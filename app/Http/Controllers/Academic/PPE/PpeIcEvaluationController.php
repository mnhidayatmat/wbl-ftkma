<?php

namespace App\Http\Controllers\Academic\PPE;

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

class PpeIcEvaluationController extends Controller
{
    use ChecksAssessmentWindow;

    /**
     * Display the list of students for IC evaluation.
     */
    /**
     * Display the list of students for IC evaluation.
     */
    public function index(Request $request): View
    {
        // Authorization checked via middleware, but double-check here
        if (! auth()->user()->isAdmin() && ! auth()->user()->isIndustry()) {
            abort(403, 'Unauthorized access.');
        }

        // Get active assessments for PPE course with IC evaluator role
        $icAssessments = Assessment::forCourse('PPE')
            ->forEvaluator('ic')
            ->active()
            // Support both Rubric and Component based
            ->with(['rubrics', 'components'])
            ->get();

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

        // Get all rubric marks for these students
        $allRubricMarks = StudentAssessmentRubricMark::whereIn('student_id', $students->pluck('id'))
            ->get()
            ->groupBy('student_id');

        // Get all overall marks for these students (for component-based assessments)
        $allMarks = StudentAssessmentMark::whereIn('student_id', $students->pluck('id'))
            ->whereIn('assessment_id', $icAssessments->pluck('id'))
            ->get()
            ->groupBy('student_id');

        // Calculate total items to complete
        $totalAssessments = $icAssessments->count();

        // Calculate evaluation status for each student
        $studentsWithStatus = $students->map(function ($student) use ($allRubricMarks, $allMarks, $icAssessments, $totalAssessments) {
            $studentRubricMarks = $allRubricMarks->get($student->id, collect());
            $studentMarks = $allMarks->get($student->id, collect());

            $totalContribution = 0;
            $completedAssessments = 0;

            foreach ($icAssessments as $assessment) {
                $isCompleted = false;

                if ($assessment->components->count() > 0) {
                    // Component Based: Check if overall mark exists
                    $mark = $studentMarks->firstWhere('assessment_id', $assessment->id);
                    if ($mark && $mark->mark !== null) {
                        $isCompleted = true;
                        if ($mark->max_mark > 0) {
                            $totalContribution += ($mark->mark / $mark->max_mark) * $assessment->weight_percentage;
                        }
                    }
                } elseif ($assessment->rubrics->count() > 0) {
                    // Rubric Based: Check if all rubrics are filled
                    $rubricIds = $assessment->rubrics->pluck('id');
                    $filledRubrics = 0;
                    $assessmentRubricMarks = $studentRubricMarks->whereIn('assessment_rubric_id', $rubricIds);

                    foreach ($assessmentRubricMarks as $rMark) {
                        $filledRubrics++;
                        $totalContribution += $rMark->weighted_contribution;
                    }

                    if ($filledRubrics === $rubricIds->count() && $filledRubrics > 0) {
                        $isCompleted = true;
                    }
                }

                if ($isCompleted) {
                    $completedAssessments++;
                }
            }

            // Determine status
            if ($completedAssessments == 0) {
                $status = 'not_started';
                $statusLabel = 'Not Started';
            } elseif ($completedAssessments < $totalAssessments) {
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
            $student->completed_questions = $completedAssessments;
            $student->total_questions = $totalAssessments;

            $lastRubricUpdate = $studentRubricMarks->max('updated_at');
            $lastMarkUpdate = $studentMarks->max('updated_at');
            $student->last_updated = max($lastRubricUpdate, $lastMarkUpdate);

            return $student;
        })->filter();

        // Get groups for filter dropdown
        $groups = WblGroup::orderBy('name')->get();

        // Calculate total IC weight
        $totalIcWeight = $icAssessments->sum('weight_percentage');

        return view('academic.ppe.ic.index', compact(
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
        // Check authorization - all authenticated users can view, but only assigned IC can edit
        if (! Gate::allows('view', $student)) {
            abort(403, 'You are not authorized to view this student.');
        }

        // Load relationships
        $student->load('academicTutor', 'industryCoach', 'group');

        // Get active rubric-based and component-based assessments for PPE course with IC evaluator role
        $icAssessments = Assessment::forCourse('PPE')
            ->forEvaluator('ic')
            ->active()
            // Removed whereIn('assessment_type', ['Oral', 'Rubric']) to support all types if they have components
            ->with(['rubrics', 'components'])
            ->orderBy('clo_code')
            ->get();

        // Get existing rubric marks for this student (Legacy)
        $rubricMarks = StudentAssessmentRubricMark::where('student_id', $student->id)
            ->with('rubric.assessment')
            ->get()
            ->keyBy('assessment_rubric_id');

        // Get existing component marks for this student (New)
        $componentMarks = \App\Models\StudentAssessmentComponentMark::where('student_id', $student->id)
            ->whereIn('assessment_id', $icAssessments->pluck('id'))
            ->get();

        // Get existing overall marks
        $marks = StudentAssessmentMark::where('student_id', $student->id)
            ->whereIn('assessment_id', $icAssessments->pluck('id'))
            ->get()
            ->keyBy('assessment_id');

        // Group assessments by CLO for display
        $assessmentsByClo = $icAssessments->groupBy('clo_code');

        // Calculate total contribution
        $totalContribution = 0;

        foreach ($icAssessments as $assessment) {
            // Check if we have an overall mark (Component based or Direct Mark)
            $overallMark = $marks->get($assessment->id);

            if ($overallMark && $overallMark->mark !== null && $overallMark->max_mark > 0) {
                // Use the overall mark
                $totalContribution += ($overallMark->mark / $overallMark->max_mark) * $assessment->weight_percentage;
            } else {
                // Fallback to Rubric marks
                if ($assessment->rubrics->isNotEmpty()) {
                    foreach ($assessment->rubrics as $rubric) {
                        $rMark = $rubricMarks->get($rubric->id);
                        if ($rMark) {
                            $totalContribution += $rMark->weighted_contribution;
                        }
                    }
                }
            }
        }

        // Get Lecturer (AT) marks for read-only display
        $atAssessments = Assessment::forCourse('PPE')
            ->forEvaluator('lecturer')
            ->active()
            ->orderBy('clo_code')
            ->orderBy('assessment_name')
            ->get();

        $atMarks = StudentAssessmentMark::where('student_id', $student->id)
            ->whereIn('assessment_id', $atAssessments->pluck('id'))
            ->with('assessment')
            ->get()
            ->keyBy('assessment_id');

        $atTotalContribution = 0;
        foreach ($atMarks as $mark) {
            if ($mark->mark !== null && $mark->max_mark > 0) {
                $atTotalContribution += ($mark->mark / $mark->max_mark) * $mark->assessment->weight_percentage;
            }
        }

        return view('academic.ppe.ic.show', compact(
            'student',
            'icAssessments',
            'assessmentsByClo',
            'rubricMarks',
            'totalContribution',
            'atAssessments',
            'atMarks',
            'atTotalContribution',
            'componentMarks',
            'marks'
        ));
    }

    /**
     * Store or update rubric scores for a student.
     */
    public function store(Request $request, Student $student): RedirectResponse
    {
        // Check authorization using gate
        if (! Gate::allows('edit-ic-marks', $student)) {
            abort(403, 'You are not authorized to edit IC marks for this student.');
        }

        // Get active assessments for PPE course with IC evaluator role
        $icAssessments = Assessment::forCourse('PPE')
            ->forEvaluator('ic')
            ->active()
            // Support both Rubric and Component based
            ->with(['rubrics', 'components'])
            ->get();

        // Create a lookup for all valid assessment IDs and their component/rubric IDs
        $validAssessmentIds = $icAssessments->pluck('id');
        $validRubricIds = $icAssessments->flatMap->rubrics->pluck('id');
        $validComponentIds = $icAssessments->flatMap->components->pluck('id');

        // Validate input
        $validated = $request->validate([
            // Rubric validation
            'rubric_scores' => ['nullable', 'array'],
            'rubric_scores.*' => ['nullable', 'integer', 'min:1'],

            // Component validation (new)
            'component_marks' => ['nullable', 'array'],
            'component_marks.*' => ['nullable', 'integer', 'min:1', 'max:5'],
            'component_remarks' => ['nullable', 'array'],
            'component_remarks.*' => ['nullable', 'string', 'max:1000'],
            'assessment_id' => ['nullable', 'exists:assessments,id'],
            'remarks' => ['nullable', 'array'],
            'remarks.*' => ['nullable', 'string', 'max:1000'],
        ]);

        $savedCount = 0;

        // 1. Handle legacy Rubric scores
        if (! empty($validated['rubric_scores'])) {
            foreach ($validated['rubric_scores'] as $rubricId => $score) {
                if ($score === null) {
                    continue;
                }

                // Verify rubric belongs to IC assessment
                if (! $validRubricIds->contains($rubricId)) {
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
                $savedCount++;
            }
        }

        // 2. Handle new Component marks
        if ($request->has('component_marks')) {
            // If assessment_id is provided (usually from a single assessment form submission), process just that one
            // Or if component_marks comes as a flat array, iterate through it.
            // The FYP implementation handles this slightly differently depending on if it's a "Group" submission or "Single".
            // Here, let's just iterate through component_marks regardless of assessment_id to be robust.

            foreach ($validated['component_marks'] as $componentId => $score) {
                if ($score === null) {
                    continue;
                }

                // Handle temporary component IDs
                if (str_starts_with((string) $componentId, 'temp_')) {
                    continue;
                }

                // Verify component belongs to IC assessment
                if (! $validComponentIds->contains($componentId)) {
                    continue;
                }

                $component = \App\Models\AssessmentComponent::find($componentId);
                if (! $component) {
                    continue;
                }

                $remarks = $validated['component_remarks'][$componentId] ?? null;

                \App\Models\StudentAssessmentComponentMark::updateOrCreate(
                    [
                        'student_id' => $student->id,
                        'assessment_id' => $component->assessment_id,
                        'component_id' => $componentId,
                    ],
                    [
                        'rubric_score' => $score,
                        'remarks' => $remarks,
                        'evaluated_by' => auth()->id(),
                    ]
                );
                $savedCount++;
            }

            // Recalculate and update overall marks for assessments that had components updated
            // This is a bit complex efficiently, but let's do it per assessment found in the input
            $processedAssessmentIds = collect(array_keys($validated['component_marks']))
                ->map(fn ($cid) => \App\Models\AssessmentComponent::find($cid)?->assessment_id)
                ->filter()
                ->unique();

            foreach ($processedAssessmentIds as $assessmentId) {
                if (! $validAssessmentIds->contains($assessmentId)) {
                    continue;
                }

                $assessment = $icAssessments->find($assessmentId);
                if (! $assessment) {
                    continue;
                }

                // Calculate total score based on components
                $studentComponents = \App\Models\StudentAssessmentComponentMark::where('student_id', $student->id)
                    ->where('assessment_id', $assessmentId)
                    ->get();

                $totalWeightedScore = 0;
                $totalWeight = 0;

                foreach ($assessment->components as $comp) {
                    $mark = $studentComponents->firstWhere('component_id', $comp->id);
                    if ($mark) {
                        // Normalize 1-5 score to percentage
                        $normalized = ($mark->rubric_score / 5) * 100;
                        $totalWeightedScore += ($normalized / 100) * $comp->weight_percentage;
                    }
                    $totalWeight += $comp->weight_percentage;
                }

                // Normalize back to 0-5 for the main mark storage if totalWeight > 0
                // Or store as percentage? FYP stores normalized 0-5.
                $overallMark = $totalWeight > 0 ? ($totalWeightedScore / $totalWeight) * 5 : 0;

                $overallRemarks = $validated['remarks'][$assessmentId] ?? null;

                // Save overall assessment mark
                StudentAssessmentMark::updateOrCreate(
                    [
                        'student_id' => $student->id,
                        'assessment_id' => $assessmentId,
                    ],
                    [
                        'mark' => $overallMark,
                        'max_mark' => 5, // Since we normalize to 0-5 scale
                        'remarks' => $overallRemarks,
                        'evaluated_by' => auth()->id(),
                    ]
                );
            }
        }

        return redirect()->route('academic.ppe.ic.show', $student)
            ->with('success', 'Evaluation saved successfully.')
            ->with('last_saved', now()->format('H:i:s'));
    }
}
