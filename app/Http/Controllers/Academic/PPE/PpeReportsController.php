<?php

namespace App\Http\Controllers\Academic\PPE;

use App\Exports\StudentPerformanceExport;
use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Company;
use App\Models\Student;
use App\Models\WblGroup;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class PpeReportsController extends Controller
{
    /**
     * Display reports overview page.
     */
    public function index(): View
    {
        if (! auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        // Get statistics
        $totalStudents = Student::count();
        $totalGroups = WblGroup::count();
        $totalCompanies = Company::whereHas('students')->distinct()->count();

        return view('academic.ppe.reports.index', compact(
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
        if (! auth()->user()->isAdmin()) {
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
        Log::info('PPE Cohort Report Export', [
            'admin_id' => auth()->id(),
            'admin_name' => auth()->user()->name,
            'format' => $format,
            'exported_count' => $studentsWithPerformance->count(),
        ]);

        if ($format === 'pdf') {
            return $this->exportPdf($studentsWithPerformance, 'PPE Full Cohort Results');
        }

        $fileName = 'PPE_Cohort_Results_'.now()->format('Y-m-d_His').'.xlsx';

        return Excel::download(new StudentPerformanceExport($studentsWithPerformance), $fileName);
    }

    /**
     * Export group-wise results.
     */
    public function exportGroup(Request $request, WblGroup $group)
    {
        if (! auth()->user()->isAdmin()) {
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
        Log::info('PPE Group Report Export', [
            'admin_id' => auth()->id(),
            'admin_name' => auth()->user()->name,
            'format' => $format,
            'group_id' => $group->id,
            'group_name' => $group->name,
            'exported_count' => $studentsWithPerformance->count(),
        ]);

        if ($format === 'pdf') {
            return $this->exportPdf($studentsWithPerformance, "PPE Results - {$group->name}");
        }

        $fileName = 'PPE_Group_'.str_replace(' ', '_', $group->name).'_'.now()->format('Y-m-d_His').'.xlsx';

        return Excel::download(new StudentPerformanceExport($studentsWithPerformance), $fileName);
    }

    /**
     * Export company-wise results.
     */
    public function exportCompany(Request $request, Company $company)
    {
        if (! auth()->user()->isAdmin()) {
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
        Log::info('PPE Company Report Export', [
            'admin_id' => auth()->id(),
            'admin_name' => auth()->user()->name,
            'format' => $format,
            'company_id' => $company->id,
            'company_name' => $company->company_name,
            'exported_count' => $studentsWithPerformance->count(),
        ]);

        if ($format === 'pdf') {
            return $this->exportPdf($studentsWithPerformance, "PPE Results - {$company->company_name}");
        }

        $fileName = 'PPE_Company_'.str_replace(' ', '_', $company->company_name).'_'.now()->format('Y-m-d_His').'.xlsx';

        return Excel::download(new StudentPerformanceExport($studentsWithPerformance), $fileName);
    }

    /**
     * Get students with performance data (shared logic).
     */
    private function getStudentsWithPerformance($students = null)
    {
        if (! $students) {
            $students = Student::with(['group', 'company'])->get();
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

        // Get all lecturer marks
        $allLecturerMarks = \App\Models\StudentAssessmentMark::whereIn('student_id', $students->pluck('id'))
            ->whereIn('assessment_id', $lecturerAssessments->pluck('id'))
            ->with('assessment')
            ->get()
            ->groupBy('student_id');

        // Get all IC rubric marks
        $allIcRubricMarks = \App\Models\StudentAssessmentRubricMark::whereIn('student_id', $students->pluck('id'))
            ->with('rubric.assessment')
            ->get()
            ->groupBy('student_id');

        // Calculate performance for each student
        return $students->map(function ($student) use ($allLecturerMarks, $allIcRubricMarks, $lecturerAssessments) {
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

            $student->lecturer_score = round($lecturerTotal, 2);
            $student->ic_score = round($icTotal, 2);
            $student->final_score = round($finalScore, 2);

            return $student;
        });
    }

    /**
     * Export to PDF.
     */
    private function exportPdf($students, $title)
    {
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
            'students' => $students,
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

        $fileName = str_replace(' ', '_', $title).'_'.now()->format('Y-m-d_His').'.pdf';

        return $pdf->download($fileName);
    }
}
