<?php

namespace App\Http\Controllers\Academic\FYP;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\FYP\FypModerationRecord;
use App\Models\FYP\FypAuditLog;
use App\Models\Student;
use App\Models\StudentAssessmentMark;
use App\Models\StudentAssessmentRubricMark;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FypModerationController extends Controller
{
    /**
     * Display moderation overview page.
     */
    public function index(Request $request): View
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        // Get active assessments - FYP uses AT (Academic Tutor) as evaluator (stored as 'lecturer' in database)
        $atAssessments = Assessment::forCourse('FYP')
            ->forEvaluator('lecturer')
            ->active()
            ->get();

        // Get AT rubric assessments
        $atRubricAssessments = Assessment::forCourse('FYP')
            ->forEvaluator('lecturer')
            ->active()
            ->whereIn('assessment_type', ['Oral', 'Rubric'])
            ->with('rubrics')
            ->get();

        // Build query for students
        $query = Student::with(['group', 'company']);

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
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

        // Get all AT marks
        $allAtMarks = StudentAssessmentMark::whereIn('student_id', $students->pluck('id'))
            ->whereIn('assessment_id', $atAssessments->pluck('id'))
            ->with('assessment')
            ->get()
            ->groupBy('student_id');

        // Get all AT rubric marks
        $allAtRubricMarks = StudentAssessmentRubricMark::whereIn('student_id', $students->pluck('id'))
            ->whereHas('rubric.assessment', function($q) {
                $q->where('course_code', 'FYP')->where('evaluator_role', 'lecturer');
            })
            ->with('rubric.assessment')
            ->get()
            ->groupBy('student_id');

        // Get moderation records
        $moderationRecords = FypModerationRecord::whereIn('student_id', $students->pluck('id'))
            ->with(['student', 'moderator'])
            ->get()
            ->keyBy('student_id');

        // Calculate scores for each student
        $studentsWithScores = $students->map(function($student) use (
            $allAtMarks,
            $allAtRubricMarks,
            $atAssessments,
            $atRubricAssessments,
            $moderationRecords
        ) {
            // Calculate AT marks
            $atMarks = $allAtMarks->get($student->id, collect());
            $atMarksByAssessment = $atMarks->keyBy('assessment_id');
            
            $atTotal = 0;
            foreach ($atAssessments as $assessment) {
                $mark = $atMarksByAssessment->get($assessment->id);
                if ($mark && $mark->mark !== null && $mark->max_mark > 0) {
                    $atTotal += ($mark->mark / $mark->max_mark) * $assessment->weight_percentage;
                }
            }

            // Calculate AT rubric marks
            $atRubricMarks = $allAtRubricMarks->get($student->id, collect());
            $atRubricTotal = 0;
            foreach ($atRubricMarks as $rubricMark) {
                $atRubricTotal += $rubricMark->weighted_contribution;
            }

            // Calculate final score (AT only)
            $finalScore = $atTotal + $atRubricTotal;

            // Check if moderated
            $moderation = $moderationRecords->get($student->id);

            $student->at_score = round($atTotal + $atRubricTotal, 2);
            $student->final_score = round($finalScore, 2);
            $student->moderation = $moderation;

            return $student;
        });

        // Get groups for filter
        $groups = \App\Models\WblGroup::orderBy('name')->get();

        return view('academic.fyp.moderation.index', compact(
            'studentsWithScores',
            'groups'
        ));
    }

    /**
     * Show moderation form for a specific student.
     */
    public function show(Student $student): View
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        // Get active assessments - FYP uses AT (Academic Tutor) as evaluator (stored as 'lecturer' in database)
        $atAssessments = Assessment::forCourse('FYP')
            ->forEvaluator('lecturer')
            ->active()
            ->get();

        $atRubricAssessments = Assessment::forCourse('FYP')
            ->forEvaluator('lecturer')
            ->active()
            ->whereIn('assessment_type', ['Oral', 'Rubric'])
            ->with('rubrics')
            ->get();

        // Calculate original scores
        $allAtMarks = StudentAssessmentMark::where('student_id', $student->id)
            ->whereIn('assessment_id', $atAssessments->pluck('id'))
            ->with('assessment')
            ->get()
            ->keyBy('assessment_id');

        $allAtRubricMarks = StudentAssessmentRubricMark::where('student_id', $student->id)
            ->whereHas('rubric.assessment', function($q) {
                $q->where('course_code', 'FYP')->where('evaluator_role', 'lecturer');
            })
            ->with('rubric.assessment')
            ->get();

        $atTotal = 0;
        foreach ($atAssessments as $assessment) {
            $mark = $allAtMarks->get($assessment->id);
            if ($mark && $mark->mark !== null && $mark->max_mark > 0) {
                $atTotal += ($mark->mark / $mark->max_mark) * $assessment->weight_percentage;
            }
        }

        $atRubricTotal = 0;
        foreach ($allAtRubricMarks as $rubricMark) {
            $atRubricTotal += $rubricMark->weighted_contribution;
        }

        $finalScore = $atTotal + $atRubricTotal;

        // Get existing moderation record
        $moderation = FypModerationRecord::where('student_id', $student->id)->first();

        return view('academic.fyp.moderation.show', compact(
            'student',
            'atTotal',
            'atRubricTotal',
            'finalScore',
            'moderation'
        ));
    }

    /**
     * Store moderation adjustments.
     */
    public function store(Request $request, Student $student)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'adjustment_type' => ['required', 'in:percentage,manual_override'],
            'adjustment_percentage' => ['nullable', 'numeric', 'min:-100', 'max:100'],
            'adjusted_at_score' => ['nullable', 'numeric', 'min:0'],
            'adjusted_final_score' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'justification' => ['required', 'string', 'min:10', 'max:1000'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        // Get original scores (same calculation as show method)
        $atAssessments = Assessment::forCourse('FYP')
            ->forEvaluator('lecturer')
            ->active()
            ->get();

        $atRubricAssessments = Assessment::forCourse('FYP')
            ->forEvaluator('lecturer')
            ->active()
            ->whereIn('assessment_type', ['Oral', 'Rubric'])
            ->with('rubrics')
            ->get();

        $allAtMarks = StudentAssessmentMark::where('student_id', $student->id)
            ->whereIn('assessment_id', $atAssessments->pluck('id'))
            ->with('assessment')
            ->get()
            ->keyBy('assessment_id');

        $allAtRubricMarks = StudentAssessmentRubricMark::where('student_id', $student->id)
            ->whereHas('rubric.assessment', function($q) {
                $q->where('course_code', 'FYP')->where('evaluator_role', 'lecturer');
            })
            ->with('rubric.assessment')
            ->get();

        $originalAtTotal = 0;
        foreach ($atAssessments as $assessment) {
            $mark = $allAtMarks->get($assessment->id);
            if ($mark && $mark->mark !== null && $mark->max_mark > 0) {
                $originalAtTotal += ($mark->mark / $mark->max_mark) * $assessment->weight_percentage;
            }
        }

        $originalAtRubricTotal = 0;
        foreach ($allAtRubricMarks as $rubricMark) {
            $originalAtRubricTotal += $rubricMark->weighted_contribution;
        }

        $originalFinalScore = $originalAtTotal + $originalAtRubricTotal;

        // Calculate adjusted scores
        if ($validated['adjustment_type'] === 'percentage') {
            $adjustmentFactor = 1 + ($validated['adjustment_percentage'] / 100);
            $adjustedAtScore = $originalFinalScore * $adjustmentFactor;
            $adjustedFinalScore = $originalFinalScore * $adjustmentFactor;
        } else {
            $adjustedAtScore = $validated['adjusted_at_score'] ?? $originalFinalScore;
            $adjustedFinalScore = $validated['adjusted_final_score'] ?? $originalFinalScore;
        }

        // Create or update moderation record
        $moderation = FypModerationRecord::updateOrCreate(
            ['student_id' => $student->id],
            [
                'original_at_score' => round($originalFinalScore, 2),
                'original_ic_score' => null, // Not used for FYP
                'original_final_score' => round($originalFinalScore, 2),
                'adjusted_at_score' => round($adjustedAtScore, 2),
                'adjusted_ic_score' => null, // Not used for FYP
                'adjusted_final_score' => round($adjustedFinalScore, 2),
                'adjustment_percentage' => $validated['adjustment_percentage'] ?? 0,
                'adjustment_type' => $validated['adjustment_type'],
                'justification' => $validated['justification'],
                'notes' => $validated['notes'] ?? null,
                'moderated_by' => auth()->id(),
            ]
        );

        // Log audit
        FypAuditLog::log(
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

        return redirect()->route('academic.fyp.moderation.index')
            ->with('success', 'Moderation applied successfully.');
    }
}
