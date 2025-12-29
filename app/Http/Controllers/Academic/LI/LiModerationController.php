<?php

namespace App\Http\Controllers\Academic\LI;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\LI\LiAuditLog;
use App\Models\LI\LiModerationRecord;
use App\Models\Student;
use App\Models\StudentAssessmentMark;
use App\Models\WblGroup;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LiModerationController extends Controller
{
    /**
     * Display moderation overview page.
     */
    public function index(Request $request): View
    {
        if (! auth()->user()->isAdmin() && ! auth()->user()->isLiCoordinator()) {
            abort(403, 'Unauthorized access.');
        }

        // Get active assessments - LI uses Supervisor (stored as 'lecturer' in database)
        $supervisorAssessments = Assessment::forCourse('LI')
            ->forEvaluator('lecturer')
            ->active()
            ->get();

        // Get active IC assessments
        $icAssessments = Assessment::forCourse('LI')
            ->forEvaluator('ic')
            ->active()
            ->get();

        // Build query for students
        $query = Student::with(['group', 'company']);

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

        // Get all Supervisor marks
        $allSupervisorMarks = StudentAssessmentMark::whereIn('student_id', $students->pluck('id'))
            ->whereIn('assessment_id', $supervisorAssessments->pluck('id'))
            ->with('assessment')
            ->get()
            ->groupBy('student_id');

        // Get all IC marks
        $allIcMarks = StudentAssessmentMark::whereIn('student_id', $students->pluck('id'))
            ->whereIn('assessment_id', $icAssessments->pluck('id'))
            ->with('assessment')
            ->get()
            ->groupBy('student_id');

        // Get moderation records
        $moderationRecords = LiModerationRecord::whereIn('student_id', $students->pluck('id'))
            ->with(['student', 'moderator'])
            ->get()
            ->keyBy('student_id');

        // Calculate scores for each student
        $studentsWithScores = $students->map(function ($student) use (
            $allSupervisorMarks,
            $allIcMarks,
            $supervisorAssessments,
            $icAssessments,
            $moderationRecords
        ) {
            // Calculate Supervisor marks
            $supervisorMarks = $allSupervisorMarks->get($student->id, collect());
            $supervisorMarksByAssessment = $supervisorMarks->keyBy('assessment_id');

            $supervisorTotal = 0;
            foreach ($supervisorAssessments as $assessment) {
                $mark = $supervisorMarksByAssessment->get($assessment->id);
                if ($mark && $mark->mark !== null && $mark->max_mark > 0) {
                    $supervisorTotal += ($mark->mark / $mark->max_mark) * $assessment->weight_percentage;
                }
            }

            // Calculate IC marks
            $icMarks = $allIcMarks->get($student->id, collect());
            $icMarksByAssessment = $icMarks->keyBy('assessment_id');

            $icTotal = 0;
            foreach ($icAssessments as $assessment) {
                $mark = $icMarksByAssessment->get($assessment->id);
                if ($mark && $mark->mark !== null && $mark->max_mark > 0) {
                    $icTotal += ($mark->mark / $mark->max_mark) * $assessment->weight_percentage;
                }
            }

            // Calculate final score
            $finalScore = $supervisorTotal + $icTotal;

            // Check if moderated
            $moderation = $moderationRecords->get($student->id);

            $student->supervisor_score = round($supervisorTotal, 2);
            $student->ic_score = round($icTotal, 2);
            $student->final_score = round($finalScore, 2);
            $student->moderation = $moderation;

            return $student;
        });

        // Get groups for filter
        $groups = WblGroup::orderBy('name')->get();

        return view('academic.li.moderation.index', compact(
            'studentsWithScores',
            'groups'
        ));
    }

    /**
     * Show moderation form for a specific student.
     */
    public function show(Student $student): View
    {
        if (! auth()->user()->isAdmin() && ! auth()->user()->isLiCoordinator()) {
            abort(403, 'Unauthorized access.');
        }

        // Get active assessments
        $supervisorAssessments = Assessment::forCourse('LI')
            ->forEvaluator('lecturer')
            ->active()
            ->get();

        $icAssessments = Assessment::forCourse('LI')
            ->forEvaluator('ic')
            ->active()
            ->get();

        // Calculate original scores
        $allSupervisorMarks = StudentAssessmentMark::where('student_id', $student->id)
            ->whereIn('assessment_id', $supervisorAssessments->pluck('id'))
            ->with('assessment')
            ->get()
            ->keyBy('assessment_id');

        $allIcMarks = StudentAssessmentMark::where('student_id', $student->id)
            ->whereIn('assessment_id', $icAssessments->pluck('id'))
            ->with('assessment')
            ->get()
            ->keyBy('assessment_id');

        $supervisorTotal = 0;
        foreach ($supervisorAssessments as $assessment) {
            $mark = $allSupervisorMarks->get($assessment->id);
            if ($mark && $mark->mark !== null && $mark->max_mark > 0) {
                $supervisorTotal += ($mark->mark / $mark->max_mark) * $assessment->weight_percentage;
            }
        }

        $icTotal = 0;
        foreach ($icAssessments as $assessment) {
            $mark = $allIcMarks->get($assessment->id);
            if ($mark && $mark->mark !== null && $mark->max_mark > 0) {
                $icTotal += ($mark->mark / $mark->max_mark) * $assessment->weight_percentage;
            }
        }

        $finalScore = $supervisorTotal + $icTotal;

        // Get existing moderation record
        $moderation = LiModerationRecord::where('student_id', $student->id)->first();

        return view('academic.li.moderation.show', compact(
            'student',
            'supervisorTotal',
            'icTotal',
            'finalScore',
            'moderation'
        ));
    }

    /**
     * Store moderation adjustments.
     */
    public function store(Request $request, Student $student)
    {
        if (! auth()->user()->isAdmin() && ! auth()->user()->isLiCoordinator()) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'adjustment_type' => ['required', 'in:percentage,manual_override'],
            'adjustment_percentage' => ['nullable', 'numeric', 'min:-100', 'max:100'],
            'adjusted_supervisor_score' => ['nullable', 'numeric', 'min:0'],
            'adjusted_ic_score' => ['nullable', 'numeric', 'min:0'],
            'adjusted_final_score' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'justification' => ['required', 'string', 'min:10', 'max:1000'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        // Get original scores
        $supervisorAssessments = Assessment::forCourse('LI')
            ->forEvaluator('lecturer')
            ->active()
            ->get();

        $icAssessments = Assessment::forCourse('LI')
            ->forEvaluator('ic')
            ->active()
            ->get();

        $allSupervisorMarks = StudentAssessmentMark::where('student_id', $student->id)
            ->whereIn('assessment_id', $supervisorAssessments->pluck('id'))
            ->with('assessment')
            ->get()
            ->keyBy('assessment_id');

        $allIcMarks = StudentAssessmentMark::where('student_id', $student->id)
            ->whereIn('assessment_id', $icAssessments->pluck('id'))
            ->with('assessment')
            ->get()
            ->keyBy('assessment_id');

        $originalSupervisorTotal = 0;
        foreach ($supervisorAssessments as $assessment) {
            $mark = $allSupervisorMarks->get($assessment->id);
            if ($mark && $mark->mark !== null && $mark->max_mark > 0) {
                $originalSupervisorTotal += ($mark->mark / $mark->max_mark) * $assessment->weight_percentage;
            }
        }

        $originalIcTotal = 0;
        foreach ($icAssessments as $assessment) {
            $mark = $allIcMarks->get($assessment->id);
            if ($mark && $mark->mark !== null && $mark->max_mark > 0) {
                $originalIcTotal += ($mark->mark / $mark->max_mark) * $assessment->weight_percentage;
            }
        }

        $originalFinalScore = $originalSupervisorTotal + $originalIcTotal;

        // Calculate adjusted scores
        if ($validated['adjustment_type'] === 'percentage') {
            $adjustmentFactor = 1 + ($validated['adjustment_percentage'] / 100);
            $adjustedSupervisorScore = $originalSupervisorTotal * $adjustmentFactor;
            $adjustedIcScore = $originalIcTotal * $adjustmentFactor;
            $adjustedFinalScore = $originalFinalScore * $adjustmentFactor;
        } else {
            $adjustedSupervisorScore = $validated['adjusted_supervisor_score'] ?? $originalSupervisorTotal;
            $adjustedIcScore = $validated['adjusted_ic_score'] ?? $originalIcTotal;
            $adjustedFinalScore = $validated['adjusted_final_score'] ?? $originalFinalScore;
        }

        // Create or update moderation record
        $moderation = LiModerationRecord::updateOrCreate(
            ['student_id' => $student->id],
            [
                'original_supervisor_score' => round($originalSupervisorTotal, 2),
                'original_ic_score' => round($originalIcTotal, 2),
                'original_final_score' => round($originalFinalScore, 2),
                'adjusted_supervisor_score' => round($adjustedSupervisorScore, 2),
                'adjusted_ic_score' => round($adjustedIcScore, 2),
                'adjusted_final_score' => round($adjustedFinalScore, 2),
                'adjustment_percentage' => $validated['adjustment_percentage'] ?? 0,
                'adjustment_type' => $validated['adjustment_type'],
                'justification' => $validated['justification'],
                'notes' => $validated['notes'] ?? null,
                'moderated_by' => auth()->id(),
            ]
        );

        // Log audit
        LiAuditLog::log(
            'moderation_applied',
            'moderation',
            "Moderation applied to student {$student->name} ({$student->matric_no})",
            [
                'student_id' => $student->id,
                'moderation_id' => $moderation->id,
                'adjustment_type' => $validated['adjustment_type'],
                'original_score' => $originalFinalScore,
                'adjusted_score' => $adjustedFinalScore,
            ],
            $student->id
        );

        return redirect()->route('academic.li.moderation.index')
            ->with('success', 'Moderation applied successfully.');
    }
}
