<?php

namespace App\Http\Controllers\Academic\OSH;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Student;
use App\Models\StudentAssessmentMark;
use App\Models\StudentAssessmentRubricMark;
use App\Models\WblGroup;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OshProgressController extends Controller
{
    /**
     * Display evaluation progress overview.
     */
    public function index(Request $request): View
    {
        if (! auth()->user()->isAdmin() && ! auth()->user()->isOshCoordinator()) {
            abort(403, 'Unauthorized access.');
        }

        // Get active assessments - OSH uses Lecturer as evaluator
        $lecturerAssessments = Assessment::forCourse('OSH')
            ->forEvaluator('lecturer')
            ->active()
            ->get();

        // Get all students (filter by active groups)
        $students = Student::with(['group', 'company'])->inActiveGroups()->get();

        // Get all lecturer marks
        $allLecturerMarks = StudentAssessmentMark::whereIn('student_id', $students->pluck('id'))
            ->whereIn('assessment_id', $lecturerAssessments->pluck('id'))
            ->with('assessment')
            ->get()
            ->groupBy('student_id');

        // Get all lecturer rubric marks (if any)
        $allLecturerRubricMarks = StudentAssessmentRubricMark::whereIn('student_id', $students->pluck('id'))
            ->whereHas('rubric.assessment', function ($q) {
                $q->where('course_code', 'OSH')
                    ->where('evaluator_role', 'lecturer');
            })
            ->with('rubric.assessment')
            ->get()
            ->groupBy('student_id');

        // Calculate total rubric questions
        $totalRubricQuestions = $lecturerAssessments->sum(function ($assessment) {
            return $assessment->rubrics->count();
        });

        // Calculate overall statistics
        $totalStudents = $students->count();
        $lecturerCompleted = 0;

        foreach ($students as $student) {
            $lecturerMarks = $allLecturerMarks->get($student->id, collect());
            $lecturerCompletedCount = $lecturerMarks->filter(function ($mark) {
                return $mark->mark !== null;
            })->count();

            $lecturerRubricMarks = $allLecturerRubricMarks->get($student->id, collect());
            $lecturerRubricCompleted = $lecturerRubricMarks->count();

            // Student is complete if all assessment marks are done AND all rubric marks (if any) are done
            if ($lecturerCompletedCount === $lecturerAssessments->count() &&
                $lecturerAssessments->count() > 0 &&
                ($totalRubricQuestions === 0 || $lecturerRubricCompleted === $totalRubricQuestions)) {
                $lecturerCompleted++;
            }
        }

        $lecturerProgress = $totalStudents > 0 ? ($lecturerCompleted / $totalStudents) * 100 : 0;
        $pendingLecturer = $totalStudents - $lecturerCompleted;

        // Group breakdown
        $groups = WblGroup::where('status', 'ACTIVE')->with('students')->get();
        $groupStats = $groups->map(function ($group) use ($allLecturerMarks, $allLecturerRubricMarks, $lecturerAssessments, $totalRubricQuestions) {
            $groupStudents = $group->students;
            $groupTotal = $groupStudents->count();
            $groupLecturerCompleted = 0;

            foreach ($groupStudents as $student) {
                $lecturerMarks = $allLecturerMarks->get($student->id, collect());
                $lecturerCompletedCount = $lecturerMarks->filter(function ($mark) {
                    return $mark->mark !== null;
                })->count();

                $lecturerRubricMarks = $allLecturerRubricMarks->get($student->id, collect());
                $lecturerRubricCompleted = $lecturerRubricMarks->count();

                if ($lecturerCompletedCount === $lecturerAssessments->count() &&
                    $lecturerAssessments->count() > 0 &&
                    ($totalRubricQuestions === 0 || $lecturerRubricCompleted === $totalRubricQuestions)) {
                    $groupLecturerCompleted++;
                }
            }

            return [
                'group' => $group,
                'total' => $groupTotal,
                'lecturer_completed' => $groupLecturerCompleted,
                'lecturer_progress' => $groupTotal > 0 ? ($groupLecturerCompleted / $groupTotal) * 100 : 0,
            ];
        });

        // Programme breakdown
        $programmes = Student::distinct()->pluck('programme')->filter();
        $programmeStats = $programmes->map(function ($programme) use ($allLecturerMarks, $allLecturerRubricMarks, $lecturerAssessments, $totalRubricQuestions) {
            $programmeStudents = Student::where('programme', $programme)->inActiveGroups()->get();
            $programmeTotal = $programmeStudents->count();
            $programmeLecturerCompleted = 0;

            foreach ($programmeStudents as $student) {
                $lecturerMarks = $allLecturerMarks->get($student->id, collect());
                $lecturerCompletedCount = $lecturerMarks->filter(function ($mark) {
                    return $mark->mark !== null;
                })->count();

                $lecturerRubricMarks = $allLecturerRubricMarks->get($student->id, collect());
                $lecturerRubricCompleted = $lecturerRubricMarks->count();

                if ($lecturerCompletedCount === $lecturerAssessments->count() &&
                    $lecturerAssessments->count() > 0 &&
                    ($totalRubricQuestions === 0 || $lecturerRubricCompleted === $totalRubricQuestions)) {
                    $programmeLecturerCompleted++;
                }
            }

            return [
                'programme' => $programme,
                'total' => $programmeTotal,
                'lecturer_completed' => $programmeLecturerCompleted,
                'lecturer_progress' => $programmeTotal > 0 ? ($programmeLecturerCompleted / $programmeTotal) * 100 : 0,
            ];
        });

        // Company breakdown
        $companies = Student::whereNotNull('company_id')->inActiveGroups()->with('company')->get()->groupBy('company_id');
        $companyStats = $companies->map(function ($companyStudents, $companyId) use ($allLecturerMarks, $allLecturerRubricMarks, $lecturerAssessments, $totalRubricQuestions) {
            $company = $companyStudents->first()->company;
            $companyTotal = $companyStudents->count();
            $companyLecturerCompleted = 0;

            foreach ($companyStudents as $student) {
                $lecturerMarks = $allLecturerMarks->get($student->id, collect());
                $lecturerCompletedCount = $lecturerMarks->filter(function ($mark) {
                    return $mark->mark !== null;
                })->count();

                $lecturerRubricMarks = $allLecturerRubricMarks->get($student->id, collect());
                $lecturerRubricCompleted = $lecturerRubricMarks->count();

                if ($lecturerCompletedCount === $lecturerAssessments->count() &&
                    $lecturerAssessments->count() > 0 &&
                    ($totalRubricQuestions === 0 || $lecturerRubricCompleted === $totalRubricQuestions)) {
                    $companyLecturerCompleted++;
                }
            }

            return [
                'company' => $company,
                'total' => $companyTotal,
                'lecturer_completed' => $companyLecturerCompleted,
                'lecturer_progress' => $companyTotal > 0 ? ($companyLecturerCompleted / $companyTotal) * 100 : 0,
            ];
        });

        return view('academic.osh.progress.index', compact(
            'totalStudents',
            'lecturerCompleted',
            'lecturerProgress',
            'pendingLecturer',
            'groupStats',
            'programmeStats',
            'companyStats'
        ));
    }
}
