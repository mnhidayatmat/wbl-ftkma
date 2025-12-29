<?php

namespace App\Http\Controllers\Academic\LI;

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

class LiReportsController extends Controller
{
    /**
     * Display reports overview page.
     */
    public function index(): View
    {
        if (! auth()->user()->isAdmin() && ! auth()->user()->isLiCoordinator()) {
            abort(403, 'Unauthorized access.');
        }

        // Get statistics
        $totalStudents = Student::count();
        $totalGroups = WblGroup::count();
        $totalCompanies = Company::whereHas('students')->distinct()->count();

        // Get groups and companies for the view
        $groups = WblGroup::orderBy('name')->get();
        $companies = Company::whereHas('students')->orderBy('company_name')->get();

        return view('academic.li.reports.index', compact(
            'totalStudents',
            'totalGroups',
            'totalCompanies',
            'groups',
            'companies'
        ));
    }

    /**
     * Export full cohort results.
     */
    public function exportCohort(Request $request)
    {
        if (! auth()->user()->isAdmin() && ! auth()->user()->isLiCoordinator()) {
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
        Log::info('LI Cohort Report Export', [
            'admin_id' => auth()->id(),
            'admin_name' => auth()->user()->name,
            'format' => $format,
            'exported_count' => $studentsWithPerformance->count(),
        ]);

        if ($format === 'pdf') {
            return $this->exportPdf($studentsWithPerformance, 'Industrial Training Full Cohort Results');
        }

        // Get weights for export
        [$supervisorTotalWeight, $icTotalWeight] = $this->getAssessmentWeights();

        $fileName = 'LI_Cohort_Results_'.now()->format('Y-m-d_His').'.xlsx';

        return Excel::download(new StudentPerformanceExport($studentsWithPerformance, 'LI', $supervisorTotalWeight, $icTotalWeight), $fileName);
    }

    /**
     * Export group-wise results.
     */
    public function exportGroup(Request $request, WblGroup $group)
    {
        if (! auth()->user()->isAdmin() && ! auth()->user()->isLiCoordinator()) {
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
        Log::info('LI Group Report Export', [
            'admin_id' => auth()->id(),
            'admin_name' => auth()->user()->name,
            'format' => $format,
            'group_id' => $group->id,
            'group_name' => $group->name,
            'exported_count' => $studentsWithPerformance->count(),
        ]);

        if ($format === 'pdf') {
            return $this->exportPdf($studentsWithPerformance, "Industrial Training Results - {$group->name}");
        }

        // Get weights for export
        [$supervisorTotalWeight, $icTotalWeight] = $this->getAssessmentWeights();

        $fileName = 'LI_Group_'.str_replace(' ', '_', $group->name).'_'.now()->format('Y-m-d_His').'.xlsx';

        return Excel::download(new StudentPerformanceExport($studentsWithPerformance, 'LI', $supervisorTotalWeight, $icTotalWeight), $fileName);
    }

    /**
     * Export company-wise results.
     */
    public function exportCompany(Request $request, Company $company)
    {
        if (! auth()->user()->isAdmin() && ! auth()->user()->isLiCoordinator()) {
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
        Log::info('LI Company Report Export', [
            'admin_id' => auth()->id(),
            'admin_name' => auth()->user()->name,
            'format' => $format,
            'company_id' => $company->id,
            'company_name' => $company->company_name,
            'exported_count' => $studentsWithPerformance->count(),
        ]);

        if ($format === 'pdf') {
            return $this->exportPdf($studentsWithPerformance, "Industrial Training Results - {$company->company_name}");
        }

        // Get weights for export
        [$supervisorTotalWeight, $icTotalWeight] = $this->getAssessmentWeights();

        $fileName = 'LI_Company_'.str_replace(' ', '_', $company->company_name).'_'.now()->format('Y-m-d_His').'.xlsx';

        return Excel::download(new StudentPerformanceExport($studentsWithPerformance, 'LI', $supervisorTotalWeight, $icTotalWeight), $fileName);
    }

    /**
     * Get students with performance data (shared logic).
     */
    private function getStudentsWithPerformance($students = null)
    {
        if (! $students) {
            $students = Student::with(['group', 'company'])->get();
        }

        // Get active assessments - LI uses Supervisor (lecturer role) as evaluator
        $supervisorAssessments = Assessment::forCourse('LI')
            ->forEvaluator('lecturer')
            ->active()
            ->get();

        $icAssessments = Assessment::forCourse('LI')
            ->forEvaluator('ic')
            ->active()
            ->whereIn('assessment_type', ['Oral', 'Rubric'])
            ->with('rubrics')
            ->get();

        // Get all Supervisor marks
        $allSupervisorMarks = StudentAssessmentMark::whereIn('student_id', $students->pluck('id'))
            ->whereIn('assessment_id', $supervisorAssessments->pluck('id'))
            ->with('assessment')
            ->get()
            ->groupBy('student_id');

        // Get all IC rubric marks
        $allIcRubricMarks = StudentAssessmentRubricMark::whereIn('student_id', $students->pluck('id'))
            ->whereHas('rubric.assessment', function ($q) {
                $q->where('course_code', 'LI')->where('evaluator_role', 'ic');
            })
            ->with('rubric.assessment')
            ->get()
            ->groupBy('student_id');

        // Calculate performance for each student
        return $students->map(function ($student) use ($allSupervisorMarks, $allIcRubricMarks, $supervisorAssessments) {
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
            $icRubricMarks = $allIcRubricMarks->get($student->id, collect());
            $icTotal = 0;
            foreach ($icRubricMarks as $rubricMark) {
                $icTotal += $rubricMark->weighted_contribution;
            }

            // Calculate final score
            $finalScore = $supervisorTotal + $icTotal;

            // Set both supervisor_score and lecturer_score for compatibility
            $student->supervisor_score = round($supervisorTotal, 2);
            $student->lecturer_score = round($supervisorTotal, 2); // For Excel export compatibility
            $student->ic_score = round($icTotal, 2);
            $student->final_score = round($finalScore, 2);

            // Set status for export
            if ($finalScore >= 80) {
                $student->overall_status = 'completed';
                $student->overall_status_label = 'Completed';
            } elseif ($supervisorTotal > 0 || $icTotal > 0) {
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
        $supervisorTotalWeight = Assessment::forCourse('LI')
            ->forEvaluator('lecturer')
            ->active()
            ->sum('weight_percentage');

        $icTotalWeight = Assessment::forCourse('LI')
            ->forEvaluator('ic')
            ->active()
            ->whereIn('assessment_type', ['Oral', 'Rubric'])
            ->with('rubrics')
            ->get()
            ->sum(function ($assessment) {
                return $assessment->rubrics->sum('weight_percentage');
            });

        return [$supervisorTotalWeight, $icTotalWeight];
    }

    /**
     * Export to PDF.
     */
    private function exportPdf($students, $title)
    {
        [$supervisorTotalWeight, $icTotalWeight] = $this->getAssessmentWeights();

        $pdf = Pdf::loadView('academic.li.performance.export-pdf', [
            'students' => $students,
            'supervisorTotalWeight' => $supervisorTotalWeight,
            'icTotalWeight' => $icTotalWeight,
            'adminName' => auth()->user()->name,
            'generatedAt' => now(),
        ])->setPaper('a4', 'landscape')
            ->setOption('margin-top', 20)
            ->setOption('margin-bottom', 20)
            ->setOption('margin-left', 15)
            ->setOption('margin-right', 15)
            ->setOption('enable-local-file-access', true);

        $fileName = str_replace(' ', '_', $title).'_'.now()->format('Y-m-d_His').'.pdf';

        return $pdf->download($fileName);
    }
}
