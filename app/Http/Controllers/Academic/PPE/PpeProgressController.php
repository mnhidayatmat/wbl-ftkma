<?php

namespace App\Http\Controllers\Academic\PPE;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Student;
use App\Models\StudentAssessmentMark;
use App\Models\StudentAssessmentRubricMark;
use App\Models\WblGroup;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PpeProgressController extends Controller
{
    /**
     * Display evaluation progress overview.
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

        // Get all students
        $students = Student::with(['group', 'company'])->get();

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

        // Calculate overall statistics
        $totalStudents = $students->count();
        $lecturerCompleted = 0;
        $icCompleted = 0;

        foreach ($students as $student) {
            $lecturerMarks = $allLecturerMarks->get($student->id, collect());
            $lecturerCompletedCount = $lecturerMarks->filter(function ($mark) {
                return $mark->mark !== null;
            })->count();

            if ($lecturerCompletedCount === $lecturerAssessments->count() && $lecturerAssessments->count() > 0) {
                $lecturerCompleted++;
            }

            $icRubricMarks = $allIcRubricMarks->get($student->id, collect());
            if ($icRubricMarks->count() === $totalRubricQuestions && $totalRubricQuestions > 0) {
                $icCompleted++;
            }
        }

        $lecturerProgress = $totalStudents > 0 ? ($lecturerCompleted / $totalStudents) * 100 : 0;
        $icProgress = $totalStudents > 0 ? ($icCompleted / $totalStudents) * 100 : 0;
        $pendingLecturer = $totalStudents - $lecturerCompleted;
        $pendingIc = $totalStudents - $icCompleted;

        // Group breakdown
        $groups = WblGroup::with('students')->get();
        $groupStats = $groups->map(function ($group) use ($allLecturerMarks, $allIcRubricMarks, $lecturerAssessments, $totalRubricQuestions) {
            $groupStudents = $group->students;
            $groupTotal = $groupStudents->count();
            $groupLecturerCompleted = 0;
            $groupIcCompleted = 0;

            foreach ($groupStudents as $student) {
                $lecturerMarks = $allLecturerMarks->get($student->id, collect());
                $lecturerCompletedCount = $lecturerMarks->filter(function ($mark) {
                    return $mark->mark !== null;
                })->count();

                if ($lecturerCompletedCount === $lecturerAssessments->count() && $lecturerAssessments->count() > 0) {
                    $groupLecturerCompleted++;
                }

                $icRubricMarks = $allIcRubricMarks->get($student->id, collect());
                if ($icRubricMarks->count() === $totalRubricQuestions && $totalRubricQuestions > 0) {
                    $groupIcCompleted++;
                }
            }

            return [
                'group' => $group,
                'total' => $groupTotal,
                'lecturer_completed' => $groupLecturerCompleted,
                'ic_completed' => $groupIcCompleted,
                'lecturer_progress' => $groupTotal > 0 ? ($groupLecturerCompleted / $groupTotal) * 100 : 0,
                'ic_progress' => $groupTotal > 0 ? ($groupIcCompleted / $groupTotal) * 100 : 0,
            ];
        });

        // Programme breakdown
        $programmes = Student::distinct()->pluck('programme')->filter();
        $programmeStats = $programmes->map(function ($programme) use ($allLecturerMarks, $allIcRubricMarks, $lecturerAssessments, $totalRubricQuestions) {
            $programmeStudents = Student::where('programme', $programme)->get();
            $programmeTotal = $programmeStudents->count();
            $programmeLecturerCompleted = 0;
            $programmeIcCompleted = 0;

            foreach ($programmeStudents as $student) {
                $lecturerMarks = $allLecturerMarks->get($student->id, collect());
                $lecturerCompletedCount = $lecturerMarks->filter(function ($mark) {
                    return $mark->mark !== null;
                })->count();

                if ($lecturerCompletedCount === $lecturerAssessments->count() && $lecturerAssessments->count() > 0) {
                    $programmeLecturerCompleted++;
                }

                $icRubricMarks = $allIcRubricMarks->get($student->id, collect());
                if ($icRubricMarks->count() === $totalRubricQuestions && $totalRubricQuestions > 0) {
                    $programmeIcCompleted++;
                }
            }

            return [
                'programme' => $programme,
                'total' => $programmeTotal,
                'lecturer_completed' => $programmeLecturerCompleted,
                'ic_completed' => $programmeIcCompleted,
                'lecturer_progress' => $programmeTotal > 0 ? ($programmeLecturerCompleted / $programmeTotal) * 100 : 0,
                'ic_progress' => $programmeTotal > 0 ? ($programmeIcCompleted / $programmeTotal) * 100 : 0,
            ];
        });

        // Company breakdown
        $companies = Student::whereNotNull('company_id')->with('company')->get()->groupBy('company_id');
        $companyStats = $companies->map(function ($companyStudents, $companyId) use ($allLecturerMarks, $allIcRubricMarks, $lecturerAssessments, $totalRubricQuestions) {
            $company = $companyStudents->first()->company;
            $companyTotal = $companyStudents->count();
            $companyLecturerCompleted = 0;
            $companyIcCompleted = 0;

            foreach ($companyStudents as $student) {
                $lecturerMarks = $allLecturerMarks->get($student->id, collect());
                $lecturerCompletedCount = $lecturerMarks->filter(function ($mark) {
                    return $mark->mark !== null;
                })->count();

                if ($lecturerCompletedCount === $lecturerAssessments->count() && $lecturerAssessments->count() > 0) {
                    $companyLecturerCompleted++;
                }

                $icRubricMarks = $allIcRubricMarks->get($student->id, collect());
                if ($icRubricMarks->count() === $totalRubricQuestions && $totalRubricQuestions > 0) {
                    $companyIcCompleted++;
                }
            }

            return [
                'company' => $company,
                'total' => $companyTotal,
                'lecturer_completed' => $companyLecturerCompleted,
                'ic_completed' => $companyIcCompleted,
                'lecturer_progress' => $companyTotal > 0 ? ($companyLecturerCompleted / $companyTotal) * 100 : 0,
                'ic_progress' => $companyTotal > 0 ? ($companyIcCompleted / $companyTotal) * 100 : 0,
            ];
        });

        return view('academic.ppe.progress.index', compact(
            'totalStudents',
            'lecturerCompleted',
            'icCompleted',
            'lecturerProgress',
            'icProgress',
            'pendingLecturer',
            'pendingIc',
            'groupStats',
            'programmeStats',
            'companyStats'
        ));
    }
}
