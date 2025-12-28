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
        $students = Student::inActiveGroups()->with(['group', 'academicTutor', 'industryCoach', 'company'])->get();

        // Top KPI Statistics
        $stats = [
            'total_students' => $students->count(),
            'active_projects' => $students->count(), // Each student has a project
            'total_groups' => $groups->count(),
            'with_at' => $students->whereNotNull('at_id')->count(),
            'with_ic' => $students->whereNotNull('ic_id')->count(),
            'at_risk_count' => 0, // Will calculate below
            'completion_rate' => 0,
        ];

        // Supervisor assignment rates
        $stats['supervisor_assignment_rate'] = $students->count() > 0
            ? round((($stats['with_at'] + $stats['with_ic']) / ($students->count() * 2)) * 100)
            : 0;

        // Get marks submission status and assessment completion
        $marksStatus = $this->getFypMarksStatus($students);
        $stats['assessment_completion_rate'] = $this->calculateAssessmentCompletionRate($marksStatus);

        // Logbook status
        $logbookStatus = $this->getFypLogbookStatus($students);

        // At-risk students calculation
        $atRiskStudents = $this->getAtRiskStudents($students);
        $stats['at_risk_count'] = count($atRiskStudents);

        // Project status distribution
        $projectStatus = $this->getProjectStatus($students);

        // Assessment breakdown by type
        $assessmentBreakdown = $this->getAssessmentBreakdown($students);

        // Grade distribution
        $gradeDistribution = $this->getGradeDistribution($students);

        // Marks comparison (AT vs IC)
        $marksComparison = $this->getMarksComparison($students);

        // Supervisor assignment breakdown
        $supervisorStatus = [
            'at_assigned' => $stats['with_at'],
            'at_total' => $students->count(),
            'ic_assigned' => $stats['with_ic'],
            'ic_total' => $students->count(),
            'unassigned_students' => $students->filter(function($s) {
                return is_null($s->at_id) || is_null($s->ic_id);
            })->values(),
        ];

        // Project milestones
        $projectMilestones = $this->getProjectMilestones($students);

        // CLO/PLO mapping status
        $cloploStatus = $this->getCloploStatus($students);

        // Recent activities
        $recentActivities = $this->getRecentActivities($students);

        return view('coordinators.fyp.dashboard', compact(
            'stats',
            'marksStatus',
            'logbookStatus',
            'groups',
            'atRiskStudents',
            'projectStatus',
            'assessmentBreakdown',
            'gradeDistribution',
            'marksComparison',
            'supervisorStatus',
            'projectMilestones',
            'cloploStatus',
            'recentActivities'
        ));
    }

    /**
     * Calculate overall assessment completion rate
     */
    private function calculateAssessmentCompletionRate($marksStatus)
    {
        $total = ($marksStatus['at_submitted'] + $marksStatus['at_pending']) +
                 ($marksStatus['ic_submitted'] + $marksStatus['ic_pending']);
        $submitted = $marksStatus['at_submitted'] + $marksStatus['ic_submitted'];

        return $total > 0 ? round(($submitted / $total) * 100) : 0;
    }

    /**
     * Get FYP logbook status
     */
    private function getFypLogbookStatus($students)
    {
        $upToDate = 0;
        $pending = 0;

        try {
            if (DB::getSchemaBuilder()->hasTable('fyp_logbook_entries')) {
                foreach ($students as $student) {
                    $recentEntries = DB::table('fyp_logbook_entries')
                        ->where('student_id', $student->id)
                        ->where('created_at', '>=', now()->subDays(14))
                        ->count();

                    if ($recentEntries > 0) {
                        $upToDate++;
                    } else {
                        $pending++;
                    }
                }
            }
        } catch (\Exception $e) {
            // Handle error silently
        }

        return [
            'up_to_date' => $upToDate,
            'pending' => $pending,
            'compliance_rate' => $students->count() > 0 ? round(($upToDate / $students->count()) * 100) : 0,
        ];
    }

    /**
     * Get at-risk students
     */
    private function getAtRiskStudents($students)
    {
        $atRisk = [];

        foreach ($students as $student) {
            $issues = [];

            // Check for missing supervisors
            if (is_null($student->at_id)) {
                $issues[] = 'No Academic Tutor assigned';
            }
            if (is_null($student->ic_id)) {
                $issues[] = 'No Industry Coach assigned';
            }

            // Check for low marks (placeholder - would need actual marks data)
            // Check for missing logbook entries
            if (DB::getSchemaBuilder()->hasTable('fyp_logbook_entries')) {
                $recentEntries = DB::table('fyp_logbook_entries')
                    ->where('student_id', $student->id)
                    ->where('created_at', '>=', now()->subDays(30))
                    ->count();

                if ($recentEntries < 2) {
                    $issues[] = 'Insufficient logbook entries';
                }
            }

            if (count($issues) > 0) {
                $atRisk[] = [
                    'id' => $student->id,
                    'student_name' => $student->user->name ?? 'Unknown',
                    'matric_no' => $student->matric_no,
                    'group' => $student->group->name ?? 'N/A',
                    'issues' => $issues,
                    'issue_count' => count($issues),
                    'risk_level' => count($issues) >= 2 ? 'high' : 'medium',
                ];
            }
        }

        return $atRisk;
    }

    /**
     * Get project status distribution
     */
    private function getProjectStatus($students)
    {
        $status = [
            'proposal_pending' => 0,
            'proposal_approved' => 0,
            'in_progress' => 0,
            'final_report_submitted' => 0,
            'completed' => 0,
        ];

        // This would need actual project status tracking
        // For now, return estimated distribution
        $status['in_progress'] = $students->count();

        return [
            'labels' => ['Proposal Pending', 'Proposal Approved', 'In Progress', 'Final Report Submitted', 'Completed'],
            'data' => array_values($status),
            'colors' => ['#F59E0B', '#3B82F6', '#10B981', '#8B5CF6', '#6B7280'],
        ];
    }

    /**
     * Get assessment breakdown by type
     */
    private function getAssessmentBreakdown($students)
    {
        // Placeholder data - would need actual assessment tracking
        $total = $students->count();

        return [
            'at' => [
                'logbook' => ['submitted' => rand(0, $total), 'total' => $total],
                'progress' => ['submitted' => rand(0, $total), 'total' => $total],
                'final_report' => ['submitted' => rand(0, $total), 'total' => $total],
                'presentation' => ['submitted' => rand(0, $total), 'total' => $total],
            ],
            'ic' => [
                'logbook' => ['submitted' => rand(0, $total), 'total' => $total],
                'progress' => ['submitted' => rand(0, $total), 'total' => $total],
                'final_report' => ['submitted' => rand(0, $total), 'total' => $total],
                'presentation' => ['submitted' => rand(0, $total), 'total' => $total],
            ],
        ];
    }

    /**
     * Get grade distribution
     */
    private function getGradeDistribution($students)
    {
        // Placeholder - would calculate from actual marks
        return [
            'labels' => ['A (80-100)', 'B (70-79)', 'C (60-69)', 'D (50-59)', 'F (<50)'],
            'data' => [0, 0, 0, 0, 0],
            'colors' => ['#10B981', '#3B82F6', '#F59E0B', '#EF4444', '#6B7280'],
        ];
    }

    /**
     * Get AT vs IC marks comparison
     */
    private function getMarksComparison($students)
    {
        // Placeholder - would calculate from actual marks
        return [
            'labels' => ['Logbook', 'Progress', 'Final Report', 'Presentation'],
            'at_marks' => [0, 0, 0, 0],
            'ic_marks' => [0, 0, 0, 0],
        ];
    }

    /**
     * Get project milestones status
     */
    private function getProjectMilestones($students)
    {
        $total = $students->count();

        return [
            'proposal' => ['submitted' => 0, 'total' => $total],
            'progress_report_1' => ['submitted' => 0, 'total' => $total],
            'progress_report_2' => ['submitted' => 0, 'total' => $total],
            'final_report' => ['submitted' => 0, 'total' => $total],
            'presentation' => ['scheduled' => 0, 'total' => $total],
        ];
    }

    /**
     * Get CLO/PLO mapping status
     */
    private function getCloploStatus($students)
    {
        $total = $students->count();

        return [
            'clo_completed' => 0,
            'plo_completed' => 0,
            'pending_review' => 0,
            'total' => $total,
            'compliance_rate' => 0,
        ];
    }

    /**
     * Get recent activities
     */
    private function getRecentActivities($students)
    {
        $activities = [];

        // This would fetch actual recent submissions, evaluations, etc.
        // For now, return empty array

        return $activities;
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

    /**
     * Show coordinator profile
     */
    public function profile()
    {
        $user = auth()->user();

        // Determine coordinator type
        $coordinatorType = '';
        if ($user->isFypCoordinator()) {
            $coordinatorType = 'FYP';
        } elseif ($user->isIpCoordinator()) {
            $coordinatorType = 'IP';
        } elseif ($user->isOshCoordinator()) {
            $coordinatorType = 'OSH';
        } elseif ($user->isPpeCoordinator()) {
            $coordinatorType = 'PPE';
        } elseif ($user->isLiCoordinator()) {
            $coordinatorType = 'LI';
        }

        return view('coordinators.profile.show', compact('user', 'coordinatorType'));
    }

    /**
     * Update coordinator profile
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'current_password' => ['nullable', 'required_with:password', 'current_password'],
            'password' => ['nullable', 'min:8', 'confirmed'],
        ]);

        // Update user information
        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if (isset($validated['phone'])) {
            $user->phone = $validated['phone'];
        }

        // Update password if provided
        if (!empty($validated['password'])) {
            $user->password = bcrypt($validated['password']);
        }

        $user->save();

        return redirect()->route('coordinator.profile.show')
            ->with('success', 'Profile updated successfully!');
    }
}
