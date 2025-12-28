<?php

namespace App\Http\Controllers\Academic\FYP;

use App\Exports\CloAssessmentExport;
use App\Exports\StudentPerformanceExport;
use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Company;
use App\Models\Student;
use App\Models\StudentAssessmentMark;
use App\Models\StudentAssessmentRubricMark;
use App\Models\WblGroup;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class FypReportsController extends Controller
{
    /**
     * Display reports overview page.
     */
    public function index(): View
    {
        if (! auth()->user()->isAdmin() && ! auth()->user()->isFypCoordinator()) {
            abort(403, 'Unauthorized access.');
        }

        // Get statistics
        $totalStudents = Student::count();
        $totalGroups = WblGroup::count();
        $totalCompanies = Company::whereHas('students')->distinct()->count();

        return view('academic.fyp.reports.index', compact(
            'totalStudents',
            'totalGroups',
            'totalCompanies'
        ));
    }

    /**
     * Export full cohort results.
     */
    public function exportCohort(Request $request)
    {
        if (! auth()->user()->isAdmin() && ! auth()->user()->isFypCoordinator()) {
            abort(403, 'Unauthorized access.');
        }

        $format = $request->query('format', 'excel');

        // Get all students with performance data
        $studentsWithPerformance = $this->getStudentsWithPerformance();

        if ($studentsWithPerformance->isEmpty()) {
            return redirect()->back()
                ->with('error', 'No student data available for export.');
        }

        // Log export
        Log::info('FYP Cohort Report Export', [
            'admin_id' => auth()->id(),
            'admin_name' => auth()->user()->name,
            'format' => $format,
            'exported_count' => $studentsWithPerformance->count(),
        ]);

        if ($format === 'pdf') {
            return $this->exportPdf($studentsWithPerformance, 'FYP Full Cohort Results');
        }

        // Get weights for export
        [$atTotalWeight, $atRubricTotalWeight, $icTotalWeight] = $this->getAssessmentWeights();

        $fileName = 'FYP_Cohort_Results_'.now()->format('Y-m-d_His').'.xlsx';

        return Excel::download(new StudentPerformanceExport($studentsWithPerformance, 'FYP', $atTotalWeight, $icTotalWeight), $fileName);
    }

    /**
     * Export group-wise results.
     */
    public function exportGroup(Request $request, WblGroup $group)
    {
        if (! auth()->user()->isAdmin() && ! auth()->user()->isFypCoordinator()) {
            abort(403, 'Unauthorized access.');
        }

        $format = $request->query('format', 'excel');

        // Get students in this group
        $students = $group->students;
        $studentsWithPerformance = $this->getStudentsWithPerformance($students);

        if ($studentsWithPerformance->isEmpty()) {
            return redirect()->back()
                ->with('error', 'No student data available for this group.');
        }

        // Log export
        Log::info('FYP Group Report Export', [
            'admin_id' => auth()->id(),
            'admin_name' => auth()->user()->name,
            'format' => $format,
            'group_id' => $group->id,
            'group_name' => $group->name,
            'exported_count' => $studentsWithPerformance->count(),
        ]);

        if ($format === 'pdf') {
            return $this->exportPdf($studentsWithPerformance, "FYP Results - {$group->name}");
        }

        // Get weights for export
        [$atTotalWeight, $atRubricTotalWeight, $icTotalWeight] = $this->getAssessmentWeights();

        $fileName = 'FYP_Group_'.str_replace(' ', '_', $group->name).'_'.now()->format('Y-m-d_His').'.xlsx';

        return Excel::download(new StudentPerformanceExport($studentsWithPerformance, 'FYP', $atTotalWeight, $icTotalWeight), $fileName);
    }

    /**
     * Export company-wise results.
     */
    public function exportCompany(Request $request, Company $company)
    {
        if (! auth()->user()->isAdmin() && ! auth()->user()->isFypCoordinator()) {
            abort(403, 'Unauthorized access.');
        }

        $format = $request->query('format', 'excel');

        // Get students in this company
        $students = $company->students;
        $studentsWithPerformance = $this->getStudentsWithPerformance($students);

        if ($studentsWithPerformance->isEmpty()) {
            return redirect()->back()
                ->with('error', 'No student data available for this company.');
        }

        // Log export
        Log::info('FYP Company Report Export', [
            'admin_id' => auth()->id(),
            'admin_name' => auth()->user()->name,
            'format' => $format,
            'company_id' => $company->id,
            'company_name' => $company->company_name,
            'exported_count' => $studentsWithPerformance->count(),
        ]);

        if ($format === 'pdf') {
            return $this->exportPdf($studentsWithPerformance, "FYP Results - {$company->company_name}");
        }

        // Get weights for export
        [$atTotalWeight, $atRubricTotalWeight, $icTotalWeight] = $this->getAssessmentWeights();

        $fileName = 'FYP_Company_'.str_replace(' ', '_', $company->company_name).'_'.now()->format('Y-m-d_His').'.xlsx';

        return Excel::download(new StudentPerformanceExport($studentsWithPerformance, 'FYP', $atTotalWeight, $icTotalWeight), $fileName);
    }

    /**
     * Get students with performance data (shared logic).
     */
    private function getStudentsWithPerformance($students = null)
    {
        if (! $students) {
            $students = Student::with(['group', 'company'])->get();
        }

        // Get active assessments - FYP uses AT (Academic Tutor) as evaluator
        $atAssessments = Assessment::forCourse('FYP')
            ->forEvaluator('lecturer')
            ->active()
            ->get();

        $icAssessments = Assessment::forCourse('FYP')
            ->forEvaluator('ic')
            ->active()
            ->whereIn('assessment_type', ['Oral', 'Rubric'])
            ->with('rubrics')
            ->get();

        // Get all AT marks
        $allAtMarks = StudentAssessmentMark::whereIn('student_id', $students->pluck('id'))
            ->whereIn('assessment_id', $atAssessments->pluck('id'))
            ->with('assessment')
            ->get()
            ->groupBy('student_id');

        // Get all IC rubric marks
        $allIcRubricMarks = StudentAssessmentRubricMark::whereIn('student_id', $students->pluck('id'))
            ->whereHas('rubric.assessment', function ($q) {
                $q->where('course_code', 'FYP')->where('evaluator_role', 'ic');
            })
            ->with('rubric.assessment')
            ->get()
            ->groupBy('student_id');

        // Calculate performance for each student
        return $students->map(function ($student) use ($allAtMarks, $allIcRubricMarks, $atAssessments) {
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

            // Calculate IC marks
            $icRubricMarks = $allIcRubricMarks->get($student->id, collect());
            $icTotal = 0;
            foreach ($icRubricMarks as $rubricMark) {
                $icTotal += $rubricMark->weighted_contribution;
            }

            // Calculate final score
            $finalScore = $atTotal + $icTotal;

            // Set both at_score and lecturer_score for compatibility
            $student->at_score = round($atTotal, 2);
            $student->lecturer_score = round($atTotal, 2); // For Excel export compatibility
            $student->ic_score = round($icTotal, 2);
            $student->final_score = round($finalScore, 2);

            // Set status for export
            if ($finalScore >= 80) {
                $student->overall_status = 'completed';
                $student->overall_status_label = 'Completed';
            } elseif ($atTotal > 0 || $icTotal > 0) {
                $student->overall_status = 'in_progress';
                $student->overall_status_label = 'In Progress';
            } else {
                $student->overall_status = 'not_started';
                $student->overall_status_label = 'Not Started';
            }

            // Set last_updated for export
            $student->last_updated = $student->updated_at;

            return $student;
        });
    }

    /**
     * Get assessment weights (shared logic).
     */
    private function getAssessmentWeights(): array
    {
        $atTotalWeight = Assessment::forCourse('FYP')
            ->forEvaluator('lecturer')
            ->active()
            ->sum('weight_percentage');

        // Get AT rubric-based assessments weight
        $atRubricTotalWeight = Assessment::forCourse('FYP')
            ->forEvaluator('at')
            ->active()
            ->whereIn('assessment_type', ['Oral', 'Rubric'])
            ->with('rubrics')
            ->get()
            ->sum(function ($assessment) {
                return $assessment->rubrics->sum('weight_percentage');
            });

        $icTotalWeight = Assessment::forCourse('FYP')
            ->forEvaluator('ic')
            ->active()
            ->whereIn('assessment_type', ['Oral', 'Rubric'])
            ->with('rubrics')
            ->get()
            ->sum(function ($assessment) {
                return $assessment->rubrics->sum('weight_percentage');
            });

        return [$atTotalWeight, $atRubricTotalWeight, $icTotalWeight];
    }

    /**
     * Export CLO Assessment report.
     */
    public function exportCloAssessment(Request $request)
    {
        if (! auth()->user()->isAdmin() && ! auth()->user()->isFypCoordinator()) {
            abort(403, 'Unauthorized access.');
        }

        $format = $request->query('format', 'excel');

        // Build query for students
        $query = Student::with(['group', 'company']);

        // Apply filters
        if ($request->filled('session')) {
            $query->where('academic_session', $request->session);
        }
        if ($request->filled('group_id')) {
            $query->where('group_id', $request->group_id);
        }
        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        $students = $query->get();

        if ($students->isEmpty()) {
            return redirect()->back()
                ->with('error', 'No student data available for the selected filters.');
        }

        // Get all FYP assessments with rubrics
        $assessments = Assessment::forCourse('FYP')
            ->active()
            ->with('rubrics')
            ->get();

        // Extract unique CLO codes from rubrics
        $cloCodes = $assessments->flatMap(function ($assessment) {
            return $assessment->rubrics->pluck('clo_code');
        })->filter()->unique()->sort()->values()->toArray();

        if (empty($cloCodes)) {
            return redirect()->back()
                ->with('error', 'No CLO data found for FYP assessments.');
        }

        // Get all rubric marks for these students
        $rubricMarks = StudentAssessmentRubricMark::whereIn('student_id', $students->pluck('id'))
            ->with('rubric')
            ->get()
            ->groupBy('student_id');

        // Calculate CLO scores for each student
        $studentsWithClo = $students->map(function ($student) use ($rubricMarks, $cloCodes) {
            $studentMarks = $rubricMarks->get($student->id, collect());

            // Group marks by CLO
            $cloScores = [];
            foreach ($cloCodes as $clo) {
                $cloScores[$clo] = 0;
            }

            foreach ($studentMarks as $mark) {
                if ($mark->rubric && $mark->rubric->clo_code) {
                    $cloCode = $mark->rubric->clo_code;
                    if (isset($cloScores[$cloCode])) {
                        $cloScores[$cloCode] += $mark->weighted_contribution;
                    }
                }
            }

            $student->clo_scores = $cloScores;

            // Calculate total and status
            $totalScore = array_sum($cloScores);
            if ($totalScore >= 80) {
                $student->overall_status_label = 'Completed';
            } elseif ($totalScore > 0) {
                $student->overall_status_label = 'In Progress';
            } else {
                $student->overall_status_label = 'Not Started';
            }

            return $student;
        });

        // Log export
        Log::info('FYP CLO Assessment Report Export', [
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name,
            'format' => $format,
            'filters' => $request->only(['session', 'group_id', 'company_id', 'report_type']),
            'exported_count' => $studentsWithClo->count(),
        ]);

        if ($format === 'pdf') {
            return $this->exportCloPdf($studentsWithClo, $cloCodes);
        }

        $fileName = 'FYP_CLO_Assessment_'.now()->format('Y-m-d_His').'.xlsx';

        return Excel::download(new CloAssessmentExport($studentsWithClo, $cloCodes, 'FYP'), $fileName);
    }

    /**
     * Export CLO Assessment to PDF.
     */
    private function exportCloPdf($students, $cloCodes)
    {
        $pdf = Pdf::loadView('academic.fyp.reports.clo-assessment-pdf', [
            'students' => $students,
            'cloCodes' => $cloCodes,
            'generatedBy' => auth()->user()->name,
            'generatedAt' => now(),
        ])->setPaper('a4', 'landscape')
            ->setOption('margin-top', 15)
            ->setOption('margin-bottom', 15)
            ->setOption('margin-left', 12)
            ->setOption('margin-right', 12);

        $fileName = 'FYP_CLO_Assessment_'.now()->format('Y-m-d_His').'.pdf';

        return $pdf->download($fileName);
    }

    /**
     * Export to PDF.
     */
    private function exportPdf($students, $title)
    {
        [$atTotalWeight, $atRubricTotalWeight, $icTotalWeight] = $this->getAssessmentWeights();

        $pdf = Pdf::loadView('academic.fyp.performance.export-pdf', [
            'students' => $students,
            'atTotalWeight' => $atTotalWeight,
            'atRubricTotalWeight' => $atRubricTotalWeight,
            'icTotalWeight' => $icTotalWeight,
            'adminName' => auth()->user()->name,
            'generatedAt' => now(),
        ])->setPaper('a4', 'landscape')
            ->setOption('margin-top', 15)
            ->setOption('margin-bottom', 15)
            ->setOption('margin-left', 12)
            ->setOption('margin-right', 12)
            ->setOption('enable-local-file-access', true);

        $fileName = str_replace(' ', '_', $title).'_'.now()->format('Y-m-d_His').'.pdf';

        return $pdf->download($fileName);
    }
}
