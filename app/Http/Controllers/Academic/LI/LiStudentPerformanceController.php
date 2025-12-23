namespace App\Http\Controllers\Academic\LI;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Student;
use App\Models\StudentAssessmentMark;
use App\Models\WblGroup;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LiStudentPerformanceController extends Controller
{
    /**
     * Display student performance overview for LI.
     */
    public function index(Request $request): View
    {
        // Get active assessments for LI
        $supervisorAssessments = Assessment::forCourse('LI')
            ->forEvaluator('lecturer')
            ->active()
            ->get();

        $icAssessments = Assessment::forCourse('LI')
            ->forEvaluator('ic')
            ->active()
            ->get();

        $supervisorWeight = $supervisorAssessments->sum('weight_percentage');
        $icWeight = $icAssessments->sum('weight_percentage');
        $totalWeight = $supervisorWeight + $icWeight;

        // Build query for students
        $query = Student::with(['group', 'company', 'academicTutor', 'industryCoach']);
        
        // Admin can see all, Lecturers/Industry might have restricted views if needed
        // For performance index, usually Admin-only or restricted to ATs/ICs
        if (auth()->user()->isLecturer() && !auth()->user()->isAdmin()) {
            // If they are a supervisor_li (stored via StudentCourseAssignment)
            $assignedStudentIds = \App\Models\StudentCourseAssignment::where('lecturer_id', auth()->id())
                ->where('course_type', 'Industrial Training')
                ->pluck('student_id');
            $query->whereIn('id', $assignedStudentIds);
        } elseif (auth()->user()->isIndustry() && !auth()->user()->isAdmin()) {
            $query->where('ic_id', auth()->id());
        }

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('matric_no', 'like', "%{$search}%");
            });
        }

        if ($request->filled('group')) {
            $query->where('group_id', $request->group);
        }

        // Get students
        $students = $query->orderBy('name')->get();

        // Get marks
        $allMarks = StudentAssessmentMark::whereIn('student_id', $students->pluck('id'))
            ->whereIn('assessment_id', $supervisorAssessments->pluck('id')->merge($icAssessments->pluck('id')))
            ->get()
            ->groupBy('student_id');

        // Calculate performance
        $studentsWithPerformance = $students->map(function($student) use (
            $allMarks, 
            $supervisorAssessments, 
            $icAssessments,
            $supervisorWeight,
            $icWeight,
            $totalWeight
        ) {
            $studentMarks = $allMarks->get($student->id, collect())->keyBy('assessment_id');
            
            $supervisorTotal = 0;
            $icTotal = 0;
            $completedCount = 0;
            $totalCount = $supervisorAssessments->count() + $icAssessments->count();

            // Supervisor marks
            foreach ($supervisorAssessments as $assessment) {
                $mark = $studentMarks->get($assessment->id);
                if ($mark && $mark->mark !== null) {
                    $completedCount++;
                    if ($mark->max_mark > 0) {
                        $supervisorTotal += ($mark->mark / $mark->max_mark) * $assessment->weight_percentage;
                    }
                }
            }

            // IC marks
            foreach ($icAssessments as $assessment) {
                $mark = $studentMarks->get($assessment->id);
                if ($mark && $mark->mark !== null) {
                    $completedCount++;
                    if ($mark->max_mark > 0) {
                        $icTotal += ($mark->mark / $mark->max_mark) * $assessment->weight_percentage;
                    }
                }
            }

            $finalScore = $supervisorTotal + $icTotal;
            $progress = $totalCount > 0 ? ($completedCount / $totalCount) * 100 : 0;

            $student->supervisor_score = $supervisorTotal;
            $student->ic_score = $icTotal;
            $student->final_score = $finalScore;
            $student->total_weight = $totalWeight;
            $student->progress = $progress;
            $student->status = $completedCount == 0 ? 'not_started' : ($completedCount < $totalCount ? 'in_progress' : 'completed');

            return $student;
        });

        $groups = WblGroup::orderBy('name')->get();

        return view('academic.li.performance.index', compact(
            'studentsWithPerformance',
            'supervisorWeight',
            'icWeight',
            'totalWeight',
            'groups'
        ));
    }

    /**
     * Export student performance to Excel.
     */
    public function exportExcel(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $studentsWithPerformance = $this->getFilteredStudents($request);

        if ($studentsWithPerformance->isEmpty()) {
            return redirect()->back()
                ->with('error', 'No students found to export. Please adjust your filters.');
        }

        $supervisorAssessments = Assessment::forCourse('LI')
            ->forEvaluator('lecturer')
            ->active()
            ->get();
        $icAssessments = Assessment::forCourse('LI')
            ->forEvaluator('ic')
            ->active()
            ->get();

        $supervisorWeight = $supervisorAssessments->sum('weight_percentage');
        $icWeight = $icAssessments->sum('weight_percentage');

        $fileName = 'LI_Student_Performance_' . now()->format('Y-m-d_His') . '.xlsx';
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\StudentPerformanceExport($studentsWithPerformance, 'LI', $supervisorWeight, $icWeight), 
            $fileName
        );
    }

    /**
     * Export student performance to PDF.
     */
    public function exportPdf(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $studentsWithPerformance = $this->getFilteredStudents($request);

        if ($studentsWithPerformance->isEmpty()) {
            return redirect()->back()
                ->with('error', 'No students found to export. Please adjust your filters.');
        }

        $supervisorAssessments = Assessment::forCourse('LI')
            ->forEvaluator('lecturer')
            ->active()
            ->get();
        $icAssessments = Assessment::forCourse('LI')
            ->forEvaluator('ic')
            ->active()
            ->get();

        $supervisorWeight = $supervisorAssessments->sum('weight_percentage');
        $icWeight = $icAssessments->sum('weight_percentage');
        $totalWeight = $supervisorWeight + $icWeight;

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('academic.li.performance.export-pdf', [
            'students' => $studentsWithPerformance,
            'supervisorWeight' => $supervisorWeight,
            'icWeight' => $icWeight,
            'totalWeight' => $totalWeight,
            'adminName' => auth()->user()->name,
            'generatedAt' => now(),
        ])->setPaper('a4', 'landscape');

        $fileName = 'LI_Student_Performance_' . now()->format('Y-m-d_His') . '.pdf';
        return $pdf->download($fileName);
    }

    /**
     * Get filtered students with performance data.
     */
    private function getFilteredStudents(Request $request)
    {
        // Get active assessments for LI
        $supervisorAssessments = Assessment::forCourse('LI')
            ->forEvaluator('lecturer')
            ->active()
            ->get();

        $icAssessments = Assessment::forCourse('LI')
            ->forEvaluator('ic')
            ->active()
            ->get();

        $supervisorWeight = $supervisorAssessments->sum('weight_percentage');
        $icWeight = $icAssessments->sum('weight_percentage');
        $totalWeight = $supervisorWeight + $icWeight;

        $query = Student::with(['group', 'company', 'academicTutor', 'industryCoach']);
        
        if (auth()->user()->isLecturer() && !auth()->user()->isAdmin()) {
            $assignedStudentIds = \App\Models\StudentCourseAssignment::where('lecturer_id', auth()->id())
                ->where('course_type', 'Industrial Training')
                ->pluck('student_id');
            $query->whereIn('id', $assignedStudentIds);
        } elseif (auth()->user()->isIndustry() && !auth()->user()->isAdmin()) {
            $query->where('ic_id', auth()->id());
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('matric_no', 'like', "%{$search}%");
            });
        }

        if ($request->filled('group')) {
            $query->where('group_id', $request->group);
        }

        $students = $query->orderBy('name')->get();

        $allMarks = StudentAssessmentMark::whereIn('student_id', $students->pluck('id'))
            ->whereIn('assessment_id', $supervisorAssessments->pluck('id')->merge($icAssessments->pluck('id')))
            ->get()
            ->groupBy('student_id');

        return $students->map(function($student) use (
            $allMarks, 
            $supervisorAssessments, 
            $icAssessments,
            $supervisorWeight,
            $icWeight,
            $totalWeight
        ) {
            $studentMarks = $allMarks->get($student->id, collect())->keyBy('assessment_id');
            
            $supervisorTotal = 0;
            $icTotal = 0;
            $completedCount = 0;
            $totalCount = $supervisorAssessments->count() + $icAssessments->count();

            foreach ($supervisorAssessments as $assessment) {
                $mark = $studentMarks->get($assessment->id);
                if ($mark && $mark->mark !== null) {
                    $completedCount++;
                    if ($mark->max_mark > 0) {
                        $supervisorTotal += ($mark->mark / $mark->max_mark) * $assessment->weight_percentage;
                    }
                }
            }

            foreach ($icAssessments as $assessment) {
                $mark = $studentMarks->get($assessment->id);
                if ($mark && $mark->mark !== null) {
                    $completedCount++;
                    if ($mark->max_mark > 0) {
                        $icTotal += ($mark->mark / $mark->max_mark) * $assessment->weight_percentage;
                    }
                }
            }

            $student->supervisor_score = $supervisorTotal;
            $student->ic_score = $icTotal;
            $student->final_score = $supervisorTotal + $icTotal;
            $student->total_weight = $totalWeight;
            $student->progress = $totalCount > 0 ? ($completedCount / $totalCount) * 100 : 0;
            $student->status = $completedCount == 0 ? 'not_started' : ($completedCount < $totalCount ? 'in_progress' : 'completed');

            return $student;
        });
    }
}
