<?php

namespace App\Http\Controllers\Academic\PPE;

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

class PpeStudentPerformanceController extends Controller
{
    /**
     * Display student performance overview combining Lecturer and IC evaluations.
     */
    public function index(Request $request): View
    {
        // Only Admin, PPE Coordinator and Lecturer can access
        if (! auth()->user()->isAdmin() && ! auth()->user()->isPpeCoordinator() && ! auth()->user()->isLecturer()) {
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

        $lecturerTotalWeight = $lecturerAssessments->sum('weight_percentage'); // Should be 40%
        $icTotalWeight = $icAssessments->sum(function ($assessment) {
            return $assessment->rubrics->sum('weight_percentage');
        }); // Should be 60%

        // Build query for students
        $query = Student::with(['group', 'company', 'academicTutor', 'industryCoach']);

        // Admin can see all students, Lecturer sees only students if they are the assigned PPE lecturer
        if (auth()->user()->isLecturer() && ! auth()->user()->isAdmin()) {
            // PPE uses single lecturer from course_settings
            $ppeSetting = CourseSetting::where('course_type', 'PPE')->first();
            if ($ppeSetting && $ppeSetting->lecturer_id === auth()->id()) {
                // This lecturer is assigned to PPE, show all students
                // No additional filter needed
            } else {
                // This lecturer is not assigned to PPE, show no students
                $query->whereRaw('1 = 0'); // Force empty result
            }
        }

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

        // Get all IC rubric marks
        $allIcRubricMarks = StudentAssessmentRubricMark::whereIn('student_id', $students->pluck('id'))
            ->with('rubric.assessment')
            ->get()
            ->groupBy('student_id');

        // Calculate total rubric questions
        $totalRubricQuestions = $icAssessments->sum(function ($assessment) {
            return $assessment->rubrics->count();
        });

        // Calculate performance for each student
        $studentsWithPerformance = $students->map(function ($student) use (
            $allLecturerMarks,
            $allIcRubricMarks,
            $lecturerAssessments,
            $totalRubricQuestions,
            $lecturerTotalWeight,
            $icTotalWeight
        ) {
            // Calculate Lecturer marks (40%)
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

            // Calculate IC marks (60%)
            $icRubricMarks = $allIcRubricMarks->get($student->id, collect());

            $icTotal = 0;
            $icCompletedCount = $icRubricMarks->count();
            $icLastUpdated = null;

            foreach ($icRubricMarks as $rubricMark) {
                $icTotal += $rubricMark->weighted_contribution;
                if (! $icLastUpdated || $rubricMark->updated_at > $icLastUpdated) {
                    $icLastUpdated = $rubricMark->updated_at;
                }
            }

            // Calculate final score
            $finalScore = $lecturerTotal + $icTotal;

            // Determine overall status
            $lecturerStatus = $lecturerCompletedCount == 0 ? 'not_started' :
                            ($lecturerCompletedCount < $lecturerAssessments->count() ? 'in_progress' : 'completed');
            $icStatus = $icCompletedCount == 0 ? 'not_started' :
                       ($icCompletedCount < $totalRubricQuestions ? 'in_progress' : 'completed');

            if ($lecturerStatus == 'not_started' && $icStatus == 'not_started') {
                $overallStatus = 'not_started';
                $overallStatusLabel = 'Not Started';
            } elseif ($lecturerStatus == 'completed' && $icStatus == 'completed') {
                $overallStatus = 'completed';
                $overallStatusLabel = 'Completed';
            } else {
                $overallStatus = 'in_progress';
                $overallStatusLabel = 'In Progress';
            }

            // Apply status filter
            $statusFilter = request('status');
            if ($statusFilter && $overallStatus !== $statusFilter) {
                return null;
            }

            // Get last updated timestamp (most recent from either evaluation)
            $lastUpdated = null;
            if ($lecturerLastUpdated && $icLastUpdated) {
                $lastUpdated = $lecturerLastUpdated > $icLastUpdated ? $lecturerLastUpdated : $icLastUpdated;
            } elseif ($lecturerLastUpdated) {
                $lastUpdated = $lecturerLastUpdated;
            } elseif ($icLastUpdated) {
                $lastUpdated = $icLastUpdated;
            }

            $student->lecturer_score = round($lecturerTotal, 2);
            $student->ic_score = round($icTotal, 2);
            $student->final_score = round($finalScore, 2);
            $student->overall_status = $overallStatus;
            $student->overall_status_label = $overallStatusLabel;
            $student->lecturer_status = $lecturerStatus;
            $student->ic_status = $icStatus;
            $student->last_updated = $lastUpdated;
            $student->lecturer_progress = $lecturerTotalWeight > 0 ? ($lecturerTotal / $lecturerTotalWeight) * 100 : 0;
            $student->ic_progress = $icTotalWeight > 0 ? ($icTotal / $icTotalWeight) * 100 : 0;
            $student->overall_progress = ($finalScore / 100) * 100;

            return $student;
        })->filter();

        // Get groups for filter dropdown
        $groups = WblGroup::orderBy('name')->get();

        // Get unique programmes for filter
        $programmes = Student::distinct()->orderBy('programme')->pluck('programme')->filter();

        return view('academic.ppe.performance.index', compact(
            'studentsWithPerformance',
            'lecturerTotalWeight',
            'icTotalWeight',
            'groups',
            'programmes'
        ));
    }

    /**
     * Export student performance to Excel.
     */
    public function exportExcel(Request $request)
    {
        // Only Admin or PPE Coordinator can export
        if (! auth()->user()->isAdmin() && ! auth()->user()->isPpeCoordinator()) {
            abort(403, 'Unauthorized access.');
        }

        // Get filtered students using the same logic as index
        $studentsWithPerformance = $this->getFilteredStudents($request);

        if ($studentsWithPerformance->isEmpty()) {
            return redirect()->back()
                ->with('error', 'No students found to export. Please adjust your filters.');
        }

        // Log export activity
        Log::info('Student Performance Excel Export', [
            'admin_id' => auth()->id(),
            'admin_name' => auth()->user()->name,
            'exported_count' => $studentsWithPerformance->count(),
            'filters' => $request->only(['search', 'programme', 'group', 'status']),
        ]);

        $fileName = 'PPE_Student_Performance_'.now()->format('Y-m-d_His').'.xlsx';

        // Get weights for export
        $lecturerTotalWeight = Assessment::forCourse('PPE')
            ->forEvaluator('lecturer')
            ->active()
            ->sum('weight_percentage');

        $icTotalWeight = Assessment::forCourse('PPE')
            ->forEvaluator('ic')
            ->active()
            ->whereIn('assessment_type', ['Oral', 'Rubric'])
            ->with('rubrics')
            ->get()
            ->sum(function ($assessment) {
                return $assessment->rubrics->sum('weight_percentage');
            });

        return Excel::download(
            new StudentPerformanceExport($studentsWithPerformance, 'PPE', $lecturerTotalWeight, $icTotalWeight),
            $fileName
        );
    }

    /**
     * Export student performance to PDF.
     */
    public function exportPdf(Request $request)
    {
        // Only Admin or PPE Coordinator can export
        if (! auth()->user()->isAdmin() && ! auth()->user()->isPpeCoordinator()) {
            abort(403, 'Unauthorized access.');
        }

        // Get filtered students using the same logic as index
        $studentsWithPerformance = $this->getFilteredStudents($request);

        if ($studentsWithPerformance->isEmpty()) {
            return redirect()->back()
                ->with('error', 'No students found to export. Please adjust your filters.');
        }

        // Log export activity
        Log::info('Student Performance PDF Export', [
            'admin_id' => auth()->id(),
            'admin_name' => auth()->user()->name,
            'exported_count' => $studentsWithPerformance->count(),
            'filters' => $request->only(['search', 'programme', 'group', 'status']),
        ]);

        $lecturerTotalWeight = Assessment::forCourse('PPE')
            ->forEvaluator('lecturer')
            ->active()
            ->sum('weight_percentage');

        $icTotalWeight = Assessment::forCourse('PPE')
            ->forEvaluator('ic')
            ->active()
            ->whereIn('assessment_type', ['Oral', 'Rubric'])
            ->with('rubrics')
            ->get()
            ->sum(function ($assessment) {
                return $assessment->rubrics->sum('weight_percentage');
            });

        $pdf = Pdf::loadView('academic.ppe.performance.export-pdf', [
            'students' => $studentsWithPerformance,
            'lecturerTotalWeight' => $lecturerTotalWeight,
            'icTotalWeight' => $icTotalWeight,
            'adminName' => auth()->user()->name,
            'generatedAt' => now(),
        ])->setPaper('a4', 'landscape')
            ->setOption('margin-top', 30)
            ->setOption('margin-bottom', 30)
            ->setOption('margin-left', 25)
            ->setOption('margin-right', 25)
            ->setOption('enable-local-file-access', true);

        $fileName = 'PPE_Student_Performance_'.now()->format('Y-m-d_His').'.pdf';

        return $pdf->download($fileName);
    }

    /**
     * Get filtered students with performance data (shared logic for index and export).
     */
    private function getFilteredStudents(Request $request)
    {
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

        $lecturerTotalWeight = $lecturerAssessments->sum('weight_percentage');
        $icTotalWeight = $icAssessments->sum(function ($assessment) {
            return $assessment->rubrics->sum('weight_percentage');
        });

        // Build query for students
        $query = Student::with(['group', 'company', 'academicTutor', 'industryCoach']);

        // Admin can see all students, Lecturer sees only students if they are the assigned PPE lecturer
        if (auth()->user()->isLecturer() && ! auth()->user()->isAdmin()) {
            // PPE uses single lecturer from course_settings
            $ppeSetting = CourseSetting::where('course_type', 'PPE')->first();
            if ($ppeSetting && $ppeSetting->lecturer_id === auth()->id()) {
                // This lecturer is assigned to PPE, show all students
                // No additional filter needed
            } else {
                // This lecturer is not assigned to PPE, show no students
                $query->whereRaw('1 = 0'); // Force empty result
            }
        }

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

        // Get all IC rubric marks
        $allIcRubricMarks = StudentAssessmentRubricMark::whereIn('student_id', $students->pluck('id'))
            ->with('rubric.assessment')
            ->get()
            ->groupBy('student_id');

        // Calculate total rubric questions
        $totalRubricQuestions = $icAssessments->sum(function ($assessment) {
            return $assessment->rubrics->count();
        });

        // Calculate performance for each student
        return $students->map(function ($student) use (
            $allLecturerMarks,
            $allIcRubricMarks,
            $lecturerAssessments,
            $totalRubricQuestions,
            $lecturerTotalWeight,
            $icTotalWeight,
            $request
        ) {
            // Calculate Lecturer marks (40%)
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

            // Calculate IC marks (60%)
            $icRubricMarks = $allIcRubricMarks->get($student->id, collect());

            $icTotal = 0;
            $icCompletedCount = $icRubricMarks->count();
            $icLastUpdated = null;

            foreach ($icRubricMarks as $rubricMark) {
                $icTotal += $rubricMark->weighted_contribution;
                if (! $icLastUpdated || $rubricMark->updated_at > $icLastUpdated) {
                    $icLastUpdated = $rubricMark->updated_at;
                }
            }

            // Calculate final score
            $finalScore = $lecturerTotal + $icTotal;

            // Determine overall status
            $lecturerStatus = $lecturerCompletedCount == 0 ? 'not_started' :
                            ($lecturerCompletedCount < $lecturerAssessments->count() ? 'in_progress' : 'completed');
            $icStatus = $icCompletedCount == 0 ? 'not_started' :
                       ($icCompletedCount < $totalRubricQuestions ? 'in_progress' : 'completed');

            if ($lecturerStatus == 'not_started' && $icStatus == 'not_started') {
                $overallStatus = 'not_started';
                $overallStatusLabel = 'Not Started';
            } elseif ($lecturerStatus == 'completed' && $icStatus == 'completed') {
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

            // Get last updated timestamp (most recent from either evaluation)
            $lastUpdated = null;
            if ($lecturerLastUpdated && $icLastUpdated) {
                $lastUpdated = $lecturerLastUpdated > $icLastUpdated ? $lecturerLastUpdated : $icLastUpdated;
            } elseif ($lecturerLastUpdated) {
                $lastUpdated = $lecturerLastUpdated;
            } elseif ($icLastUpdated) {
                $lastUpdated = $icLastUpdated;
            }

            $student->lecturer_score = round($lecturerTotal, 2);
            $student->ic_score = round($icTotal, 2);
            $student->final_score = round($finalScore, 2);
            $student->overall_status = $overallStatus;
            $student->overall_status_label = $overallStatusLabel;
            $student->lecturer_status = $lecturerStatus;
            $student->ic_status = $icStatus;
            $student->last_updated = $lastUpdated;
            $student->lecturer_progress = $lecturerTotalWeight > 0 ? ($lecturerTotal / $lecturerTotalWeight) * 100 : 0;
            $student->ic_progress = $icTotalWeight > 0 ? ($icTotal / $icTotalWeight) * 100 : 0;
            $student->overall_progress = ($finalScore / 100) * 100;

            return $student;
        })->filter();
    }
}
