<?php

namespace App\Http\Controllers\Academic\FYP;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Student;
use App\Models\StudentAssessmentMark;
use App\Models\StudentAssessmentRubricMark;
use App\Models\WblGroup;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FypProgressController extends Controller
{
    /**
     * Display evaluation progress overview.
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

        // Get active IC assessments
        $icAssessments = Assessment::forCourse('FYP')
            ->forEvaluator('ic')
            ->active()
            ->whereIn('assessment_type', ['Oral', 'Rubric'])
            ->with('rubrics')
            ->get();

        // Get all students
        $students = Student::with(['group', 'company'])->get();

        // Get all AT marks
        $allAtMarks = StudentAssessmentMark::whereIn('student_id', $students->pluck('id'))
            ->whereIn('assessment_id', $atAssessments->pluck('id'))
            ->with('assessment')
            ->get()
            ->groupBy('student_id');

        // Get all IC rubric marks
        $allIcRubricMarks = StudentAssessmentRubricMark::whereIn('student_id', $students->pluck('id'))
            ->whereHas('rubric.assessment', function($q) {
                $q->where('course_code', 'FYP')
                  ->where('evaluator_role', 'ic');
            })
            ->with('rubric.assessment')
            ->get()
            ->groupBy('student_id');

        // Calculate total rubric questions for IC
        $totalIcRubricQuestions = $icAssessments->sum(function($assessment) {
            return $assessment->rubrics->count();
        });

        // Calculate overall statistics
        $totalStudents = $students->count();
        $atCompleted = 0;
        $icCompleted = 0;

        foreach ($students as $student) {
            $atMarks = $allAtMarks->get($student->id, collect());
            $atCompletedCount = $atMarks->filter(function($mark) {
                return $mark->mark !== null;
            })->count();
            
            // Student is complete for AT if all assessment marks are done
            if ($atCompletedCount === $atAssessments->count() && $atAssessments->count() > 0) {
                $atCompleted++;
            }

            // Check IC completion
            $icRubricMarks = $allIcRubricMarks->get($student->id, collect());
            if ($icRubricMarks->count() === $totalIcRubricQuestions && $totalIcRubricQuestions > 0) {
                $icCompleted++;
            }
        }

        $atProgress = $totalStudents > 0 ? ($atCompleted / $totalStudents) * 100 : 0;
        $icProgress = $totalStudents > 0 ? ($icCompleted / $totalStudents) * 100 : 0;
        $pendingAt = $totalStudents - $atCompleted;
        $pendingIc = $totalStudents - $icCompleted;

        // Group breakdown
        $groups = WblGroup::with('students')->get();
        $groupStats = $groups->map(function($group) use ($allAtMarks, $allIcRubricMarks, $atAssessments, $totalIcRubricQuestions) {
            $groupStudents = $group->students;
            $groupTotal = $groupStudents->count();
            $groupAtCompleted = 0;
            $groupIcCompleted = 0;

            foreach ($groupStudents as $student) {
                $atMarks = $allAtMarks->get($student->id, collect());
                $atCompletedCount = $atMarks->filter(function($mark) {
                    return $mark->mark !== null;
                })->count();
                
                if ($atCompletedCount === $atAssessments->count() && $atAssessments->count() > 0) {
                    $groupAtCompleted++;
                }

                $icRubricMarks = $allIcRubricMarks->get($student->id, collect());
                if ($icRubricMarks->count() === $totalIcRubricQuestions && $totalIcRubricQuestions > 0) {
                    $groupIcCompleted++;
                }
            }

            return [
                'group' => $group,
                'total' => $groupTotal,
                'at_completed' => $groupAtCompleted,
                'ic_completed' => $groupIcCompleted,
                'at_progress' => $groupTotal > 0 ? ($groupAtCompleted / $groupTotal) * 100 : 0,
                'ic_progress' => $groupTotal > 0 ? ($groupIcCompleted / $groupTotal) * 100 : 0,
            ];
        });

        // Programme breakdown
        $programmes = Student::distinct()->pluck('programme')->filter();
        $programmeStats = $programmes->map(function($programme) use ($allAtMarks, $allIcRubricMarks, $atAssessments, $totalIcRubricQuestions) {
            $programmeStudents = Student::where('programme', $programme)->get();
            $programmeTotal = $programmeStudents->count();
            $programmeAtCompleted = 0;
            $programmeIcCompleted = 0;

            foreach ($programmeStudents as $student) {
                $atMarks = $allAtMarks->get($student->id, collect());
                $atCompletedCount = $atMarks->filter(function($mark) {
                    return $mark->mark !== null;
                })->count();
                
                if ($atCompletedCount === $atAssessments->count() && $atAssessments->count() > 0) {
                    $programmeAtCompleted++;
                }

                $icRubricMarks = $allIcRubricMarks->get($student->id, collect());
                if ($icRubricMarks->count() === $totalIcRubricQuestions && $totalIcRubricQuestions > 0) {
                    $programmeIcCompleted++;
                }
            }

            return [
                'programme' => $programme,
                'total' => $programmeTotal,
                'at_completed' => $programmeAtCompleted,
                'ic_completed' => $programmeIcCompleted,
                'at_progress' => $programmeTotal > 0 ? ($programmeAtCompleted / $programmeTotal) * 100 : 0,
                'ic_progress' => $programmeTotal > 0 ? ($programmeIcCompleted / $programmeTotal) * 100 : 0,
            ];
        });

        // Company breakdown
        $companies = Student::whereNotNull('company_id')->with('company')->get()->groupBy('company_id');
        $companyStats = $companies->map(function($companyStudents, $companyId) use ($allAtMarks, $allIcRubricMarks, $atAssessments, $totalIcRubricQuestions) {
            $company = $companyStudents->first()->company;
            $companyTotal = $companyStudents->count();
            $companyAtCompleted = 0;
            $companyIcCompleted = 0;

            foreach ($companyStudents as $student) {
                $atMarks = $allAtMarks->get($student->id, collect());
                $atCompletedCount = $atMarks->filter(function($mark) {
                    return $mark->mark !== null;
                })->count();
                
                if ($atCompletedCount === $atAssessments->count() && $atAssessments->count() > 0) {
                    $companyAtCompleted++;
                }

                $icRubricMarks = $allIcRubricMarks->get($student->id, collect());
                if ($icRubricMarks->count() === $totalIcRubricQuestions && $totalIcRubricQuestions > 0) {
                    $companyIcCompleted++;
                }
            }

            return [
                'company' => $company,
                'total' => $companyTotal,
                'at_completed' => $companyAtCompleted,
                'ic_completed' => $companyIcCompleted,
                'at_progress' => $companyTotal > 0 ? ($companyAtCompleted / $companyTotal) * 100 : 0,
                'ic_progress' => $companyTotal > 0 ? ($companyIcCompleted / $companyTotal) * 100 : 0,
            ];
        });

        return view('academic.fyp.progress.index', compact(
            'totalStudents',
            'atCompleted',
            'icCompleted',
            'atProgress',
            'icProgress',
            'pendingAt',
            'pendingIc',
            'groupStats',
            'programmeStats',
            'companyStats'
        ));
    }
}
