<?php

namespace App\Http\Controllers\Academic\FYP;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\FYP\FypAuditLog;
use App\Models\FYP\FypRubricEvaluation;
use App\Models\FYP\FypRubricOverallFeedback;
use App\Models\FYP\FypRubricTemplate;
use App\Models\Student;
use App\Models\WblGroup;
use App\Traits\ChecksAssessmentWindow;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class FypRubricEvaluationController extends Controller
{
    use ChecksAssessmentWindow;

    /**
     * Determine the evaluator role based on the current user.
     */
    private function getEvaluatorRole(): string
    {
        $user = auth()->user();

        // Admin can act as AT by default, but can view both
        if ($user->isAdmin()) {
            return request()->get('role', 'at');
        }

        // AT users evaluate as AT
        if ($user->isAt()) {
            return 'at';
        }

        // Industry users evaluate as IC
        if ($user->isIndustry()) {
            return 'ic';
        }

        return 'at'; // Default
    }

    /**
     * Display list of students for rubric evaluation.
     */
    public function index(Request $request): View
    {
        $user = auth()->user();

        // Check access
        if (! $user->isAdmin() && ! $user->isAt() && ! $user->isIndustry()) {
            abort(403, 'Unauthorized access.');
        }

        $evaluatorRole = $this->getEvaluatorRole();

        // Get FYP assessments for this evaluator role with components
        $assessments = Assessment::forCourse('FYP')
            ->where('evaluator_role', $evaluatorRole)
            ->where('is_active', true)
            ->with(['components', 'clos'])
            ->orderBy('assessment_name')
            ->get();

        // Get active rubric templates for this evaluator role
        $templates = FypRubricTemplate::forCourse('FYP')
            ->forEvaluator($evaluatorRole)
            ->active()
            ->with('elements')
            ->orderBy('phase')
            ->get();

        // Get selected assessment
        $selectedAssessmentId = $request->get('assessment');
        $selectedAssessment = $selectedAssessmentId
            ? $assessments->firstWhere('id', $selectedAssessmentId)
            : $assessments->first();

        // Get selected template (for backward compatibility)
        $selectedTemplateId = $request->get('template');
        $selectedTemplate = $selectedTemplateId
            ? $templates->firstWhere('id', $selectedTemplateId)
            : $templates->first();

        // Build query for students
        $query = Student::with(['group', 'company', 'academicTutor', 'industryCoach']);

        // Filter based on role
        if ($evaluatorRole === 'at' && $user->isAt() && ! $user->isAdmin()) {
            $query->where('at_id', $user->id);
        } elseif ($evaluatorRole === 'ic' && $user->isIndustry() && ! $user->isAdmin()) {
            $query->where('ic_id', $user->id);
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

        // Get evaluation status for each student
        if ($selectedTemplate) {
            $totalElements = $selectedTemplate->elements()->active()->count();

            // Get all evaluations for this template
            $evaluations = FypRubricEvaluation::where('rubric_template_id', $selectedTemplate->id)
                ->whereIn('student_id', $students->pluck('id'))
                ->whereNotNull('selected_level')
                ->get()
                ->groupBy('student_id');

            // Get overall feedback
            $feedbacks = FypRubricOverallFeedback::where('rubric_template_id', $selectedTemplate->id)
                ->whereIn('student_id', $students->pluck('id'))
                ->get()
                ->keyBy('student_id');

            $students = $students->map(function ($student) use ($evaluations, $feedbacks, $totalElements, $selectedTemplate) {
                $studentEvaluations = $evaluations->get($student->id, collect());
                $completedCount = $studentEvaluations->count();
                $totalScore = $studentEvaluations->sum('weighted_score');

                // Calculate contribution to grade (based on component_marks)
                $componentMarks = $selectedTemplate->component_marks;
                $contributionScore = $totalScore * ($componentMarks / 100);

                $feedback = $feedbacks->get($student->id);

                // Determine status
                if ($completedCount === 0) {
                    $status = 'not_started';
                    $statusLabel = 'Not Started';
                } elseif ($completedCount < $totalElements) {
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
                $student->completed_elements = $completedCount;
                $student->total_elements = $totalElements;
                $student->total_score = $totalScore;
                $student->contribution_score = $contributionScore;
                $student->feedback_status = $feedback?->status ?? 'draft';
                $student->last_updated = $studentEvaluations->max('updated_at');

                return $student;
            })->filter();
        }

        // Get groups for filter dropdown
        $groups = WblGroup::orderBy('name')->get();

        return view('academic.fyp.rubric-evaluation.index', compact(
            'students',
            'assessments',
            'selectedAssessment',
            'templates',
            'selectedTemplate',
            'groups',
            'evaluatorRole'
        ));
    }

    /**
     * Show the rubric evaluation form for a specific student.
     */
    public function show(Request $request, Student $student): View
    {
        $user = auth()->user();
        $evaluatorRole = $this->getEvaluatorRole();

        // Check authorization based on role
        if (! $user->isAdmin()) {
            if ($evaluatorRole === 'at' && $user->isAt()) {
                if ($student->at_id !== $user->id) {
                    abort(403, 'You are not authorized to evaluate this student.');
                }
            } elseif ($evaluatorRole === 'ic' && $user->isIndustry()) {
                if ($student->ic_id !== $user->id) {
                    abort(403, 'You are not authorized to evaluate this student.');
                }
            } else {
                abort(403, 'Unauthorized access.');
            }
        }

        // Load relationships
        $student->load('academicTutor', 'industryCoach', 'group');

        // Get selected template for this evaluator role
        $templateId = $request->get('template');

        if ($templateId) {
            $template = FypRubricTemplate::forCourse('FYP')
                ->forEvaluator($evaluatorRole)
                ->active()
                ->with(['elements.levelDescriptors'])
                ->findOrFail($templateId);
        } else {
            // Default to first template for this role
            $template = FypRubricTemplate::forCourse('FYP')
                ->forEvaluator($evaluatorRole)
                ->active()
                ->with(['elements.levelDescriptors'])
                ->first();

            if (! $template) {
                abort(404, 'No rubric template configured for this evaluator role.');
            }
        }

        // Get existing evaluations
        $evaluations = FypRubricEvaluation::where('student_id', $student->id)
            ->where('rubric_template_id', $template->id)
            ->get()
            ->keyBy('rubric_element_id');

        // Get overall feedback
        $overallFeedback = FypRubricOverallFeedback::firstOrNew([
            'student_id' => $student->id,
            'rubric_template_id' => $template->id,
        ]);

        // Group elements by CLO for display
        $elementsByClo = $template->elements()->active()->get()->groupBy('clo_code');

        // Calculate totals
        $totalScore = $evaluations->sum('weighted_score');
        $totalWeight = $template->calculateTotalWeight();
        $percentageScore = $totalWeight > 0 ? ($totalScore / $totalWeight) * 100 : 0;

        // Calculate contribution to overall grade
        $componentMarks = $template->component_marks;
        $contributionToGrade = $totalScore * ($componentMarks / 100);

        // Get other available templates for this role
        $allTemplates = FypRubricTemplate::forCourse('FYP')
            ->forEvaluator($evaluatorRole)
            ->active()
            ->orderBy('phase')
            ->orderBy('assessment_type')
            ->get();

        // Check if user can edit
        $canEdit = $user->isAdmin() ||
            ($evaluatorRole === 'at' && $user->isAt() && $student->at_id == $user->id) ||
            ($evaluatorRole === 'ic' && $user->isIndustry() && $student->ic_id == $user->id);

        return view('academic.fyp.rubric-evaluation.show', compact(
            'student',
            'template',
            'elementsByClo',
            'evaluations',
            'overallFeedback',
            'totalScore',
            'totalWeight',
            'percentageScore',
            'contributionToGrade',
            'componentMarks',
            'allTemplates',
            'canEdit',
            'evaluatorRole'
        ));
    }

    /**
     * Store or update rubric evaluations.
     */
    public function store(Request $request, Student $student): RedirectResponse
    {
        $user = auth()->user();
        $evaluatorRole = $this->getEvaluatorRole();

        // Check authorization
        if (! $user->isAdmin()) {
            if ($evaluatorRole === 'at' && $user->isAt()) {
                if ($student->at_id !== $user->id) {
                    abort(403, 'You are not authorized to evaluate this student.');
                }
            } elseif ($evaluatorRole === 'ic' && $user->isIndustry()) {
                if ($student->ic_id !== $user->id) {
                    abort(403, 'You are not authorized to evaluate this student.');
                }
            } else {
                abort(403, 'Unauthorized access.');
            }
        }

        // Check assessment window
        if (! $user->isAdmin()) {
            $this->requireOpenWindow($evaluatorRole);
        }

        $validated = $request->validate([
            'template_id' => ['required', 'exists:fyp_rubric_templates,id'],
            'evaluations' => ['required', 'array'],
            'evaluations.*.element_id' => ['required', 'exists:fyp_rubric_elements,id'],
            'evaluations.*.level' => ['nullable', 'integer', 'min:1', 'max:5'],
            'evaluations.*.remarks' => ['nullable', 'string', 'max:1000'],
            'overall_feedback' => ['nullable', 'string', 'max:2000'],
            'strengths' => ['nullable', 'string', 'max:1000'],
            'areas_for_improvement' => ['nullable', 'string', 'max:1000'],
        ]);

        $template = FypRubricTemplate::findOrFail($validated['template_id']);

        // Verify template matches evaluator role
        if ($template->evaluator_role !== $evaluatorRole && ! $user->isAdmin()) {
            abort(403, 'This rubric template is not for your evaluator role.');
        }

        if ($template->is_locked) {
            return redirect()->back()
                ->with('error', 'This rubric template is locked and cannot be used for evaluation.');
        }

        DB::transaction(function () use ($student, $template, $validated) {
            $updatedElements = [];

            foreach ($validated['evaluations'] as $evalData) {
                if (! isset($evalData['level']) || $evalData['level'] === null) {
                    continue;
                }

                $element = $template->elements()->find($evalData['element_id']);
                if (! $element) {
                    continue;
                }

                // Get the level descriptor
                $descriptor = $element->getDescriptorForLevel($evalData['level']);

                $evaluation = FypRubricEvaluation::updateOrCreate(
                    [
                        'student_id' => $student->id,
                        'rubric_element_id' => $evalData['element_id'],
                    ],
                    [
                        'rubric_template_id' => $template->id,
                        'selected_level_id' => $descriptor?->id,
                        'selected_level' => $evalData['level'],
                        'remarks' => $evalData['remarks'] ?? null,
                        'evaluated_by' => auth()->id(),
                    ]
                );

                $updatedElements[] = $element->name;
            }

            // Update overall feedback
            FypRubricOverallFeedback::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'rubric_template_id' => $template->id,
                ],
                [
                    'overall_feedback' => $validated['overall_feedback'] ?? null,
                    'strengths' => $validated['strengths'] ?? null,
                    'areas_for_improvement' => $validated['areas_for_improvement'] ?? null,
                    'evaluated_by' => auth()->id(),
                ]
            );

            // Log audit
            if (! empty($updatedElements)) {
                $roleLabel = $template->evaluator_role === 'at' ? 'AT' : 'IC';
                FypAuditLog::log(
                    'rubric_evaluation_updated',
                    'evaluation',
                    "{$roleLabel} rubric evaluation updated for {$student->name} ({$student->matric_no}) - {$template->name}",
                    [
                        'student_id' => $student->id,
                        'template_id' => $template->id,
                        'template_name' => $template->name,
                        'evaluator_role' => $template->evaluator_role,
                        'elements_updated' => $updatedElements,
                    ],
                    $student->id
                );
            }
        });

        return redirect()->route('academic.fyp.rubric-evaluation.show', [
            'student' => $student,
            'template' => $template->id,
        ])->with('success', 'Evaluation saved successfully.');
    }

    /**
     * Submit evaluation for review.
     */
    public function submit(Request $request, Student $student, FypRubricTemplate $template): RedirectResponse
    {
        $user = auth()->user();
        $evaluatorRole = $this->getEvaluatorRole();

        // Check authorization
        if (! $user->isAdmin()) {
            if ($evaluatorRole === 'at' && $user->isAt()) {
                if ($student->at_id !== $user->id) {
                    abort(403, 'You are not authorized to submit evaluation for this student.');
                }
            } elseif ($evaluatorRole === 'ic' && $user->isIndustry()) {
                if ($student->ic_id !== $user->id) {
                    abort(403, 'You are not authorized to submit evaluation for this student.');
                }
            } else {
                abort(403, 'Unauthorized access.');
            }
        }

        // Check if all elements are evaluated
        $totalElements = $template->elements()->active()->count();
        $evaluatedElements = FypRubricEvaluation::where('student_id', $student->id)
            ->where('rubric_template_id', $template->id)
            ->whereNotNull('selected_level')
            ->count();

        if ($evaluatedElements < $totalElements) {
            return redirect()->back()
                ->with('error', "Cannot submit evaluation. Please evaluate all elements ({$evaluatedElements}/{$totalElements} completed).");
        }

        $feedback = FypRubricOverallFeedback::firstOrCreate(
            [
                'student_id' => $student->id,
                'rubric_template_id' => $template->id,
            ],
            [
                'evaluated_by' => auth()->id(),
            ]
        );

        $feedback->submit();

        $roleLabel = $template->evaluator_role === 'at' ? 'AT' : 'IC';
        FypAuditLog::log(
            'rubric_evaluation_submitted',
            'evaluation',
            "{$roleLabel} rubric evaluation submitted for {$student->name} ({$student->matric_no}) - {$template->name}",
            [
                'student_id' => $student->id,
                'template_id' => $template->id,
                'total_score' => $feedback->total_score,
                'percentage_score' => $feedback->percentage_score,
            ],
            $student->id
        );

        return redirect()->route('academic.fyp.rubric-evaluation.show', [
            'student' => $student,
            'template' => $template->id,
        ])->with('success', 'Evaluation submitted successfully.');
    }

    /**
     * Release evaluation to student.
     */
    public function release(Request $request, Student $student, FypRubricTemplate $template): RedirectResponse
    {
        // Only Admin can release
        if (! auth()->user()->isAdmin() && ! auth()->user()->isFypCoordinator()) {
            abort(403, 'Only administrators can release evaluations.');
        }

        $feedback = FypRubricOverallFeedback::where('student_id', $student->id)
            ->where('rubric_template_id', $template->id)
            ->first();

        if (! $feedback) {
            return redirect()->back()
                ->with('error', 'No evaluation found for this student.');
        }

        if ($feedback->status !== FypRubricOverallFeedback::STATUS_SUBMITTED) {
            return redirect()->back()
                ->with('error', 'Evaluation must be submitted before it can be released.');
        }

        $feedback->release();

        $roleLabel = $template->evaluator_role === 'at' ? 'AT' : 'IC';
        FypAuditLog::log(
            'rubric_evaluation_released',
            'evaluation',
            "{$roleLabel} rubric evaluation released to {$student->name} ({$student->matric_no}) - {$template->name}",
            [
                'student_id' => $student->id,
                'template_id' => $template->id,
            ],
            $student->id
        );

        return redirect()->route('academic.fyp.rubric-evaluation.show', [
            'student' => $student,
            'template' => $template->id,
        ])->with('success', 'Evaluation released to student.');
    }
}
