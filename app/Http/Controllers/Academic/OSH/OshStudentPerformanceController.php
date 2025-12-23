<?php

namespace App\Http\Controllers\Academic\OSH;

use App\Exports\StudentPerformanceExport;
use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\CourseSetting;
use App\Models\Student;
use App\Models\StudentAssessmentMark;
use App\Models\StudentAssessmentRubricMark;
use App\Models\WblGroup;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class OshStudentPerformanceController extends Controller
{
    /**
     * Display student performance overview for OSH (Lecturer evaluation only).
     */
    public function index(Request $request): View
    {
        // Only Admin and Lecturer can access
        if (! auth()->user()->isAdmin() && ! auth()->user()->isLecturer()) {
            abort(403, 'Unauthorized access.');
        }

        // Get active assessments - OSH uses Lecturer as evaluator
        $lecturerAssessments = Assessment::forCourse('OSH')
            ->forEvaluator('lecturer')
            ->active()
            ->get();

        $lecturerTotalWeight = $lecturerAssessments->sum('weight_percentage');

        // Get rubric-based assessments
        $lecturerRubricAssessments = Assessment::forCourse('OSH')
            ->forEvaluator('lecturer')
            ->active()
            ->whereIn('assessment_type', ['Oral', 'Rubric'])
            ->with('rubrics')
            ->get();

        $lecturerRubricTotalWeight = $lecturerRubricAssessments->sum(function ($assessment) {
            return $assessment->rubrics->sum('weight_percentage');
        });

        // Build query for students
        $query = Student::with(['group', 'company', 'academicTutor']);

        // Admin can see all students, Lecturer sees only students if they are the assigned OSH lecturer
        if (auth()->user()->isLecturer() && ! auth()->user()->isAdmin()) {
            // OSH uses single lecturer from course_settings
            $oshSetting = CourseSetting::where('course_type', 'OSH')->first();
            if ($oshSetting && $oshSetting->lecturer_id === auth()->id()) {
                // This lecturer is assigned to OSH, show all students
                // No additional filter needed
            } else {
                // This lecturer is not assigned to OSH, show no students
                $query->whereRaw('1 = 0'); // Force empty result
            }
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

        // Get all lecturer marks
        $allLecturerMarks = StudentAssessmentMark::whereIn('student_id', $students->pluck('id'))
            ->whereIn('assessment_id', $lecturerAssessments->pluck('id'))
            ->with('assessment')
            ->get()
            ->groupBy('student_id');

        // Get all lecturer rubric marks
        $allLecturerRubricMarks = StudentAssessmentRubricMark::whereIn('student_id', $students->pluck('id'))
            ->whereHas('rubric.assessment', function ($q) {
                $q->where('course_code', 'OSH')->where('evaluator_role', 'lecturer');
            })
            ->with('rubric.assessment')
            ->get()
            ->groupBy('student_id');

        // Calculate total rubric questions
        $totalRubricQuestions = $lecturerRubricAssessments->sum(function ($assessment) {
            return $assessment->rubrics->count();
        });

        // Calculate performance for each student
        $studentsWithPerformance = $students->map(function ($student) use (
            $allLecturerMarks,
            $allLecturerRubricMarks,
            $lecturerAssessments,
            $totalRubricQuestions,
            $lecturerTotalWeight,
            $lecturerRubricTotalWeight,
            $request
        ) {
            // Calculate Lecturer marks
            $lecturerMarks = $allLecturerMarks->get($student->id, collect());
            $lecturerMarksByAssessment = $lecturerMarks->keyBy('assessment_id');

            $lecturerTotal = 0;
            $lecturerCompletedCount = 0;
            $lecturerLastUpdated = null;

            foreach ($lecturerAssessments as $assessment) {
                $mark = $lecturerMarksByAssessment->get($assessment->id);
                if ($mark && $mark->mark !== null) {
                    $lecturerCompletedCount++;
                    if ($mark->max_mark > 0) {
                        $lecturerTotal += ($mark->mark / $mark->max_mark) * $assessment->weight_percentage;
                    }
                    if (! $lecturerLastUpdated || $mark->updated_at > $lecturerLastUpdated) {
                        $lecturerLastUpdated = $mark->updated_at;
                    }
                }
            }

            // Calculate Lecturer rubric marks
            $lecturerRubricMarks = $allLecturerRubricMarks->get($student->id, collect());

            $lecturerRubricTotal = 0;
            $lecturerRubricCompletedCount = $lecturerRubricMarks->count();
            $lecturerRubricLastUpdated = null;

            foreach ($lecturerRubricMarks as $rubricMark) {
                $lecturerRubricTotal += $rubricMark->weighted_contribution;
                if (! $lecturerRubricLastUpdated || $rubricMark->updated_at > $lecturerRubricLastUpdated) {
                    $lecturerRubricLastUpdated = $rubricMark->updated_at;
                }
            }

            // Calculate final score
            $finalScore = $lecturerTotal + $lecturerRubricTotal;

            // Determine overall status
            $lecturerStatus = $lecturerCompletedCount == 0 ? 'not_started' :
                            ($lecturerCompletedCount < $lecturerAssessments->count() ? 'in_progress' : 'completed');
            $lecturerRubricStatus = $lecturerRubricCompletedCount == 0 ? 'not_started' :
                                   ($lecturerRubricCompletedCount < $totalRubricQuestions ? 'in_progress' : 'completed');

            if ($lecturerStatus == 'not_started' && $lecturerRubricStatus == 'not_started') {
                $overallStatus = 'not_started';
                $overallStatusLabel = 'Not Started';
            } elseif ($lecturerStatus == 'completed' && ($totalRubricQuestions == 0 || $lecturerRubricStatus == 'completed')) {
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
            if ($lecturerLastUpdated && $lecturerRubricLastUpdated) {
                $lastUpdated = $lecturerLastUpdated > $lecturerRubricLastUpdated ? $lecturerLastUpdated : $lecturerRubricLastUpdated;
            } elseif ($lecturerLastUpdated) {
                $lastUpdated = $lecturerLastUpdated;
            } elseif ($lecturerRubricLastUpdated) {
                $lastUpdated = $lecturerRubricLastUpdated;
            }

            $student->lecturer_score = round($lecturerTotal + $lecturerRubricTotal, 2);
            $student->final_score = round($finalScore, 2);
            $student->overall_status = $overallStatus;
            $student->overall_status_label = $overallStatusLabel;
            $student->lecturer_status = $lecturerStatus;
            $student->last_updated = $lastUpdated;
            $student->lecturer_progress = ($lecturerTotalWeight + $lecturerRubricTotalWeight) > 0 ? (($lecturerTotal + $lecturerRubricTotal) / ($lecturerTotalWeight + $lecturerRubricTotalWeight)) * 100 : 0;
            $student->overall_progress = ($finalScore / 100) * 100;

            return $student;
        })->filter();

        // Get groups for filter dropdown
        $groups = WblGroup::where('status', 'ACTIVE')->orderBy('name')->get();

        // Get unique programmes for filter
        $programmes = Student::distinct()->orderBy('programme')->pluck('programme')->filter();

        return view('academic.osh.performance.index', compact(
            'studentsWithPerformance',
            'lecturerTotalWeight',
            'lecturerRubricTotalWeight',
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

        Log::info('OSH Student Performance Excel Export', [
            'admin_id' => auth()->id(),
            'admin_name' => auth()->user()->name,
            'exported_count' => $studentsWithPerformance->count(),
            'filters' => $request->only(['search', 'programme', 'group', 'status']),
        ]);

        $fileName = 'OSH_Student_Performance_'.now()->format('Y-m-d_His').'.xlsx';

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

        Log::info('OSH Student Performance PDF Export', [
            'admin_id' => auth()->id(),
            'admin_name' => auth()->user()->name,
            'exported_count' => $studentsWithPerformance->count(),
            'filters' => $request->only(['search', 'programme', 'group', 'status']),
        ]);

        $lecturerTotalWeight = Assessment::forCourse('OSH')
            ->forEvaluator('lecturer')
            ->active()
            ->sum('weight_percentage');

        $lecturerRubricTotalWeight = Assessment::forCourse('OSH')
            ->forEvaluator('lecturer')
            ->active()
            ->whereIn('assessment_type', ['Oral', 'Rubric'])
            ->with('rubrics')
            ->get()
            ->sum(function ($assessment) {
                return $assessment->rubrics->sum('weight_percentage');
            });

        $pdf = Pdf::loadView('academic.osh.performance.export-pdf', [
            'students' => $studentsWithPerformance,
            'lecturerTotalWeight' => $lecturerTotalWeight,
            'lecturerRubricTotalWeight' => $lecturerRubricTotalWeight,
            'adminName' => auth()->user()->name,
            'generatedAt' => now(),
        ])->setPaper('a4', 'landscape')
            ->setOption('margin-top', 30)
            ->setOption('margin-bottom', 30)
            ->setOption('margin-left', 25)
            ->setOption('margin-right', 25)
            ->setOption('enable-local-file-access', true);

        $fileName = 'OSH_Student_Performance_'.now()->format('Y-m-d_His').'.pdf';

        return $pdf->download($fileName);
    }

    /**
     * Get filtered students with performance data.
     */
    private function getFilteredStudents(Request $request)
    {
        $lecturerAssessments = Assessment::forCourse('OSH')
            ->forEvaluator('lecturer')
            ->active()
            ->get();

        $lecturerRubricAssessments = Assessment::forCourse('OSH')
            ->forEvaluator('lecturer')
            ->active()
            ->whereIn('assessment_type', ['Oral', 'Rubric'])
            ->with('rubrics')
            ->get();

        $lecturerTotalWeight = $lecturerAssessments->sum('weight_percentage');
        $lecturerRubricTotalWeight = $lecturerRubricAssessments->sum(function ($assessment) {
            return $assessment->rubrics->sum('weight_percentage');
        });

        $query = Student::with(['group', 'company', 'academicTutor']);
        $query->inActiveGroups();

        if (auth()->user()->isLecturer() && ! auth()->user()->isAdmin()) {
            $oshSetting = CourseSetting::where('course_type', 'OSH')->first();
            if ($oshSetting && $oshSetting->lecturer_id === auth()->id()) {
                // This lecturer is assigned to OSH
            } else {
                $query->whereRaw('1 = 0');
            }
        }

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

        $allLecturerMarks = StudentAssessmentMark::whereIn('student_id', $students->pluck('id'))
            ->whereIn('assessment_id', $lecturerAssessments->pluck('id'))
            ->with('assessment')
            ->get()
            ->groupBy('student_id');

        $allLecturerRubricMarks = StudentAssessmentRubricMark::whereIn('student_id', $students->pluck('id'))
            ->whereHas('rubric.assessment', function ($q) {
                $q->where('course_code', 'OSH')->where('evaluator_role', 'lecturer');
            })
            ->with('rubric.assessment')
            ->get()
            ->groupBy('student_id');

        $totalRubricQuestions = $lecturerRubricAssessments->sum(function ($assessment) {
            return $assessment->rubrics->count();
        });

        return $students->map(function ($student) use (
            $allLecturerMarks,
            $allLecturerRubricMarks,
            $lecturerAssessments,
            $totalRubricQuestions,
            $request
        ) {
            $lecturerMarks = $allLecturerMarks->get($student->id, collect());
            $lecturerMarksByAssessment = $lecturerMarks->keyBy('assessment_id');

            $lecturerTotal = 0;
            $lecturerCompletedCount = 0;

            foreach ($lecturerAssessments as $assessment) {
                $mark = $lecturerMarksByAssessment->get($assessment->id);
                if ($mark && $mark->mark !== null) {
                    $lecturerCompletedCount++;
                    if ($mark->max_mark > 0) {
                        $lecturerTotal += ($mark->mark / $mark->max_mark) * $assessment->weight_percentage;
                    }
                }
            }

            $lecturerRubricMarks = $allLecturerRubricMarks->get($student->id, collect());
            $lecturerRubricTotal = 0;
            $lecturerRubricCompletedCount = $lecturerRubricMarks->count();

            foreach ($lecturerRubricMarks as $rubricMark) {
                $lecturerRubricTotal += $rubricMark->weighted_contribution;
            }

            $finalScore = $lecturerTotal + $lecturerRubricTotal;

            $lecturerStatus = $lecturerCompletedCount == 0 ? 'not_started' :
                            ($lecturerCompletedCount < $lecturerAssessments->count() ? 'in_progress' : 'completed');
            $lecturerRubricStatus = $lecturerRubricCompletedCount == 0 ? 'not_started' :
                                   ($lecturerRubricCompletedCount < $totalRubricQuestions ? 'in_progress' : 'completed');

            if ($lecturerStatus == 'not_started' && $lecturerRubricStatus == 'not_started') {
                $overallStatus = 'not_started';
            } elseif ($lecturerStatus == 'completed' && ($totalRubricQuestions == 0 || $lecturerRubricStatus == 'completed')) {
                $overallStatus = 'completed';
            } else {
                $overallStatus = 'in_progress';
            }

            $statusFilter = $request->input('status');
            if ($statusFilter && $overallStatus !== $statusFilter) {
                return null;
            }

            $student->lecturer_score = round($lecturerTotal + $lecturerRubricTotal, 2);
            $student->final_score = round($finalScore, 2);
            $student->overall_status = $overallStatus;

            return $student;
        })->filter();
    }
}
