<?php

namespace App\Http\Controllers\Academic\FYP;

use App\Http\Controllers\Controller;
use App\Models\FypLogbookEvaluation;
use App\Models\Student;
use App\Models\WblGroup;
use App\Traits\ChecksAssessmentWindow;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class FypLogbookController extends Controller
{
    use ChecksAssessmentWindow;

    /**
     * Display the list of students for logbook evaluation.
     */
    public function index(Request $request): View
    {
        // Authorization: Admin, IC, or FYP Coordinator can access
        if (! auth()->user()->isAdmin() && ! auth()->user()->isIndustry() && ! auth()->user()->isFypCoordinator()) {
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

        // Get all logbook evaluations for these students
        $allEvaluations = FypLogbookEvaluation::whereIn('student_id', $students->pluck('id'))
            ->get()
            ->groupBy('student_id');

        // Calculate evaluation status for each student
        $studentsWithStatus = $students->map(function ($student) use ($allEvaluations) {
            $studentEvaluations = $allEvaluations->get($student->id, collect());
            $completedMonths = $studentEvaluations->whereNotNull('score')->count();
            $totalScore = $studentEvaluations->sum('score');
            $avgScore = $completedMonths > 0 ? $totalScore / $completedMonths : 0;

            // Determine status
            if ($completedMonths == 0) {
                $status = 'not_started';
                $statusLabel = 'Not Started';
            } elseif ($completedMonths < 6) {
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
            $student->completed_months = $completedMonths;
            $student->total_score = $totalScore;
            $student->average_score = $avgScore;
            $student->last_updated = $studentEvaluations->max('updated_at');

            return $student;
        })->filter();

        // Get groups for filter dropdown
        $groups = WblGroup::orderBy('name')->get();

        return view('academic.fyp.logbook.index', compact(
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

        // Get existing evaluations for this student (all 6 months)
        $evaluations = FypLogbookEvaluation::forStudent($student->id)
            ->get()
            ->keyBy('month');

        // Prepare months data
        $months = collect();
        for ($i = 1; $i <= 6; $i++) {
            $evaluation = $evaluations->get($i);
            $months->push([
                'month' => $i,
                'label' => "Month {$i} (M{$i})",
                'score' => $evaluation?->score,
                'remarks' => $evaluation?->remarks,
                'evaluated_at' => $evaluation?->updated_at,
                'evaluator' => $evaluation?->evaluator,
            ]);
        }

        // Calculate totals
        $totalScore = $evaluations->sum('score');
        $completedMonths = $evaluations->whereNotNull('score')->count();
        $averageScore = $completedMonths > 0 ? $totalScore / $completedMonths : 0;
        $maxPossibleScore = 60; // 6 months × 10 max score

        // Get Logbook assessment weight for IC evaluator
        $logbookAssessment = \App\Models\Assessment::forCourse('FYP')
            ->whereHas('evaluators', function ($query) {
                $query->where('evaluator_role', 'ic');
            })
            ->active()
            ->where('assessment_name', 'like', '%Logbook%')
            ->with('evaluators')
            ->first();

        // Get IC evaluator weight for this assessment
        $assessmentWeight = 0;
        if ($logbookAssessment) {
            $icEvaluator = $logbookAssessment->evaluators->firstWhere('evaluator_role', 'ic');
            $assessmentWeight = $icEvaluator ? $icEvaluator->total_score : $logbookAssessment->weight_percentage;
        }

        // Check if user can edit
        $canEdit = Gate::allows('edit-ic-marks', $student);

        return view('academic.fyp.logbook.show', compact(
            'student',
            'months',
            'totalScore',
            'completedMonths',
            'averageScore',
            'maxPossibleScore',
            'assessmentWeight',
            'canEdit'
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

        // Validate input
        $validated = $request->validate([
            'scores' => ['required', 'array'],
            'scores.*' => ['nullable', 'integer', 'min:1', 'max:10'],
            'remarks' => ['nullable', 'array'],
            'remarks.*' => ['nullable', 'string', 'max:1000'],
        ]);

        // Save each month's evaluation
        for ($month = 1; $month <= 6; $month++) {
            $score = $validated['scores'][$month] ?? null;
            $remarks = $validated['remarks'][$month] ?? null;

            // Only save if score is provided
            if ($score !== null) {
                FypLogbookEvaluation::updateOrCreate(
                    [
                        'student_id' => $student->id,
                        'month' => $month,
                    ],
                    [
                        'score' => $score,
                        'remarks' => $remarks,
                        'evaluated_by' => auth()->id(),
                    ]
                );
            }
        }

        return redirect()->route('academic.fyp.logbook.show', $student)
            ->with('success', 'Logbook evaluation saved successfully.');
    }
}
