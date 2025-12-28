<?php

namespace App\Http\Controllers\Academic\FYP;

use App\Exports\StudentPerformanceExport;
use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Student;
use App\Models\StudentAssessmentMark;
use App\Models\StudentAssessmentRubricMark;
use App\Models\WblGroup;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class FypStudentPerformanceController extends Controller
{
    /**
     * Display student performance overview for FYP (AT evaluation only).
     */
    public function index(Request $request): View
    {
        // Only Admin, AT, and FYP Coordinator can access
        if (! auth()->user()->isAdmin() && ! auth()->user()->isAt() && ! auth()->user()->isFypCoordinator()) {
            abort(403, 'Unauthorized access.');
        }

        // Get active assessments - FYP uses AT (Academic Tutor) as evaluator (stored as 'lecturer' in database)
        $atAssessments = Assessment::forCourse('FYP')
            ->forEvaluator('lecturer')
            ->active()
            ->get();

        $atTotalWeight = $atAssessments->sum('weight_percentage');

        // Get rubric-based assessments
        $atRubricAssessments = Assessment::forCourse('FYP')
            ->forEvaluator('at')
            ->active()
            ->whereIn('assessment_type', ['Oral', 'Rubric'])
            ->with('rubrics')
            ->get();

        $atRubricTotalWeight = $atRubricAssessments->sum(function ($assessment) {
            return $assessment->rubrics->sum('weight_percentage');
        });

        // Build query for students
        $query = Student::with(['group', 'company', 'academicTutor']);

        // Admin can see all students, AT sees only assigned students
        if (auth()->user()->isAt() && ! auth()->user()->isAdmin()) {
            $query->where('at_id', auth()->id());
        }

        // Filter by active groups only
        $query->inActiveGroups();

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('matric_no', 'like', "%{$search}%");
            });
        }

        // Apply programme filter
        if ($request->filled('programme')) {
            $query->where('programme', $request->programme);
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
            ->whereHas('rubric.assessment', function ($q) {
                $q->where('course_code', 'FYP')->where('evaluator_role', 'lecturer');
            })
            ->with('rubric.assessment')
            ->get()
            ->groupBy('student_id');

        // Calculate total rubric questions
        $totalRubricQuestions = $atRubricAssessments->sum(function ($assessment) {
            return $assessment->rubrics->count();
        });

        // Calculate performance for each student
        $studentsWithPerformance = $students->map(function ($student) use (
            $allAtMarks,
            $allAtRubricMarks,
            $atAssessments,
            $totalRubricQuestions,
            $atTotalWeight,
            $atRubricTotalWeight,
            $request
        ) {
            // Calculate AT marks
            $atMarks = $allAtMarks->get($student->id, collect());
            $atMarksByAssessment = $atMarks->keyBy('assessment_id');

            $atTotal = 0;
            $atCompletedCount = 0;
            $atLastUpdated = null;

            foreach ($atAssessments as $assessment) {
                $mark = $atMarksByAssessment->get($assessment->id);
                if ($mark && $mark->mark !== null) {
                    $atCompletedCount++;
                    if ($mark->max_mark > 0) {
                        $atTotal += ($mark->mark / $mark->max_mark) * $assessment->weight_percentage;
                    }
                    if (! $atLastUpdated || $mark->updated_at > $atLastUpdated) {
                        $atLastUpdated = $mark->updated_at;
                    }
                }
            }

            // Calculate AT rubric marks
            $atRubricMarks = $allAtRubricMarks->get($student->id, collect());

            $atRubricTotal = 0;
            $atRubricCompletedCount = $atRubricMarks->count();
            $atRubricLastUpdated = null;

            foreach ($atRubricMarks as $rubricMark) {
                $atRubricTotal += $rubricMark->weighted_contribution;
                if (! $atRubricLastUpdated || $rubricMark->updated_at > $atRubricLastUpdated) {
                    $atRubricLastUpdated = $rubricMark->updated_at;
                }
            }

            // Calculate final score
            $finalScore = $atTotal + $atRubricTotal;

            // Determine overall status
            $atStatus = $atCompletedCount == 0 ? 'not_started' :
                        ($atCompletedCount < $atAssessments->count() ? 'in_progress' : 'completed');
            $atRubricStatus = $atRubricCompletedCount == 0 ? 'not_started' :
                             ($atRubricCompletedCount < $totalRubricQuestions ? 'in_progress' : 'completed');

            if ($atStatus == 'not_started' && $atRubricStatus == 'not_started') {
                $overallStatus = 'not_started';
                $overallStatusLabel = 'Not Started';
            } elseif ($atStatus == 'completed' && ($totalRubricQuestions == 0 || $atRubricStatus == 'completed')) {
                $overallStatus = 'completed';
                $overallStatusLabel = 'Completed';
            } else {
                $overallStatus = 'in_progress';
                $overallStatusLabel = 'In Progress';
            }

            // Apply status filter
            $statusFilter = $request->input('status');
            if ($statusFilter && $overallStatus !== $statusFilter) {
                return null;
            }

            // Get last updated timestamp
            $lastUpdated = null;
            if ($atLastUpdated && $atRubricLastUpdated) {
                $lastUpdated = $atLastUpdated > $atRubricLastUpdated ? $atLastUpdated : $atRubricLastUpdated;
            } elseif ($atLastUpdated) {
                $lastUpdated = $atLastUpdated;
            } elseif ($atRubricLastUpdated) {
                $lastUpdated = $atRubricLastUpdated;
            }

            $student->at_score = round($atTotal + $atRubricTotal, 2);
            $student->final_score = round($finalScore, 2);
            $student->overall_status = $overallStatus;
            $student->overall_status_label = $overallStatusLabel;
            $student->at_status = $atStatus;
            $student->last_updated = $lastUpdated;
            $student->at_progress = ($atTotalWeight + $atRubricTotalWeight) > 0 ? (($atTotal + $atRubricTotal) / ($atTotalWeight + $atRubricTotalWeight)) * 100 : 0;
            $student->overall_progress = ($finalScore / 100) * 100;

            return $student;
        })->filter();

        // Get groups for filter dropdown
        $groups = WblGroup::where('status', 'ACTIVE')->orderBy('name')->get();

        // Get unique programmes for filter
        $programmes = Student::distinct()->orderBy('programme')->pluck('programme')->filter();

        return view('academic.fyp.performance.index', compact(
            'studentsWithPerformance',
            'atTotalWeight',
            'atRubricTotalWeight',
            'groups',
            'programmes'
        ));
    }

    /**
     * Export student performance to Excel.
     */
    public function exportExcel(Request $request)
    {
        if (! auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $studentsWithPerformance = $this->getFilteredStudents($request);

        if ($studentsWithPerformance->isEmpty()) {
            return redirect()->back()
                ->with('error', 'No students found to export. Please adjust your filters.');
        }

        Log::info('FYP Student Performance Excel Export', [
            'admin_id' => auth()->id(),
            'admin_name' => auth()->user()->name,
            'exported_count' => $studentsWithPerformance->count(),
            'filters' => $request->only(['search', 'programme', 'group', 'status']),
        ]);

        $fileName = 'FYP_Student_Performance_'.now()->format('Y-m-d_His').'.xlsx';

        return Excel::download(new StudentPerformanceExport($studentsWithPerformance), $fileName);
    }

    /**
     * Export student performance to PDF.
     */
    public function exportPdf(Request $request)
    {
        if (! auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $studentsWithPerformance = $this->getFilteredStudents($request);

        if ($studentsWithPerformance->isEmpty()) {
            return redirect()->back()
                ->with('error', 'No students found to export. Please adjust your filters.');
        }

        Log::info('FYP Student Performance PDF Export', [
            'admin_id' => auth()->id(),
            'admin_name' => auth()->user()->name,
            'exported_count' => $studentsWithPerformance->count(),
            'filters' => $request->only(['search', 'programme', 'group', 'status']),
        ]);

        $atTotalWeight = Assessment::forCourse('FYP')
            ->forEvaluator('lecturer')
            ->active()
            ->sum('weight_percentage');

        $atRubricTotalWeight = Assessment::forCourse('FYP')
            ->forEvaluator('lecturer')
            ->active()
            ->whereIn('assessment_type', ['Oral', 'Rubric'])
            ->with('rubrics')
            ->get()
            ->sum(function ($assessment) {
                return $assessment->rubrics->sum('weight_percentage');
            });

        $pdf = Pdf::loadView('academic.fyp.performance.export-pdf', [
            'students' => $studentsWithPerformance,
            'atTotalWeight' => $atTotalWeight,
            'atRubricTotalWeight' => $atRubricTotalWeight,
            'adminName' => auth()->user()->name,
            'generatedAt' => now(),
        ])->setPaper('a4', 'landscape')
            ->setOption('margin-top', 30)
            ->setOption('margin-bottom', 30)
            ->setOption('margin-left', 25)
            ->setOption('margin-right', 25)
            ->setOption('enable-local-file-access', true);

        $fileName = 'FYP_Student_Performance_'.now()->format('Y-m-d_His').'.pdf';

        return $pdf->download($fileName);
    }

    /**
     * Get filtered students with performance data.
     */
    private function getFilteredStudents(Request $request)
    {
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

        $atTotalWeight = $atAssessments->sum('weight_percentage');
        $atRubricTotalWeight = $atRubricAssessments->sum(function ($assessment) {
            return $assessment->rubrics->sum('weight_percentage');
        });

        $query = Student::with(['group', 'company', 'academicTutor']);
        $query->inActiveGroups();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('matric_no', 'like', "%{$search}%");
            });
        }

        if ($request->filled('programme')) {
            $query->where('programme', $request->programme);
        }

        if ($request->filled('group')) {
            $query->where('group_id', $request->group);
        }

        $students = $query->orderBy('name')->get();

        $allAtMarks = StudentAssessmentMark::whereIn('student_id', $students->pluck('id'))
            ->whereIn('assessment_id', $atAssessments->pluck('id'))
            ->with('assessment')
            ->get()
            ->groupBy('student_id');

        $allAtRubricMarks = StudentAssessmentRubricMark::whereIn('student_id', $students->pluck('id'))
            ->whereHas('rubric.assessment', function ($q) {
                $q->where('course_code', 'FYP')->where('evaluator_role', 'lecturer');
            })
            ->with('rubric.assessment')
            ->get()
            ->groupBy('student_id');

        $totalRubricQuestions = $atRubricAssessments->sum(function ($assessment) {
            return $assessment->rubrics->count();
        });

        return $students->map(function ($student) use (
            $allAtMarks,
            $allAtRubricMarks,
            $atAssessments,
            $totalRubricQuestions,
            $request
        ) {
            $atMarks = $allAtMarks->get($student->id, collect());
            $atMarksByAssessment = $atMarks->keyBy('assessment_id');

            $atTotal = 0;
            $atCompletedCount = 0;

            foreach ($atAssessments as $assessment) {
                $mark = $atMarksByAssessment->get($assessment->id);
                if ($mark && $mark->mark !== null) {
                    $atCompletedCount++;
                    if ($mark->max_mark > 0) {
                        $atTotal += ($mark->mark / $mark->max_mark) * $assessment->weight_percentage;
                    }
                }
            }

            $atRubricMarks = $allAtRubricMarks->get($student->id, collect());
            $atRubricTotal = 0;
            $atRubricCompletedCount = $atRubricMarks->count();

            foreach ($atRubricMarks as $rubricMark) {
                $atRubricTotal += $rubricMark->weighted_contribution;
            }

            $finalScore = $atTotal + $atRubricTotal;

            $atStatus = $atCompletedCount == 0 ? 'not_started' :
                        ($atCompletedCount < $atAssessments->count() ? 'in_progress' : 'completed');
            $atRubricStatus = $atRubricCompletedCount == 0 ? 'not_started' :
                             ($atRubricCompletedCount < $totalRubricQuestions ? 'in_progress' : 'completed');

            if ($atStatus == 'not_started' && $atRubricStatus == 'not_started') {
                $overallStatus = 'not_started';
            } elseif ($atStatus == 'completed' && ($totalRubricQuestions == 0 || $atRubricStatus == 'completed')) {
                $overallStatus = 'completed';
            } else {
                $overallStatus = 'in_progress';
            }

            $statusFilter = $request->input('status');
            if ($statusFilter && $overallStatus !== $statusFilter) {
                return null;
            }

            $student->at_score = round($atTotal + $atRubricTotal, 2);
            $student->final_score = round($finalScore, 2);
            $student->overall_status = $overallStatus;

            return $student;
        })->filter();
    }
}
