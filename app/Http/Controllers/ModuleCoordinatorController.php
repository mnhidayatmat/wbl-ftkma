<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\WblGroup;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class ModuleCoordinatorController extends Controller
{
    /**
     * Show the coordinator dashboard based on their module
     */
    public function dashboard(): View
    {
        $user = auth()->user();

        // Determine which module dashboard to show
        if ($user->isFypCoordinator()) {
            return $this->fypDashboard();
        } elseif ($user->isIpCoordinator()) {
            return $this->ipDashboard();
        } elseif ($user->isOshCoordinator()) {
            return $this->oshDashboard();
        } elseif ($user->isPpeCoordinator()) {
            return $this->ppeDashboard();
        } elseif ($user->isLiCoordinator()) {
            return $this->liDashboard();
        }

        abort(403, 'You do not have coordinator access to any module.');
    }

    /**
     * FYP Coordinator Dashboard
     */
    private function fypDashboard(): View
    {
        $groups = WblGroup::where('status', 'ACTIVE')->get();
        $students = Student::inActiveGroups()->with(['group', 'academicTutor', 'industryCoach'])->get();

        // Get FYP statistics
        $stats = [
            'total_students' => $students->count(),
            'total_groups' => $groups->count(),
            'with_at' => $students->whereNotNull('at_id')->count(),
            'with_ic' => $students->whereNotNull('ic_id')->count(),
            'completion_rate' => 0, // Calculate based on finalizations
        ];

        // Get marks submission status
        $marksStatus = $this->getFypMarksStatus($students);

        return view('coordinators.fyp.dashboard', compact('stats', 'marksStatus', 'groups'));
    }

    /**
     * IP Coordinator Dashboard
     */
    private function ipDashboard(): View
    {
        $groups = WblGroup::where('status', 'ACTIVE')->get();
        $students = Student::inActiveGroups()->with(['group'])->get();

        $stats = [
            'total_students' => $students->count(),
            'total_groups' => $groups->count(),
            'completion_rate' => 0,
        ];

        $marksStatus = $this->getIpMarksStatus($students);

        return view('coordinators.ip.dashboard', compact('stats', 'marksStatus', 'groups'));
    }

    /**
     * OSH Coordinator Dashboard
     */
    private function oshDashboard(): View
    {
        $groups = WblGroup::where('status', 'ACTIVE')->get();
        $students = Student::inActiveGroups()->with(['group'])->get();

        $stats = [
            'total_students' => $students->count(),
            'total_groups' => $groups->count(),
            'completion_rate' => 0,
        ];

        $marksStatus = $this->getOshMarksStatus($students);

        return view('coordinators.osh.dashboard', compact('stats', 'marksStatus', 'groups'));
    }

    /**
     * PPE Coordinator Dashboard
     */
    private function ppeDashboard(): View
    {
        $groups = WblGroup::where('status', 'ACTIVE')->get();
        $students = Student::inActiveGroups()->with(['group'])->get();

        $stats = [
            'total_students' => $students->count(),
            'total_groups' => $groups->count(),
            'completion_rate' => 0,
        ];

        $marksStatus = $this->getPpeMarksStatus($students);

        return view('coordinators.ppe.dashboard', compact('stats', 'marksStatus', 'groups'));
    }

    /**
     * LI Coordinator Dashboard
     */
    private function liDashboard(): View
    {
        $groups = WblGroup::where('status', 'ACTIVE')->get();
        $students = Student::inActiveGroups()->with(['group'])->get();

        $stats = [
            'total_students' => $students->count(),
            'total_groups' => $groups->count(),
            'completion_rate' => 0,
        ];

        $marksStatus = $this->getLiMarksStatus($students);

        return view('coordinators.li.dashboard', compact('stats', 'marksStatus', 'groups'));
    }

    /**
     * Get FYP marks submission status
     */
    private function getFypMarksStatus($students)
    {
        $atSubmitted = 0;
        $icSubmitted = 0;
        $total = $students->count();

        try {
            // Check if tables exist
            $atTableExists = DB::getSchemaBuilder()->hasTable('fyp_student_at_marks');
            $icTableExists = DB::getSchemaBuilder()->hasTable('fyp_student_ic_marks');

            foreach ($students as $student) {
                // Check if AT marks exist
                if ($student->at_id && $atTableExists) {
                    $hasAtMarks = DB::table('fyp_student_at_marks')
                        ->where('student_id', $student->id)
                        ->exists();
                    if ($hasAtMarks) $atSubmitted++;
                }

                // Check if IC marks exist
                if ($student->ic_id && $icTableExists) {
                    $hasIcMarks = DB::table('fyp_student_ic_marks')
                        ->where('student_id', $student->id)
                        ->exists();
                    if ($hasIcMarks) $icSubmitted++;
                }
            }
        } catch (\Exception $e) {
            // If there's any error, just return zeros
        }

        return [
            'at_submitted' => $atSubmitted,
            'at_pending' => $students->whereNotNull('at_id')->count() - $atSubmitted,
            'ic_submitted' => $icSubmitted,
            'ic_pending' => $students->whereNotNull('ic_id')->count() - $icSubmitted,
            'total_students' => $total,
        ];
    }

    /**
     * Get IP marks submission status
     */
    private function getIpMarksStatus($students)
    {
        $submitted = 0;

        try {
            if (DB::getSchemaBuilder()->hasTable('ip_student_marks')) {
                $submitted = DB::table('ip_student_marks')
                    ->whereIn('student_id', $students->pluck('id'))
                    ->distinct('student_id')
                    ->count();
            }
        } catch (\Exception $e) {
            // Handle error silently
        }

        return [
            'submitted' => $submitted,
            'pending' => $students->count() - $submitted,
            'total_students' => $students->count(),
        ];
    }

    /**
     * Get OSH marks submission status
     */
    private function getOshMarksStatus($students)
    {
        $submitted = 0;

        try {
            if (DB::getSchemaBuilder()->hasTable('osh_student_marks')) {
                $submitted = DB::table('osh_student_marks')
                    ->whereIn('student_id', $students->pluck('id'))
                    ->distinct('student_id')
                    ->count();
            }
        } catch (\Exception $e) {
            // Handle error silently
        }

        return [
            'submitted' => $submitted,
            'pending' => $students->count() - $submitted,
            'total_students' => $students->count(),
        ];
    }

    /**
     * Get PPE marks submission status
     */
    private function getPpeMarksStatus($students)
    {
        $atSubmitted = 0;
        $icSubmitted = 0;
        $total = $students->count();

        try {
            $atTableExists = DB::getSchemaBuilder()->hasTable('ppe_student_at_marks');
            $icTableExists = DB::getSchemaBuilder()->hasTable('ppe_student_ic_marks');

            foreach ($students as $student) {
                if ($atTableExists) {
                    $hasAtMarks = DB::table('ppe_student_at_marks')
                        ->where('student_id', $student->id)
                        ->exists();
                    if ($hasAtMarks) $atSubmitted++;
                }

                if ($icTableExists) {
                    $hasIcMarks = DB::table('ppe_student_ic_marks')
                        ->where('student_id', $student->id)
                        ->exists();
                    if ($hasIcMarks) $icSubmitted++;
                }
            }
        } catch (\Exception $e) {
            // Handle error silently
        }

        return [
            'at_submitted' => $atSubmitted,
            'at_pending' => $total - $atSubmitted,
            'ic_submitted' => $icSubmitted,
            'ic_pending' => $total - $icSubmitted,
            'total_students' => $total,
        ];
    }

    /**
     * Get LI marks submission status
     */
    private function getLiMarksStatus($students)
    {
        $submitted = 0;

        try {
            if (DB::getSchemaBuilder()->hasTable('li_student_marks')) {
                $submitted = DB::table('li_student_marks')
                    ->whereIn('student_id', $students->pluck('id'))
                    ->distinct('student_id')
                    ->count();
            }
        } catch (\Exception $e) {
            // Handle error silently
        }

        return [
            'submitted' => $submitted,
            'pending' => $students->count() - $submitted,
            'total_students' => $students->count(),
        ];
    }
}
