<?php

namespace App\Http\Controllers;

use App\Models\FYP\FypAssessmentWindow;
use App\Models\IP\IpAssessmentWindow;
use App\Models\LI\LiAssessmentWindow;
use App\Models\OSH\OshAssessmentWindow;
use App\Models\PPE\PpeAssessmentWindow;
use App\Models\Student;
use App\Models\WblGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

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
        $withBoth = $students->filter(fn ($s) => ! is_null($s->at_id) && ! is_null($s->ic_id))->count();
        $atOnly = $students->filter(fn ($s) => ! is_null($s->at_id) && is_null($s->ic_id))->count();
        $icOnly = $students->filter(fn ($s) => is_null($s->at_id) && ! is_null($s->ic_id))->count();
        $noAssignment = $students->filter(fn ($s) => is_null($s->at_id) && is_null($s->ic_id))->count();

        $supervisorStatus = [
            'with_both' => $withBoth,
            'at_only' => $atOnly,
            'ic_only' => $icOnly,
            'no_assignment' => $noAssignment,
            'at_assigned' => $stats['with_at'],
            'at_total' => $students->count(),
            'ic_assigned' => $stats['with_ic'],
            'ic_total' => $students->count(),
            'unassigned_students' => $students->filter(function ($s) {
                return is_null($s->at_id) || is_null($s->ic_id);
            })->values(),
        ];

        // Project milestones
        $projectMilestones = $this->getProjectMilestones($students);

        // CLO/PLO mapping status
        $cloploStatus = $this->getCloploStatus($students);

        // Recent activities
        $recentActivities = $this->getRecentActivities($students);

        // Assessment windows for timeline
        $atWindow = FypAssessmentWindow::where('evaluator_role', 'lecturer')->first();
        $icWindow = FypAssessmentWindow::where('evaluator_role', 'ic')->first();

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
            'recentActivities',
            'atWindow',
            'icWindow'
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
            'compliant' => $upToDate,
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
            'at_completed' => 0,
            'ic_completed' => 0,
            'at' => [
                'logbook' => ['submitted' => 0, 'total' => $total],
                'progress' => ['submitted' => 0, 'total' => $total],
                'final_report' => ['submitted' => 0, 'total' => $total],
                'presentation' => ['submitted' => 0, 'total' => $total],
            ],
            'ic' => [
                'logbook' => ['submitted' => 0, 'total' => $total],
                'progress' => ['submitted' => 0, 'total' => $total],
                'final_report' => ['submitted' => 0, 'total' => $total],
                'presentation' => ['submitted' => 0, 'total' => $total],
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
        $students = Student::inActiveGroups()->with(['group', 'academicTutor', 'industryCoach', 'company'])->get();

        // Top KPI Statistics
        $stats = [
            'total_students' => $students->count(),
            'active_evaluations' => $students->count(),
            'total_groups' => $groups->count(),
            'with_lecturer' => $students->whereNotNull('at_id')->count(),
            'with_ic' => $students->whereNotNull('ic_id')->count(),
            'at_risk_count' => 0,
            'completion_rate' => 0,
        ];

        // Supervisor assignment rates
        $stats['supervisor_assignment_rate'] = $students->count() > 0
            ? round((($stats['with_lecturer'] + $stats['with_ic']) / ($students->count() * 2)) * 100)
            : 0;

        // Get marks submission status
        $marksStatus = $this->getIpMarksStatus($students);
        $stats['assessment_completion_rate'] = $this->calculateIpAssessmentCompletionRate($marksStatus);

        // Logbook status
        $logbookStatus = $this->getIpLogbookStatus($students);

        // At-risk students calculation
        $atRiskStudents = $this->getIpAtRiskStudents($students);
        $stats['at_risk_count'] = count($atRiskStudents);

        // Evaluation status distribution
        $projectStatus = $this->getIpEvaluationStatus($students);

        // Assessment breakdown
        $assessmentBreakdown = $this->getIpAssessmentBreakdown($students);

        // Grade distribution
        $gradeDistribution = $this->getIpGradeDistribution($students);

        // Marks comparison (Lecturer vs IC)
        $marksComparison = $this->getIpMarksComparison($students);

        // Supervisor assignment breakdown
        $withBoth = $students->filter(fn ($s) => ! is_null($s->at_id) && ! is_null($s->ic_id))->count();
        $atOnly = $students->filter(fn ($s) => ! is_null($s->at_id) && is_null($s->ic_id))->count();
        $icOnly = $students->filter(fn ($s) => is_null($s->at_id) && ! is_null($s->ic_id))->count();
        $noAssignment = $students->filter(fn ($s) => is_null($s->at_id) && is_null($s->ic_id))->count();

        $supervisorStatus = [
            'with_both' => $withBoth,
            'at_only' => $atOnly,
            'ic_only' => $icOnly,
            'no_assignment' => $noAssignment,
            'lecturer_assigned' => $stats['with_lecturer'],
            'lecturer_total' => $students->count(),
            'ic_assigned' => $stats['with_ic'],
            'ic_total' => $students->count(),
            'unassigned_students' => $students->filter(function ($s) {
                return is_null($s->at_id) || is_null($s->ic_id);
            })->values(),
        ];

        // Evaluation milestones
        $projectMilestones = $this->getIpMilestones($students);

        // CLO/PLO mapping status
        $cloploStatus = $this->getIpCloploStatus($students);

        // Recent activities
        $recentActivities = $this->getIpRecentActivities($students);

        // Assessment windows for timeline
        $atWindow = IpAssessmentWindow::where('evaluator_role', 'lecturer')->first();
        $icWindow = IpAssessmentWindow::where('evaluator_role', 'ic')->first();

        return view('coordinators.ip.dashboard', compact(
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
            'recentActivities',
            'atWindow',
            'icWindow'
        ));
    }

    /**
     * OSH Coordinator Dashboard
     */
    private function oshDashboard(): View
    {
        $groups = WblGroup::where('status', 'ACTIVE')->get();
        $students = Student::inActiveGroups()->with(['group', 'academicTutor', 'industryCoach', 'company'])->get();

        // Top KPI Statistics
        $stats = [
            'total_students' => $students->count(),
            'active_evaluations' => $students->count(),
            'total_groups' => $groups->count(),
            'with_lecturer' => $students->whereNotNull('at_id')->count(),
            'with_ic' => $students->whereNotNull('ic_id')->count(),
            'at_risk_count' => 0,
            'completion_rate' => 0,
        ];

        // Supervisor assignment rates
        $stats['supervisor_assignment_rate'] = $students->count() > 0
            ? round((($stats['with_lecturer'] + $stats['with_ic']) / ($students->count() * 2)) * 100)
            : 0;

        // Get marks submission status
        $marksStatus = $this->getOshMarksStatus($students);
        $stats['assessment_completion_rate'] = $this->calculateOshAssessmentCompletionRate($marksStatus);

        // Logbook status
        $logbookStatus = $this->getOshLogbookStatus($students);

        // At-risk students calculation
        $atRiskStudents = $this->getOshAtRiskStudents($students);
        $stats['at_risk_count'] = count($atRiskStudents);

        // Evaluation status distribution
        $projectStatus = $this->getOshEvaluationStatus($students);

        // Assessment breakdown
        $assessmentBreakdown = $this->getOshAssessmentBreakdown($students);

        // Grade distribution
        $gradeDistribution = $this->getOshGradeDistribution($students);

        // Marks comparison (Lecturer vs IC)
        $marksComparison = $this->getOshMarksComparison($students);

        // Supervisor assignment breakdown
        $withBoth = $students->filter(fn ($s) => ! is_null($s->at_id) && ! is_null($s->ic_id))->count();
        $atOnly = $students->filter(fn ($s) => ! is_null($s->at_id) && is_null($s->ic_id))->count();
        $icOnly = $students->filter(fn ($s) => is_null($s->at_id) && ! is_null($s->ic_id))->count();
        $noAssignment = $students->filter(fn ($s) => is_null($s->at_id) && is_null($s->ic_id))->count();

        $supervisorStatus = [
            'with_both' => $withBoth,
            'at_only' => $atOnly,
            'ic_only' => $icOnly,
            'no_assignment' => $noAssignment,
            'lecturer_assigned' => $stats['with_lecturer'],
            'lecturer_total' => $students->count(),
            'ic_assigned' => $stats['with_ic'],
            'ic_total' => $students->count(),
            'unassigned_students' => $students->filter(function ($s) {
                return is_null($s->at_id) || is_null($s->ic_id);
            })->values(),
        ];

        // Evaluation milestones
        $projectMilestones = $this->getOshMilestones($students);

        // CLO/PLO mapping status
        $cloploStatus = $this->getOshCloploStatus($students);

        // Recent activities
        $recentActivities = $this->getOshRecentActivities($students);

        // Assessment windows for timeline
        $atWindow = OshAssessmentWindow::where('evaluator_role', 'lecturer')->first();
        $icWindow = OshAssessmentWindow::where('evaluator_role', 'ic')->first();

        return view('coordinators.osh.dashboard', compact(
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
            'recentActivities',
            'atWindow',
            'icWindow'
        ));
    }

    /**
     * PPE Coordinator Dashboard
     */
    private function ppeDashboard(): View
    {
        $groups = WblGroup::where('status', 'ACTIVE')->get();
        $students = Student::inActiveGroups()->with(['group', 'academicTutor', 'industryCoach', 'company'])->get();

        // Top KPI Statistics
        $stats = [
            'total_students' => $students->count(),
            'active_evaluations' => $students->count(),
            'total_groups' => $groups->count(),
            'with_at' => $students->whereNotNull('at_id')->count(),
            'with_ic' => $students->whereNotNull('ic_id')->count(),
            'at_risk_count' => 0,
            'completion_rate' => 0,
        ];

        // Supervisor assignment rates
        $stats['supervisor_assignment_rate'] = $students->count() > 0
            ? round((($stats['with_at'] + $stats['with_ic']) / ($students->count() * 2)) * 100)
            : 0;

        // Get marks submission status
        $marksStatus = $this->getPpeMarksStatus($students);
        $stats['assessment_completion_rate'] = $this->calculatePpeAssessmentCompletionRate($marksStatus);

        // Logbook status
        $logbookStatus = $this->getPpeLogbookStatus($students);

        // At-risk students calculation
        $atRiskStudents = $this->getPpeAtRiskStudents($students);
        $stats['at_risk_count'] = count($atRiskStudents);

        // Evaluation status distribution
        $projectStatus = $this->getPpeEvaluationStatus($students);

        // Assessment breakdown by type
        $assessmentBreakdown = $this->getPpeAssessmentBreakdown($students);

        // Grade distribution
        $gradeDistribution = $this->getPpeGradeDistribution($students);

        // Marks comparison (AT vs IC)
        $marksComparison = $this->getPpeMarksComparison($students);

        // Supervisor assignment breakdown
        $supervisorStatus = [
            'at_assigned' => $stats['with_at'],
            'at_total' => $students->count(),
            'ic_assigned' => $stats['with_ic'],
            'ic_total' => $students->count(),
            'unassigned_students' => $students->filter(function ($s) {
                return is_null($s->at_id) || is_null($s->ic_id);
            })->values(),
        ];

        // Evaluation milestones
        $projectMilestones = $this->getPpeMilestones($students);

        // CLO/PLO mapping status
        $cloploStatus = $this->getPpeCloploStatus($students);

        // Recent activities
        $recentActivities = $this->getPpeRecentActivities($students);

        // Assessment windows for timeline
        $atWindow = PpeAssessmentWindow::where('evaluator_role', 'lecturer')->first();
        $icWindow = PpeAssessmentWindow::where('evaluator_role', 'ic')->first();

        return view('coordinators.ppe.dashboard', compact(
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
            'recentActivities',
            'atWindow',
            'icWindow'
        ));
    }

    /**
     * LI Coordinator Dashboard
     */
    private function liDashboard(): View
    {
        $groups = WblGroup::where('status', 'ACTIVE')->get();
        $students = Student::inActiveGroups()->with(['group', 'academicTutor', 'industryCoach', 'company'])->get();

        // Top KPI Statistics (LI uses supervisor_li instead of AT)
        $stats = [
            'total_students' => $students->count(),
            'active_evaluations' => $students->count(),
            'total_groups' => $groups->count(),
            'with_supervisor' => $students->whereNotNull('at_id')->count(), // supervisor_li stored in at_id
            'with_ic' => $students->whereNotNull('ic_id')->count(),
            'at_risk_count' => 0,
            'completion_rate' => 0,
        ];

        // Supervisor assignment rates
        $stats['supervisor_assignment_rate'] = $students->count() > 0
            ? round((($stats['with_supervisor'] + $stats['with_ic']) / ($students->count() * 2)) * 100)
            : 0;

        // Get marks submission status
        $marksStatus = $this->getLiMarksStatus($students);
        $stats['assessment_completion_rate'] = $this->calculateLiAssessmentCompletionRate($marksStatus);

        // Logbook status
        $logbookStatus = $this->getLiLogbookStatus($students);

        // At-risk students calculation
        $atRiskStudents = $this->getLiAtRiskStudents($students);
        $stats['at_risk_count'] = count($atRiskStudents);

        // Evaluation status distribution
        $projectStatus = $this->getLiEvaluationStatus($students);

        // Assessment breakdown
        $assessmentBreakdown = $this->getLiAssessmentBreakdown($students);

        // Grade distribution
        $gradeDistribution = $this->getLiGradeDistribution($students);

        // Marks comparison (Supervisor LI vs IC)
        $marksComparison = $this->getLiMarksComparison($students);

        // Supervisor assignment breakdown
        $withBoth = $students->filter(fn ($s) => ! is_null($s->at_id) && ! is_null($s->ic_id))->count();
        $supervisorOnly = $students->filter(fn ($s) => ! is_null($s->at_id) && is_null($s->ic_id))->count();
        $icOnly = $students->filter(fn ($s) => is_null($s->at_id) && ! is_null($s->ic_id))->count();
        $noAssignment = $students->filter(fn ($s) => is_null($s->at_id) && is_null($s->ic_id))->count();

        $supervisorStatus = [
            'with_both' => $withBoth,
            'supervisor_only' => $supervisorOnly,
            'ic_only' => $icOnly,
            'no_assignment' => $noAssignment,
            'supervisor_assigned' => $stats['with_supervisor'],
            'supervisor_total' => $students->count(),
            'ic_assigned' => $stats['with_ic'],
            'ic_total' => $students->count(),
            'unassigned_students' => $students->filter(function ($s) {
                return is_null($s->at_id) || is_null($s->ic_id);
            })->values(),
        ];

        // Evaluation milestones
        $projectMilestones = $this->getLiMilestones($students);

        // CLO/PLO mapping status
        $cloploStatus = $this->getLiCloploStatus($students);

        // Recent activities
        $recentActivities = $this->getLiRecentActivities($students);

        // Assessment windows for timeline (LI uses supervisor instead of lecturer)
        $atWindow = LiAssessmentWindow::where('evaluator_role', 'lecturer')->first();
        $icWindow = LiAssessmentWindow::where('evaluator_role', 'ic')->first();

        return view('coordinators.li.dashboard', compact(
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
            'recentActivities',
            'atWindow',
            'icWindow'
        ));
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
                    if ($hasAtMarks) {
                        $atSubmitted++;
                    }
                }

                // Check if IC marks exist
                if ($student->ic_id && $icTableExists) {
                    $hasIcMarks = DB::table('fyp_student_ic_marks')
                        ->where('student_id', $student->id)
                        ->exists();
                    if ($hasIcMarks) {
                        $icSubmitted++;
                    }
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
                    if ($hasAtMarks) {
                        $atSubmitted++;
                    }
                }

                if ($icTableExists) {
                    $hasIcMarks = DB::table('ppe_student_ic_marks')
                        ->where('student_id', $student->id)
                        ->exists();
                    if ($hasIcMarks) {
                        $icSubmitted++;
                    }
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

    // =========================================================================
    // PPE Helper Methods
    // =========================================================================

    private function calculatePpeAssessmentCompletionRate($marksStatus)
    {
        $total = ($marksStatus['at_submitted'] + $marksStatus['at_pending']) +
                 ($marksStatus['ic_submitted'] + $marksStatus['ic_pending']);
        $submitted = $marksStatus['at_submitted'] + $marksStatus['ic_submitted'];

        return $total > 0 ? round(($submitted / $total) * 100) : 0;
    }

    private function getPpeLogbookStatus($students)
    {
        $upToDate = 0;
        $pending = 0;
        try {
            if (DB::getSchemaBuilder()->hasTable('ppe_logbook_entries')) {
                foreach ($students as $student) {
                    $recentEntries = DB::table('ppe_logbook_entries')
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
        }

        return [
            'up_to_date' => $upToDate,
            'pending' => $pending,
            'compliance_rate' => $students->count() > 0 ? round(($upToDate / $students->count()) * 100) : 0,
        ];
    }

    private function getPpeAtRiskStudents($students)
    {
        $atRisk = [];
        foreach ($students as $student) {
            $issues = [];
            if (is_null($student->at_id)) {
                $issues[] = 'No Academic Tutor assigned';
            }
            if (is_null($student->ic_id)) {
                $issues[] = 'No Industry Coach assigned';
            }
            if (count($issues) > 0) {
                $atRisk[] = [
                    'id' => $student->id,
                    'student_name' => $student->user->name ?? $student->name ?? 'Unknown',
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

    private function getPpeEvaluationStatus($students)
    {
        return [
            'labels' => ['Not Started', 'AT Completed', 'IC Completed', 'Both Completed'],
            'data' => [$students->count(), 0, 0, 0],
            'colors' => ['#F59E0B', '#3B82F6', '#10B981', '#8B5CF6'],
        ];
    }

    private function getPpeAssessmentBreakdown($students)
    {
        $total = $students->count();

        return [
            'at' => ['submitted' => 0, 'total' => $total],
            'ic' => ['submitted' => 0, 'total' => $total],
        ];
    }

    private function getPpeGradeDistribution($students)
    {
        return [
            'labels' => ['A (80-100)', 'B (70-79)', 'C (60-69)', 'D (50-59)', 'F (<50)'],
            'data' => [0, 0, 0, 0, 0],
            'colors' => ['#10B981', '#3B82F6', '#F59E0B', '#EF4444', '#6B7280'],
        ];
    }

    private function getPpeMarksComparison($students)
    {
        return [
            'labels' => ['Assessment 1', 'Assessment 2', 'Assessment 3', 'Final'],
            'at_marks' => [0, 0, 0, 0],
            'ic_marks' => [0, 0, 0, 0],
        ];
    }

    private function getPpeMilestones($students)
    {
        $total = $students->count();

        return [
            'at_evaluation' => ['submitted' => 0, 'total' => $total],
            'ic_evaluation' => ['submitted' => 0, 'total' => $total],
            'logbook' => ['submitted' => 0, 'total' => $total],
            'final_report' => ['submitted' => 0, 'total' => $total],
        ];
    }

    private function getPpeCloploStatus($students)
    {
        return [
            'clo_completed' => 0,
            'plo_completed' => 0,
            'pending_review' => 0,
            'total' => $students->count(),
            'compliance_rate' => 0,
        ];
    }

    private function getPpeRecentActivities($students)
    {
        return [];
    }

    // =========================================================================
    // IP Helper Methods
    // =========================================================================

    private function calculateIpAssessmentCompletionRate($marksStatus)
    {
        $total = $marksStatus['total_students'];
        $submitted = $marksStatus['submitted'];

        return $total > 0 ? round(($submitted / $total) * 100) : 0;
    }

    private function getIpLogbookStatus($students)
    {
        $upToDate = 0;
        $pending = 0;
        try {
            if (DB::getSchemaBuilder()->hasTable('ip_logbook_entries')) {
                foreach ($students as $student) {
                    $recentEntries = DB::table('ip_logbook_entries')
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
        }

        return [
            'up_to_date' => $upToDate,
            'compliant' => $upToDate,
            'pending' => $pending,
            'compliance_rate' => $students->count() > 0 ? round(($upToDate / $students->count()) * 100) : 0,
        ];
    }

    private function getIpAtRiskStudents($students)
    {
        $atRisk = [];
        foreach ($students as $student) {
            $issues = [];
            if (is_null($student->at_id)) {
                $issues[] = 'No Lecturer assigned';
            }
            if (is_null($student->ic_id)) {
                $issues[] = 'No Industry Coach assigned';
            }
            if (count($issues) > 0) {
                $atRisk[] = [
                    'id' => $student->id,
                    'student_name' => $student->user->name ?? $student->name ?? 'Unknown',
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

    private function getIpEvaluationStatus($students)
    {
        return [
            'labels' => ['Not Started', 'Lecturer Completed', 'IC Completed', 'Both Completed'],
            'data' => [$students->count(), 0, 0, 0],
            'colors' => ['#F59E0B', '#3B82F6', '#10B981', '#8B5CF6'],
        ];
    }

    private function getIpAssessmentBreakdown($students)
    {
        $total = $students->count();

        return [
            'lecturer' => ['submitted' => 0, 'total' => $total],
            'ic' => ['submitted' => 0, 'total' => $total],
            'at_completed' => 0,
            'ic_completed' => 0,
        ];
    }

    private function getIpGradeDistribution($students)
    {
        return [
            'labels' => ['A (80-100)', 'B (70-79)', 'C (60-69)', 'D (50-59)', 'F (<50)'],
            'data' => [0, 0, 0, 0, 0],
            'colors' => ['#10B981', '#3B82F6', '#F59E0B', '#EF4444', '#6B7280'],
        ];
    }

    private function getIpMarksComparison($students)
    {
        return [
            'labels' => ['Assessment 1', 'Assessment 2', 'Assessment 3', 'Final'],
            'lecturer_marks' => [0, 0, 0, 0],
            'ic_marks' => [0, 0, 0, 0],
        ];
    }

    private function getIpMilestones($students)
    {
        $total = $students->count();

        return [
            'lecturer_evaluation' => ['submitted' => 0, 'total' => $total],
            'ic_evaluation' => ['submitted' => 0, 'total' => $total],
            'logbook' => ['submitted' => 0, 'total' => $total],
            'final_report' => ['submitted' => 0, 'total' => $total],
        ];
    }

    private function getIpCloploStatus($students)
    {
        return [
            'clo_completed' => 0,
            'plo_completed' => 0,
            'pending_review' => 0,
            'total' => $students->count(),
            'compliance_rate' => 0,
        ];
    }

    private function getIpRecentActivities($students)
    {
        return [];
    }

    // =========================================================================
    // OSH Helper Methods
    // =========================================================================

    private function calculateOshAssessmentCompletionRate($marksStatus)
    {
        $total = $marksStatus['total_students'];
        $submitted = $marksStatus['submitted'];

        return $total > 0 ? round(($submitted / $total) * 100) : 0;
    }

    private function getOshLogbookStatus($students)
    {
        $upToDate = 0;
        $pending = 0;
        try {
            if (DB::getSchemaBuilder()->hasTable('osh_logbook_entries')) {
                foreach ($students as $student) {
                    $recentEntries = DB::table('osh_logbook_entries')
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
        }

        return [
            'up_to_date' => $upToDate,
            'compliant' => $upToDate,
            'pending' => $pending,
            'compliance_rate' => $students->count() > 0 ? round(($upToDate / $students->count()) * 100) : 0,
        ];
    }

    private function getOshAtRiskStudents($students)
    {
        $atRisk = [];
        foreach ($students as $student) {
            $issues = [];
            if (is_null($student->at_id)) {
                $issues[] = 'No Lecturer assigned';
            }
            if (is_null($student->ic_id)) {
                $issues[] = 'No Industry Coach assigned';
            }
            if (count($issues) > 0) {
                $atRisk[] = [
                    'id' => $student->id,
                    'student_name' => $student->user->name ?? $student->name ?? 'Unknown',
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

    private function getOshEvaluationStatus($students)
    {
        return [
            'labels' => ['Not Started', 'Lecturer Completed', 'IC Completed', 'Both Completed'],
            'data' => [$students->count(), 0, 0, 0],
            'colors' => ['#F59E0B', '#3B82F6', '#10B981', '#8B5CF6'],
        ];
    }

    private function getOshAssessmentBreakdown($students)
    {
        $total = $students->count();

        return [
            'lecturer' => ['submitted' => 0, 'total' => $total],
            'ic' => ['submitted' => 0, 'total' => $total],
            'at_completed' => 0,
            'ic_completed' => 0,
        ];
    }

    private function getOshGradeDistribution($students)
    {
        return [
            'labels' => ['A (80-100)', 'B (70-79)', 'C (60-69)', 'D (50-59)', 'F (<50)'],
            'data' => [0, 0, 0, 0, 0],
            'colors' => ['#10B981', '#3B82F6', '#F59E0B', '#EF4444', '#6B7280'],
        ];
    }

    private function getOshMarksComparison($students)
    {
        return [
            'labels' => ['Assessment 1', 'Assessment 2', 'Assessment 3', 'Final'],
            'lecturer_marks' => [0, 0, 0, 0],
            'ic_marks' => [0, 0, 0, 0],
        ];
    }

    private function getOshMilestones($students)
    {
        $total = $students->count();

        return [
            'lecturer_evaluation' => ['submitted' => 0, 'total' => $total],
            'ic_evaluation' => ['submitted' => 0, 'total' => $total],
            'logbook' => ['submitted' => 0, 'total' => $total],
            'final_report' => ['submitted' => 0, 'total' => $total],
        ];
    }

    private function getOshCloploStatus($students)
    {
        return [
            'clo_completed' => 0,
            'plo_completed' => 0,
            'pending_review' => 0,
            'total' => $students->count(),
            'compliance_rate' => 0,
        ];
    }

    private function getOshRecentActivities($students)
    {
        return [];
    }

    // =========================================================================
    // LI Helper Methods
    // =========================================================================

    private function calculateLiAssessmentCompletionRate($marksStatus)
    {
        $total = $marksStatus['total_students'];
        $submitted = $marksStatus['submitted'];

        return $total > 0 ? round(($submitted / $total) * 100) : 0;
    }

    private function getLiLogbookStatus($students)
    {
        $upToDate = 0;
        $pending = 0;
        try {
            if (DB::getSchemaBuilder()->hasTable('li_logbook_entries')) {
                foreach ($students as $student) {
                    $recentEntries = DB::table('li_logbook_entries')
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
        }

        return [
            'up_to_date' => $upToDate,
            'compliant' => $upToDate,
            'pending' => $pending,
            'compliance_rate' => $students->count() > 0 ? round(($upToDate / $students->count()) * 100) : 0,
        ];
    }

    private function getLiAtRiskStudents($students)
    {
        $atRisk = [];
        foreach ($students as $student) {
            $issues = [];
            if (is_null($student->at_id)) {
                $issues[] = 'No Supervisor LI assigned';
            }
            if (is_null($student->ic_id)) {
                $issues[] = 'No Industry Coach assigned';
            }
            if (count($issues) > 0) {
                $atRisk[] = [
                    'id' => $student->id,
                    'student_name' => $student->user->name ?? $student->name ?? 'Unknown',
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

    private function getLiEvaluationStatus($students)
    {
        return [
            'labels' => ['Not Started', 'Supervisor Completed', 'IC Completed', 'Both Completed'],
            'data' => [$students->count(), 0, 0, 0],
            'colors' => ['#F59E0B', '#3B82F6', '#10B981', '#8B5CF6'],
        ];
    }

    private function getLiAssessmentBreakdown($students)
    {
        $total = $students->count();

        return [
            'supervisor_completed' => 0,
            'ic_completed' => 0,
            'supervisor' => ['submitted' => 0, 'total' => $total],
            'ic' => ['submitted' => 0, 'total' => $total],
        ];
    }

    private function getLiGradeDistribution($students)
    {
        return [
            'labels' => ['A (80-100)', 'B (70-79)', 'C (60-69)', 'D (50-59)', 'F (<50)'],
            'data' => [0, 0, 0, 0, 0],
            'colors' => ['#10B981', '#3B82F6', '#F59E0B', '#EF4444', '#6B7280'],
        ];
    }

    private function getLiMarksComparison($students)
    {
        return [
            'labels' => ['Assessment 1', 'Assessment 2', 'Assessment 3', 'Final'],
            'supervisor_marks' => [0, 0, 0, 0],
            'ic_marks' => [0, 0, 0, 0],
        ];
    }

    private function getLiMilestones($students)
    {
        $total = $students->count();

        return [
            'supervisor_evaluation' => ['submitted' => 0, 'total' => $total],
            'ic_evaluation' => ['submitted' => 0, 'total' => $total],
            'logbook' => ['submitted' => 0, 'total' => $total],
            'final_report' => ['submitted' => 0, 'total' => $total],
        ];
    }

    private function getLiCloploStatus($students)
    {
        return [
            'clo_completed' => 0,
            'plo_completed' => 0,
            'pending_review' => 0,
            'total' => $students->count(),
            'compliance_rate' => 0,
        ];
    }

    private function getLiRecentActivities($students)
    {
        return [];
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
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
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
        if (! empty($validated['password'])) {
            $user->password = bcrypt($validated['password']);
        }

        $user->save();

        return redirect()->route('coordinator.profile.show')
            ->with('success', 'Profile updated successfully!');
    }
}
