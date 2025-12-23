<?php

namespace App\Http\Controllers\Academic\FYP;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\FYP\FypAuditLog;
use App\Models\Student;
use App\Models\StudentAssessmentComponentMark;
use App\Models\StudentAssessmentMark;
use App\Models\WblGroup;
use App\Traits\ChecksAssessmentWindow;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class FypAtEvaluationController extends Controller
{
    use ChecksAssessmentWindow;

    /**
     * Display the list of students for AT (Academic Tutor) evaluation.
     */
    public function index(Request $request): View
    {
        // Only Admin and AT can access AT evaluation
        if (! auth()->user()->isAdmin() && ! auth()->user()->isAt()) {
            abort(403, 'Unauthorized access.');
        }

        // Get active assessments for FYP course with AT evaluator role
        $assessments = Assessment::forCourse('FYP')
            ->forEvaluator('at')
            ->active()
            ->with('components')
            ->get();

        $totalWeight = $assessments->sum('weight_percentage');

        // Build query for students
        $query = Student::with(['group', 'company', 'academicTutor', 'industryCoach']);

        // Admin can see all students, AT only sees assigned students
        if (auth()->user()->isAt() && ! auth()->user()->isAdmin()) {
            $query->where('at_id', auth()->id());
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

            $totalMarks = 0;
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

        return view('academic.fyp.lecturer.index', compact(
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
        // Load relationships for display
        $student->load('academicTutor', 'industryCoach', 'group');

        // Check authorization: Admin or assigned AT
        if (! auth()->user()->isAdmin()) {
            if (auth()->user()->isAt()) {
                if ($student->at_id !== auth()->id()) {
                    abort(403, 'You are not authorized to edit AT marks for this student. This student is assigned to a different AT.');
                }
            } else {
                abort(403, 'Unauthorized access.');
            }
        }

        // Get active assessments for FYP course with AT evaluator role
        $assessments = Assessment::forCourse('FYP')
            ->forEvaluator('at')
            ->active()
            ->with('components')
            ->get();

        // Get existing marks for this student
        $marks = StudentAssessmentMark::where('student_id', $student->id)
            ->whereIn('assessment_id', $assessments->pluck('id'))
            ->get()
            ->keyBy('assessment_id');

        // Define the phase order for FYP assessments
        $phaseOrder = [
            'Mid-Term Report' => 1,
            'Mid-Term Oral Presentation' => 2,
            'Final Report' => 3,
            'Final Oral Presentation' => 4,
            'Progress Logbook' => 5,
        ];

        // Group assessments by phase based on assessment name
        $assessmentsByPhase = collect([
            'Mid-Term' => collect(),
            'Final' => collect(),
            'Progress' => collect(),
        ]);

        foreach ($assessments as $assessment) {
            $name = $assessment->assessment_name;
            if (str_contains($name, 'Mid-Term')) {
                $assessmentsByPhase['Mid-Term']->push($assessment);
            } elseif (str_contains($name, 'Final')) {
                $assessmentsByPhase['Final']->push($assessment);
            } elseif (str_contains($name, 'Progress') || str_contains($name, 'Logbook')) {
                $assessmentsByPhase['Progress']->push($assessment);
            } else {
                // Default to Progress for any other assessments
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

        // Calculate total contribution
        $totalContribution = 0;
        foreach ($marks as $mark) {
            if ($mark->mark !== null && $mark->max_mark > 0) {
                $totalContribution += ($mark->mark / $mark->max_mark) * $mark->assessment->weight_percentage;
            }
        }

        // Calculate total AT weight
        $totalAtWeight = $assessments->sum('weight_percentage');

        return view('academic.fyp.lecturer.show', compact(
            'student',
            'assessments',
            'assessmentsByPhase',
            'groupedAssessments',
            'marks',
            'totalContribution',
            'totalAtWeight'
        ));
    }

    /**
     * Store or update marks for a student.
     */
    public function store(Request $request, Student $student): RedirectResponse
    {
        // Check authorization
        if (! auth()->user()->isAdmin()) {
            if (auth()->user()->isAt()) {
                if ($student->at_id !== auth()->id()) {
                    abort(403, 'You are not authorized to edit AT marks for this student.');
                }
            } else {
                abort(403, 'Unauthorized access.');
            }
        }

        // Check if assessment window is open (Admin can bypass)
        if (! auth()->user()->isAdmin()) {
            $this->requireOpenWindow('at');
        }

        $validated = $request->validate([
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

        $updatedAssessments = [];
        $assessmentDetails = [];

        // Handle component-based marking (new approach)
        if ($request->has('component_marks') && $request->filled('assessment_id')) {
            $assessmentId = $validated['assessment_id'];
            $assessment = Assessment::with('components')->findOrFail($assessmentId);

            // Validate assessment belongs to FYP and AT role
            if ($assessment->course_code !== 'FYP' || $assessment->evaluator_role !== 'at') {
                return redirect()->back()
                    ->with('error', 'Invalid assessment.')
                    ->withInput();
            }

            DB::transaction(function () use ($student, $assessment, $validated) {
                $totalWeightedScore = 0;
                $totalWeight = 0;
                $componentDetails = [];

                foreach ($validated['component_marks'] as $componentId => $rubricScore) {
                    // Handle temporary component IDs (for components without database entries)
                    if (str_starts_with($componentId, 'temp_')) {
                        continue; // Skip temporary components
                    }

                    $component = $assessment->components->find($componentId);
                    if (! $component) {
                        continue;
                    }

                    $componentRemarks = $validated['component_remarks'][$componentId] ?? null;

                    // Save component mark
                    StudentAssessmentComponentMark::updateOrCreate(
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

                    $componentDetails[] = [
                        'component_id' => $componentId,
                        'component_name' => $component->component_name,
                        'rubric_score' => $rubricScore,
                        'weight' => $componentWeight,
                        'contribution' => $weightedScore,
                    ];
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

                $updatedAssessments[] = $assessment->assessment_name;
                $assessmentDetails[] = [
                    'assessment_id' => $assessment->id,
                    'assessment_name' => $assessment->assessment_name,
                    'overall_mark' => $overallMark,
                    'components' => $componentDetails,
                ];
            });
        } else {
            // Handle legacy single mark approach (for backward compatibility)
            if ($request->has('marks')) {
                foreach ($validated['marks'] as $assessmentId => $mark) {
                    $assessment = Assessment::findOrFail($assessmentId);

                    // Validate assessment belongs to FYP and AT role
                    if ($assessment->course_code !== 'FYP' || $assessment->evaluator_role !== 'at') {
                        continue; // Skip invalid assessments
                    }

                    $maxMark = $validated['max_marks'][$assessmentId] ?? 100;
                    $remarks = $validated['remarks'][$assessmentId] ?? null;

                    // Validate mark doesn't exceed max_mark
                    if ($mark !== null && $maxMark > 0 && $mark > $maxMark) {
                        return redirect()->back()
                            ->with('error', "Mark for {$assessment->assessment_name} cannot exceed {$maxMark}.")
                            ->withInput();
                    }

                    // Get existing mark for audit log
                    $existingMark = StudentAssessmentMark::where('student_id', $student->id)
                        ->where('assessment_id', $assessmentId)
                        ->first();

                    $oldMark = $existingMark?->mark;
                    $isNew = $existingMark === null;

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

                    $updatedAssessments[] = $assessment->assessment_name;
                    $assessmentDetails[] = [
                        'assessment_id' => $assessmentId,
                        'assessment_name' => $assessment->assessment_name,
                        'old_mark' => $oldMark,
                        'new_mark' => $mark ?: null,
                        'max_mark' => $maxMark,
                        'is_new' => $isNew,
                    ];
                }
            }
        }

        // Log audit entry
        if (! empty($updatedAssessments)) {
            $action = count($updatedAssessments) === 1 ? 'mark_updated' : 'marks_updated';
            $description = count($updatedAssessments) === 1
                ? "AT evaluation mark updated for {$student->name} ({$student->matric_no}) - {$updatedAssessments[0]}"
                : "AT evaluation marks updated for {$student->name} ({$student->matric_no}) - ".count($updatedAssessments).' assessment(s)';

            FypAuditLog::log(
                $action,
                'evaluation',
                $description,
                [
                    'student_id' => $student->id,
                    'student_name' => $student->name,
                    'student_matric_no' => $student->matric_no,
                    'assessments' => $updatedAssessments,
                    'assessment_details' => $assessmentDetails,
                ],
                $student->id
            );
        }

        return redirect()->route('academic.fyp.lecturer.show', $student)
            ->with('success', 'Marks saved successfully.');
    }
}
