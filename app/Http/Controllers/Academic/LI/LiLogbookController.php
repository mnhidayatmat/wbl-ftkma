<?php

namespace App\Http\Controllers\Academic\LI;

use App\Http\Controllers\Controller;
use App\Models\LiLogbookEvaluation;
use App\Models\Student;
use App\Models\WblGroup;
use App\Traits\ChecksAssessmentWindow;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class LiLogbookController extends Controller
{
    use ChecksAssessmentWindow;

    /**
     * Display the list of students for logbook evaluation.
     */
    public function index(Request $request): View
    {
        // Authorization: Admin or IC can access
        if (! auth()->user()->isAdmin() && ! auth()->user()->isIndustry()) {
            abort(403, 'Unauthorized access.');
        }

        // Build query for students
        $query = Student::with(['group', 'company', 'industryCoach']);

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

        // Get the logbook assessment to determine total periods
        $logbookAssessment = \App\Models\Assessment::forCourse('LI')
            ->whereHas('evaluators', function ($query) {
                $query->where('evaluator_role', 'ic');
            })
            ->active()
            ->where('assessment_type', 'Logbook')
            ->with(['components' => function ($query) {
                $query->whereNotNull('duration_label')->orderBy('order');
            }])
            ->first();

        $totalPeriods = $logbookAssessment ? $logbookAssessment->components->whereNotNull('duration_label')->count() : 6;

        // Get all logbook evaluations for these students
        $allEvaluations = LiLogbookEvaluation::whereIn('student_id', $students->pluck('id'))
            ->get()
            ->groupBy('student_id');

        // Calculate evaluation status for each student
        $studentsWithStatus = $students->map(function ($student) use ($allEvaluations, $totalPeriods) {
            $studentEvaluations = $allEvaluations->get($student->id, collect());
            $completedPeriods = $studentEvaluations->whereNotNull('score')->count();
            $totalScore = $studentEvaluations->sum('score');
            $avgScore = $completedPeriods > 0 ? $totalScore / $completedPeriods : 0;

            // Determine status based on actual total periods
            if ($completedPeriods == 0) {
                $status = 'not_started';
                $statusLabel = 'Not Started';
            } elseif ($completedPeriods < $totalPeriods) {
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
            $student->completed_months = $completedPeriods; // Keep name for backward compatibility
            $student->total_periods = $totalPeriods;
            $student->total_score = $totalScore;
            $student->average_score = $avgScore;
            $student->last_updated = $studentEvaluations->max('updated_at');

            return $student;
        })->filter();

        // Get groups for filter dropdown
        $groups = WblGroup::orderBy('name')->get();

        return view('academic.li.logbook.index', compact(
            'studentsWithStatus',
            'groups'
        ));
    }

    /**
     * Show the logbook evaluation form for a specific student.
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
        $student->load('industryCoach', 'group', 'company');

        // Get Logbook assessment for IC evaluator
        $logbookAssessment = \App\Models\Assessment::forCourse('LI')
            ->whereHas('evaluators', function ($query) {
                $query->where('evaluator_role', 'ic');
            })
            ->active()
            ->where('assessment_type', 'Logbook')
            ->with(['evaluators', 'components' => function ($query) {
                $query->whereNotNull('duration_label')->orderBy('order');
            }])
            ->first();

        if (! $logbookAssessment) {
            abort(404, 'Logbook assessment not found. Please create a Logbook assessment for Industrial Training.');
        }

        // Get logbook components (these define the periods - weeks or months)
        $logbookComponents = $logbookAssessment->components->whereNotNull('duration_label')->sortBy('order');

        if ($logbookComponents->isEmpty()) {
            abort(404, 'Logbook assessment has no components configured. Please configure the logbook periods.');
        }

        // Get existing evaluations for this student, keyed by period index (component order)
        $evaluations = LiLogbookEvaluation::forStudent($student->id)
            ->get()
            ->keyBy('month'); // 'month' field is actually period index (component order)

        // Prepare periods data from assessment components
        $periods = collect();
        foreach ($logbookComponents as $index => $component) {
            $periodIndex = $component->order; // Use component order as period index
            $evaluation = $evaluations->get($periodIndex);

            $periods->push([
                'period_index' => $periodIndex,
                'component_id' => $component->id,
                'label' => $component->duration_label, // e.g., "Week 1", "Month 1"
                'component_name' => $component->component_name,
                'score' => $evaluation?->score,
                'remarks' => $evaluation?->remarks,
                'evaluated_at' => $evaluation?->updated_at,
                'evaluator' => $evaluation?->evaluator,
            ]);
        }

        // Calculate totals
        $totalScore = $evaluations->sum('score');
        $completedPeriods = $evaluations->whereNotNull('score')->count();
        $totalPeriods = $logbookComponents->count();
        $averageScore = $completedPeriods > 0 ? $totalScore / $completedPeriods : 0;
        $maxPossibleScore = $totalPeriods * 10; // total periods × 10 max score

        // Get IC evaluator weight for this assessment
        $assessmentWeight = 0;
        $icEvaluator = $logbookAssessment->evaluators->firstWhere('evaluator_role', 'ic');
        $assessmentWeight = $icEvaluator ? $icEvaluator->total_score : $logbookAssessment->weight_percentage;

        // Check if user can edit
        $canEdit = Gate::allows('edit-ic-marks', $student);

        return view('academic.li.logbook.show', compact(
            'student',
            'periods',
            'totalScore',
            'completedPeriods',
            'totalPeriods',
            'averageScore',
            'maxPossibleScore',
            'assessmentWeight',
            'canEdit',
            'logbookAssessment'
        ));
    }

    /**
     * Store or update logbook evaluations for a student.
     */
    public function store(Request $request, Student $student): RedirectResponse
    {
        // Check authorization using gate
        if (! Gate::allows('edit-ic-marks', $student)) {
            abort(403, 'You are not authorized to evaluate this student\'s logbook.');
        }

        // Check if assessment window is open (Admin can bypass)
        if (! auth()->user()->isAdmin()) {
            $this->requireOpenWindow('ic');
        }

        // Get the logbook assessment to validate period indices
        $logbookAssessment = \App\Models\Assessment::forCourse('LI')
            ->whereHas('evaluators', function ($query) {
                $query->where('evaluator_role', 'ic');
            })
            ->active()
            ->where('assessment_type', 'Logbook')
            ->with(['components' => function ($query) {
                $query->whereNotNull('duration_label')->orderBy('order');
            }])
            ->first();

        if (! $logbookAssessment) {
            abort(404, 'Logbook assessment not found.');
        }

        $logbookComponents = $logbookAssessment->components->whereNotNull('duration_label')->sortBy('order');
        $validPeriodIndices = $logbookComponents->pluck('order')->toArray();

        // Validate input
        $validated = $request->validate([
            'scores' => ['required', 'array'],
            'scores.*' => ['nullable', 'integer', 'min:1', 'max:10'],
            'remarks' => ['nullable', 'array'],
            'remarks.*' => ['nullable', 'string', 'max:1000'],
        ]);

        // Save each period's evaluation (using component order as period index)
        foreach ($validPeriodIndices as $periodIndex) {
            $score = $validated['scores'][$periodIndex] ?? null;
            $remarks = $validated['remarks'][$periodIndex] ?? null;

            // Only save if score is provided
            if ($score !== null) {
                LiLogbookEvaluation::updateOrCreate(
                    [
                        'student_id' => $student->id,
                        'month' => $periodIndex, // 'month' field stores period index (component order)
                    ],
                    [
                        'score' => $score,
                        'remarks' => $remarks,
                        'evaluated_by' => auth()->id(),
                    ]
                );
            }
        }

        return redirect()->route('academic.li.logbook.show', $student)
            ->with('success', 'Logbook evaluation saved successfully.');
    }
}
