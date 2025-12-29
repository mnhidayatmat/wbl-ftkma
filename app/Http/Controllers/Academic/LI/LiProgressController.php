<?php

namespace App\Http\Controllers\Academic\LI;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Student;
use App\Models\StudentAssessmentMark;
use App\Models\WblGroup;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LiProgressController extends Controller
{
    /**
     * Display evaluation progress overview for Industrial Training (LI).
     */
    public function index(Request $request): View
    {
        if (! auth()->user()->isAdmin() && ! auth()->user()->isLiCoordinator()) {
            abort(403, 'Unauthorized access.');
        }

        // Get active assessments - LI uses Supervisor (stored as 'lecturer' in database)
        $supervisorAssessments = Assessment::forCourse('LI')
            ->forEvaluator('lecturer')
            ->active()
            ->get();

        // Get active IC assessments
        $icAssessments = Assessment::forCourse('LI')
            ->forEvaluator('ic')
            ->active()
            ->get();

        // Get all students
        $students = Student::with(['group', 'company'])->get();

        // Get all Supervisor marks
        $allSupervisorMarks = StudentAssessmentMark::whereIn('student_id', $students->pluck('id'))
            ->whereIn('assessment_id', $supervisorAssessments->pluck('id'))
            ->with('assessment')
            ->get()
            ->groupBy('student_id');

        // Get all IC marks
        $allIcMarks = StudentAssessmentMark::whereIn('student_id', $students->pluck('id'))
            ->whereIn('assessment_id', $icAssessments->pluck('id'))
            ->with('assessment')
            ->get()
            ->groupBy('student_id');

        // Calculate overall statistics
        $totalStudents = $students->count();
        $supervisorCompleted = 0;
        $icCompleted = 0;

        foreach ($students as $student) {
            // Check Supervisor completion
            $supervisorMarks = $allSupervisorMarks->get($student->id, collect());
            $supervisorCompletedCount = $supervisorMarks->filter(function ($mark) {
                return $mark->mark !== null;
            })->count();

            if ($supervisorCompletedCount === $supervisorAssessments->count() && $supervisorAssessments->count() > 0) {
                $supervisorCompleted++;
            }

            // Check IC completion
            $icMarks = $allIcMarks->get($student->id, collect());
            $icCompletedCount = $icMarks->filter(function ($mark) {
                return $mark->mark !== null;
            })->count();

            if ($icCompletedCount === $icAssessments->count() && $icAssessments->count() > 0) {
                $icCompleted++;
            }
        }

        $supervisorProgress = $totalStudents > 0 ? ($supervisorCompleted / $totalStudents) * 100 : 0;
        $icProgress = $totalStudents > 0 ? ($icCompleted / $totalStudents) * 100 : 0;
        $pendingSupervisor = $totalStudents - $supervisorCompleted;
        $pendingIc = $totalStudents - $icCompleted;

        // Group breakdown
        $groups = WblGroup::with('students')->get();
        $groupStats = $groups->map(function ($group) use ($allSupervisorMarks, $allIcMarks, $supervisorAssessments, $icAssessments) {
            $groupStudents = $group->students;
            $groupTotal = $groupStudents->count();
            $groupSupervisorCompleted = 0;
            $groupIcCompleted = 0;

            foreach ($groupStudents as $student) {
                // Supervisor completion
                $supervisorMarks = $allSupervisorMarks->get($student->id, collect());
                $supervisorCompletedCount = $supervisorMarks->filter(function ($mark) {
                    return $mark->mark !== null;
                })->count();

                if ($supervisorCompletedCount === $supervisorAssessments->count() && $supervisorAssessments->count() > 0) {
                    $groupSupervisorCompleted++;
                }

                // IC completion
                $icMarks = $allIcMarks->get($student->id, collect());
                $icCompletedCount = $icMarks->filter(function ($mark) {
                    return $mark->mark !== null;
                })->count();

                if ($icCompletedCount === $icAssessments->count() && $icAssessments->count() > 0) {
                    $groupIcCompleted++;
                }
            }

            return [
                'group' => $group,
                'total' => $groupTotal,
                'supervisor_completed' => $groupSupervisorCompleted,
                'ic_completed' => $groupIcCompleted,
                'supervisor_progress' => $groupTotal > 0 ? ($groupSupervisorCompleted / $groupTotal) * 100 : 0,
                'ic_progress' => $groupTotal > 0 ? ($groupIcCompleted / $groupTotal) * 100 : 0,
            ];
        });

        // Programme breakdown
        $programmes = Student::distinct()->pluck('programme')->filter();
        $programmeStats = $programmes->map(function ($programme) use ($allSupervisorMarks, $allIcMarks, $supervisorAssessments, $icAssessments) {
            $programmeStudents = Student::where('programme', $programme)->get();
            $programmeTotal = $programmeStudents->count();
            $programmeSupervisorCompleted = 0;
            $programmeIcCompleted = 0;

            foreach ($programmeStudents as $student) {
                // Supervisor completion
                $supervisorMarks = $allSupervisorMarks->get($student->id, collect());
                $supervisorCompletedCount = $supervisorMarks->filter(function ($mark) {
                    return $mark->mark !== null;
                })->count();

                if ($supervisorCompletedCount === $supervisorAssessments->count() && $supervisorAssessments->count() > 0) {
                    $programmeSupervisorCompleted++;
                }

                // IC completion
                $icMarks = $allIcMarks->get($student->id, collect());
                $icCompletedCount = $icMarks->filter(function ($mark) {
                    return $mark->mark !== null;
                })->count();

                if ($icCompletedCount === $icAssessments->count() && $icAssessments->count() > 0) {
                    $programmeIcCompleted++;
                }
            }

            return [
                'programme' => $programme,
                'total' => $programmeTotal,
                'supervisor_completed' => $programmeSupervisorCompleted,
                'ic_completed' => $programmeIcCompleted,
                'supervisor_progress' => $programmeTotal > 0 ? ($programmeSupervisorCompleted / $programmeTotal) * 100 : 0,
                'ic_progress' => $programmeTotal > 0 ? ($programmeIcCompleted / $programmeTotal) * 100 : 0,
            ];
        });

        // Company breakdown
        $companies = Student::whereNotNull('company_id')->with('company')->get()->groupBy('company_id');
        $companyStats = $companies->map(function ($companyStudents, $companyId) use ($allSupervisorMarks, $allIcMarks, $supervisorAssessments, $icAssessments) {
            $company = $companyStudents->first()->company;
            $companyTotal = $companyStudents->count();
            $companySupervisorCompleted = 0;
            $companyIcCompleted = 0;

            foreach ($companyStudents as $student) {
                // Supervisor completion
                $supervisorMarks = $allSupervisorMarks->get($student->id, collect());
                $supervisorCompletedCount = $supervisorMarks->filter(function ($mark) {
                    return $mark->mark !== null;
                })->count();

                if ($supervisorCompletedCount === $supervisorAssessments->count() && $supervisorAssessments->count() > 0) {
                    $companySupervisorCompleted++;
                }

                // IC completion
                $icMarks = $allIcMarks->get($student->id, collect());
                $icCompletedCount = $icMarks->filter(function ($mark) {
                    return $mark->mark !== null;
                })->count();

                if ($icCompletedCount === $icAssessments->count() && $icAssessments->count() > 0) {
                    $companyIcCompleted++;
                }
            }

            return [
                'company' => $company,
                'total' => $companyTotal,
                'supervisor_completed' => $companySupervisorCompleted,
                'ic_completed' => $companyIcCompleted,
                'supervisor_progress' => $companyTotal > 0 ? ($companySupervisorCompleted / $companyTotal) * 100 : 0,
                'ic_progress' => $companyTotal > 0 ? ($companyIcCompleted / $companyTotal) * 100 : 0,
            ];
        });

        return view('academic.li.progress.index', compact(
            'totalStudents',
            'supervisorCompleted',
            'icCompleted',
            'supervisorProgress',
            'icProgress',
            'pendingSupervisor',
            'pendingIc',
            'groupStats',
            'programmeStats',
            'companyStats'
        ));
    }
}
