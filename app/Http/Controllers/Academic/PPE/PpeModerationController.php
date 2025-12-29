<?php

namespace App\Http\Controllers\Academic\PPE;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\PPE\PpeAuditLog;
use App\Models\PPE\PpeModerationRecord;
use App\Models\Student;
use App\Models\StudentAssessmentMark;
use App\Models\StudentAssessmentRubricMark;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PpeModerationController extends Controller
{
    /**
     * Display moderation overview page.
     */
    public function index(Request $request): View
    {
        if (! auth()->user()->isAdmin() && ! auth()->user()->isPpeCoordinator()) {
            abort(403, 'Unauthorized access.');
        }

        // Get active assessments
        $lecturerAssessments = Assessment::forCourse('PPE')
            ->forEvaluator('lecturer')
            ->active()
            ->get();

        $icAssessments = Assessment::forCourse('PPE')
            ->forEvaluator('ic')
            ->active()
            ->whereIn('assessment_type', ['Oral', 'Rubric'])
            ->with('rubrics')
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

        // Get all lecturer marks
        $allLecturerMarks = StudentAssessmentMark::whereIn('student_id', $students->pluck('id'))
            ->whereIn('assessment_id', $lecturerAssessments->pluck('id'))
            ->with('assessment')
            ->get()
            ->groupBy('student_id');

        // Get all IC rubric marks
        $allIcRubricMarks = StudentAssessmentRubricMark::whereIn('student_id', $students->pluck('id'))
            ->with('rubric.assessment')
            ->get()
            ->groupBy('student_id');

        // Get moderation records
        $moderationRecords = PpeModerationRecord::whereIn('student_id', $students->pluck('id'))
            ->with(['student', 'moderator'])
            ->get()
            ->keyBy('student_id');

        // Calculate scores for each student
        $studentsWithScores = $students->map(function ($student) use (
            $allLecturerMarks,
            $allIcRubricMarks,
            $lecturerAssessments,
            $moderationRecords
        ) {
            // Calculate Lecturer marks (40%)
            $lecturerMarks = $allLecturerMarks->get($student->id, collect());
            $lecturerMarksByAssessment = $lecturerMarks->keyBy('assessment_id');

            $lecturerTotal = 0;
            foreach ($lecturerAssessments as $assessment) {
                $mark = $lecturerMarksByAssessment->get($assessment->id);
                if ($mark && $mark->mark !== null && $mark->max_mark > 0) {
                    $lecturerTotal += ($mark->mark / $mark->max_mark) * $assessment->weight_percentage;
                }
            }

            // Calculate IC marks (60%)
            $icRubricMarks = $allIcRubricMarks->get($student->id, collect());
            $icTotal = 0;
            foreach ($icRubricMarks as $rubricMark) {
                $icTotal += $rubricMark->weighted_contribution;
            }

            // Calculate final score
            $finalScore = $lecturerTotal + $icTotal;

            // Check if moderated
            $moderation = $moderationRecords->get($student->id);

            $student->lecturer_score = round($lecturerTotal, 2);
            $student->ic_score = round($icTotal, 2);
            $student->final_score = round($finalScore, 2);
            $student->moderation = $moderation;

            return $student;
        });

        // Get groups for filter
        $groups = \App\Models\WblGroup::orderBy('name')->get();

        return view('academic.ppe.moderation.index', compact(
            'studentsWithScores',
            'groups'
        ));
    }

    /**
     * Show moderation form for a specific student.
     */
    public function show(Student $student): View
    {
        if (! auth()->user()->isAdmin() && ! auth()->user()->isPpeCoordinator()) {
            abort(403, 'Unauthorized access.');
        }

        // Get active assessments
        $lecturerAssessments = Assessment::forCourse('PPE')
            ->forEvaluator('lecturer')
            ->active()
            ->get();

        $icAssessments = Assessment::forCourse('PPE')
            ->forEvaluator('ic')
            ->active()
            ->whereIn('assessment_type', ['Oral', 'Rubric'])
            ->with('rubrics')
            ->get();

        // Calculate original scores
        $allLecturerMarks = StudentAssessmentMark::where('student_id', $student->id)
            ->whereIn('assessment_id', $lecturerAssessments->pluck('id'))
            ->with('assessment')
            ->get()
            ->keyBy('assessment_id');

        $allIcRubricMarks = StudentAssessmentRubricMark::where('student_id', $student->id)
            ->with('rubric.assessment')
            ->get();

        $lecturerTotal = 0;
        foreach ($lecturerAssessments as $assessment) {
            $mark = $allLecturerMarks->get($assessment->id);
            if ($mark && $mark->mark !== null && $mark->max_mark > 0) {
                $lecturerTotal += ($mark->mark / $mark->max_mark) * $assessment->weight_percentage;
            }
        }

        $icTotal = 0;
        foreach ($allIcRubricMarks as $rubricMark) {
            $icTotal += $rubricMark->weighted_contribution;
        }

        $finalScore = $lecturerTotal + $icTotal;

        // Get existing moderation record
        $moderation = PpeModerationRecord::where('student_id', $student->id)->first();

        return view('academic.ppe.moderation.show', compact(
            'student',
            'lecturerTotal',
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
        if (! auth()->user()->isAdmin() && ! auth()->user()->isPpeCoordinator()) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'adjustment_type' => ['required', 'in:percentage,manual_override'],
            'adjustment_percentage' => ['nullable', 'numeric', 'min:-100', 'max:100'],
            'adjusted_lecturer_score' => ['nullable', 'numeric', 'min:0', 'max:40'],
            'adjusted_ic_score' => ['nullable', 'numeric', 'min:0', 'max:60'],
            'adjusted_final_score' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'justification' => ['required', 'string', 'min:10', 'max:1000'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        // Get original scores (same calculation as show method)
        $lecturerAssessments = Assessment::forCourse('PPE')
            ->forEvaluator('lecturer')
            ->active()
            ->get();

        $icAssessments = Assessment::forCourse('PPE')
            ->forEvaluator('ic')
            ->active()
            ->whereIn('assessment_type', ['Oral', 'Rubric'])
            ->with('rubrics')
            ->get();

        $allLecturerMarks = StudentAssessmentMark::where('student_id', $student->id)
            ->whereIn('assessment_id', $lecturerAssessments->pluck('id'))
            ->with('assessment')
            ->get()
            ->keyBy('assessment_id');

        $allIcRubricMarks = StudentAssessmentRubricMark::where('student_id', $student->id)
            ->with('rubric.assessment')
            ->get();

        $originalLecturerTotal = 0;
        foreach ($lecturerAssessments as $assessment) {
            $mark = $allLecturerMarks->get($assessment->id);
            if ($mark && $mark->mark !== null && $mark->max_mark > 0) {
                $originalLecturerTotal += ($mark->mark / $mark->max_mark) * $assessment->weight_percentage;
            }
        }

        $originalIcTotal = 0;
        foreach ($allIcRubricMarks as $rubricMark) {
            $originalIcTotal += $rubricMark->weighted_contribution;
        }

        $originalFinalScore = $originalLecturerTotal + $originalIcTotal;

        // Calculate adjusted scores
        if ($validated['adjustment_type'] === 'percentage') {
            $adjustmentFactor = 1 + ($validated['adjustment_percentage'] / 100);
            $adjustedLecturerScore = $originalLecturerTotal * $adjustmentFactor;
            $adjustedIcScore = $originalIcTotal * $adjustmentFactor;
            $adjustedFinalScore = $originalFinalScore * $adjustmentFactor;
        } else {
            $adjustedLecturerScore = $validated['adjusted_lecturer_score'] ?? $originalLecturerTotal;
            $adjustedIcScore = $validated['adjusted_ic_score'] ?? $originalIcTotal;
            $adjustedFinalScore = $validated['adjusted_final_score'] ?? $originalFinalScore;
        }

        // Create or update moderation record
        $moderation = PpeModerationRecord::updateOrCreate(
            ['student_id' => $student->id],
            [
                'original_lecturer_score' => round($originalLecturerTotal, 2),
                'original_ic_score' => round($originalIcTotal, 2),
                'original_final_score' => round($originalFinalScore, 2),
                'adjusted_lecturer_score' => round($adjustedLecturerScore, 2),
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
        PpeAuditLog::log(
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

        return redirect()->route('academic.ppe.moderation.index')
            ->with('success', 'Moderation applied successfully.');
    }
}
