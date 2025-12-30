<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\Company;
use App\Models\CompanyAgreement;
use App\Models\CompanyNote;
use App\Models\FYP\FypAssessmentWindow;
use App\Models\IP\IpAssessmentWindow;
use App\Models\LI\LiAssessmentWindow;
use App\Models\OSH\OshAssessmentWindow;
use App\Models\PPE\PpeAssessmentWindow;
use App\Models\PPE\PpeStudentAtMark;
use App\Models\PPE\PpeStudentIcMark;
use App\Models\Student;
use App\Models\StudentAssessmentMark;
use App\Models\StudentPlacementTracking;
use App\Models\StudentResumeInspection;
use App\Models\WblGroup;
use App\Models\WorkplaceIssueReport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index(Request $request): View|RedirectResponse
    {
        $user = auth()->user();

        // If user is a student, show student dashboard
        if ($user->isStudent()) {
            return $this->studentDashboard();
        }

        // Admin Dashboard
        if ($user->isAdmin()) {
            return $this->adminDashboard($request);
        }

        // Module Coordinator Dashboard - redirect to coordinator dashboard
        if ($user->isFypCoordinator() || $user->isIpCoordinator() ||
            $user->isOshCoordinator() || $user->isPpeCoordinator() ||
            $user->isLiCoordinator()) {
            return redirect()->route('coordinator.dashboard');
        }

        // Default dashboard for other roles
        return $this->staffDashboard($request);
    }

    /**
     * Admin Dashboard with comprehensive KPIs.
     */
    private function adminDashboard(Request $request): View
    {
        $groupFilter = $request->get('group_filter', 'active');
        $sessionFilter = $request->get('session', 'all');

        // =====================================================
        // 1. TOP KPI SUMMARY CARDS
        // =====================================================

        // Total Students (with comparison)
        $totalStudents = Student::count();
        $activeStudents = Student::inActiveGroups()->count();
        $completedStudents = Student::inCompletedGroups()->count();

        // Active Groups
        $totalGroups = WblGroup::count();
        $activeGroups = WblGroup::where('status', 'ACTIVE')->count();
        $completedGroups = WblGroup::where('status', 'COMPLETED')->count();

        // Active Companies
        $totalCompanies = Company::count();
        $companiesWithStudents = Company::has('students')->count();

        // Active Agreements (MoU/MoA/LOI)
        $agreementStats = CompanyAgreement::getSummaryStats();
        $totalActiveAgreements = $agreementStats['total_active'];

        // Placement Completion Rate
        $placementStats = $this->getPlacementStats();

        $kpiCards = [
            'students' => [
                'total' => $totalStudents,
                'active' => $activeStudents,
                'completed' => $completedStudents,
                'tooltip' => 'Total students enrolled in WBL programmes',
            ],
            'groups' => [
                'total' => $totalGroups,
                'active' => $activeGroups,
                'completed' => $completedGroups,
                'tooltip' => 'Student cohort groups (Active groups are currently in placement)',
            ],
            'companies' => [
                'total' => $totalCompanies,
                'with_students' => $companiesWithStudents,
                'tooltip' => 'Industry partners hosting WBL students',
            ],
            'agreements' => [
                'total' => $totalActiveAgreements,
                'mou' => $agreementStats['total_mou_active'],
                'moa' => $agreementStats['total_moa_active'],
                'loi' => $agreementStats['total_loi_active'],
                'expiring_soon' => $agreementStats['expiring_3_months'],
                'tooltip' => 'Active formal agreements with industry partners',
            ],
            'placement' => [
                'completion_rate' => $placementStats['completion_rate'],
                'confirmed' => $placementStats['confirmed'],
                'pending' => $placementStats['pending'],
                'tooltip' => 'Students who have confirmed placement with SCL released',
            ],
        ];

        // =====================================================
        // 2. STUDENT DISTRIBUTION
        // =====================================================

        // Students by Group (with status breakdown)
        $studentsByGroup = $this->getStudentsByGroup($groupFilter);

        // Students by Programme
        $studentsByProgramme = $this->getStudentsByProgramme();

        // =====================================================
        // 3. PLACEMENT HEALTH
        // =====================================================

        // Placement Funnel Data
        $placementFunnel = $this->getPlacementFunnel();

        // At-Risk Students
        $atRiskStudents = $this->getAtRiskStudents();

        // =====================================================
        // 4. COMPANY & AGREEMENT INTELLIGENCE
        // =====================================================

        // Companies by Agreement Type
        $companiesByAgreement = $this->getCompaniesByAgreementType();

        // Agreement Expiry Watchlist
        $expiryWatchlist = $this->getAgreementExpiryWatchlist();

        // =====================================================
        // 5. WORKPLACE SAFETY & STUDENT WELLBEING
        // =====================================================

        // Workplace Issue Statistics
        $workplaceIssueStats = $this->getWorkplaceIssueStats();

        // Workplace Issues by Status
        $workplaceIssuesByStatus = $this->getWorkplaceIssuesByStatus();

        // Workplace Issues by Severity
        $workplaceIssuesBySeverity = $this->getWorkplaceIssuesBySeverity();

        // Critical Workplace Issues
        $criticalWorkplaceIssues = $this->getCriticalWorkplaceIssues();

        // Workplace Issue Metrics
        $workplaceIssueMetrics = $this->getWorkplaceIssueMetrics();

        // Companies with Most Issues
        $companiesWithIssues = $this->getCompaniesWithMostIssues();

        // Assessment Completion Overview
        $assessmentCompletion = $this->getAssessmentCompletion();

        // =====================================================
        // 6. RECENT ACTIVITY / ALERTS
        // =====================================================
        $systemAlerts = $this->getSystemAlerts();

        return view('dashboard-admin', compact(
            'kpiCards',
            'studentsByGroup',
            'studentsByProgramme',
            'placementFunnel',
            'atRiskStudents',
            'companiesByAgreement',
            'expiryWatchlist',
            'workplaceIssueStats',
            'workplaceIssuesByStatus',
            'workplaceIssuesBySeverity',
            'criticalWorkplaceIssues',
            'workplaceIssueMetrics',
            'companiesWithIssues',
            'assessmentCompletion',
            'systemAlerts',
            'groupFilter'
        ));
    }

    /**
     * Get placement statistics.
     */
    private function getPlacementStats(): array
    {
        $total = StudentPlacementTracking::count();
        $confirmed = StudentPlacementTracking::whereIn('status', ['CONFIRMED', 'SCL_RELEASED'])->count();
        $pending = $total - $confirmed;

        return [
            'total' => $total,
            'confirmed' => $confirmed,
            'pending' => $pending,
            'completion_rate' => $total > 0 ? round(($confirmed / $total) * 100, 1) : 0,
        ];
    }

    /**
     * Get students by group with status breakdown.
     */
    private function getStudentsByGroup(string $filter = 'all'): array
    {
        $query = WblGroup::withCount([
            'students',
            'students as placed_count' => function ($q) {
                $q->whereHas('placementTracking', function ($pt) {
                    $pt->whereIn('status', ['CONFIRMED', 'SCL_RELEASED']);
                });
            },
            'students as not_placed_count' => function ($q) {
                $q->whereDoesntHave('placementTracking')
                    ->orWhereHas('placementTracking', function ($pt) {
                        $pt->whereNotIn('status', ['CONFIRMED', 'SCL_RELEASED']);
                    });
            },
        ]);

        if ($filter === 'active') {
            $query->where('status', 'ACTIVE');
        } elseif ($filter === 'completed') {
            $query->where('status', 'COMPLETED');
        }

        $groups = $query->orderBy('status')->orderBy('name')->get();

        return [
            'labels' => $groups->pluck('name')->toArray(),
            'placed' => $groups->pluck('placed_count')->toArray(),
            'not_placed' => $groups->pluck('not_placed_count')->toArray(),
            'total' => $groups->pluck('students_count')->toArray(),
            'statuses' => $groups->pluck('status')->toArray(),
        ];
    }

    /**
     * Get students by programme.
     */
    private function getStudentsByProgramme(): array
    {
        $data = Student::select('programme', DB::raw('count(*) as total'))
            ->whereNotNull('programme')
            ->where('programme', '!=', '')
            ->groupBy('programme')
            ->orderByDesc('total')
            ->get();

        return [
            'labels' => $data->pluck('programme')->toArray(),
            'data' => $data->pluck('total')->toArray(),
        ];
    }

    /**
     * Get placement funnel data.
     */
    private function getPlacementFunnel(): array
    {
        // Get resume inspection stats
        $resumeApproved = StudentResumeInspection::where('status', 'PASSED')->count();

        // Get placement tracking stats
        $salReleased = StudentPlacementTracking::whereNotNull('sal_released_at')->count();
        $applied = StudentPlacementTracking::where('status', 'APPLIED')
            ->orWhere('applied_at', '!=', null)->count();
        $interviewed = StudentPlacementTracking::whereNotNull('interviewed_at')->count();
        $offerReceived = StudentPlacementTracking::whereNotNull('offer_received_at')->count();
        $accepted = StudentPlacementTracking::whereNotNull('accepted_at')->count();
        $confirmed = StudentPlacementTracking::whereIn('status', ['CONFIRMED', 'SCL_RELEASED'])->count();

        return [
            ['stage' => 'Resume Approved', 'count' => $resumeApproved, 'color' => '#E6F4EF'],
            ['stage' => 'SAL Released', 'count' => $salReleased, 'color' => '#B3E0D2'],
            ['stage' => 'Applied', 'count' => $applied, 'color' => '#80CCB5'],
            ['stage' => 'Interviewed', 'count' => $interviewed, 'color' => '#4DB898'],
            ['stage' => 'Offer Received', 'count' => $offerReceived, 'color' => '#1AA47B'],
            ['stage' => 'Accepted', 'count' => $accepted, 'color' => '#00905E'],
            ['stage' => 'Confirmed (SCL)', 'count' => $confirmed, 'color' => '#007C41'],
        ];
    }

    /**
     * Get at-risk students.
     */
    private function getAtRiskStudents(): array
    {
        // Students in APPLIED status for more than 14 days without interview
        $atRisk = StudentPlacementTracking::with(['student.group'])
            ->where('status', 'APPLIED')
            ->whereNull('interviewed_at')
            ->where(function ($q) {
                $q->where('applied_at', '<', now()->subDays(14))
                    ->orWhere('applied_status_set_at', '<', now()->subDays(14));
            })
            ->limit(10)
            ->get()
            ->map(function ($tracking) {
                $daysStuck = $tracking->applied_at
                    ? now()->diffInDays($tracking->applied_at)
                    : ($tracking->applied_status_set_at ? now()->diffInDays($tracking->applied_status_set_at) : 0);

                return [
                    'student_name' => $tracking->student->name ?? 'Unknown',
                    'matric_no' => $tracking->student->matric_no ?? '',
                    'group' => $tracking->student->group->name ?? 'N/A',
                    'programme' => $tracking->student->programme ?? 'N/A',
                    'status' => $tracking->status_display,
                    'days_stuck' => $daysStuck,
                    'risk_level' => $daysStuck > 30 ? 'high' : ($daysStuck > 14 ? 'medium' : 'low'),
                ];
            })->toArray();

        return $atRisk;
    }

    /**
     * Get companies by agreement type.
     */
    private function getCompaniesByAgreementType(): array
    {
        $mouCount = CompanyAgreement::ofType('MoU')->active()->count();
        $moaCount = CompanyAgreement::ofType('MoA')->active()->count();
        $loiCount = CompanyAgreement::ofType('LOI')->active()->count();
        $expiredCount = CompanyAgreement::expired()->count();
        $expiringCount = CompanyAgreement::expiringWithin(3)->count();

        return [
            'labels' => ['MoU', 'MoA', 'LOI', 'Expiring Soon', 'Expired'],
            'data' => [$mouCount, $moaCount, $loiCount, $expiringCount, $expiredCount],
            'colors' => ['#0084C5', '#7C3AED', '#F59E0B', '#EF4444', '#6B7280'],
        ];
    }

    /**
     * Get agreement expiry watchlist.
     */
    private function getAgreementExpiryWatchlist(): array
    {
        return CompanyAgreement::with('company')
            ->where('status', 'Active')
            ->whereNotNull('end_date')
            ->where('end_date', '<=', now()->addMonths(6))
            ->orderBy('end_date')
            ->limit(10)
            ->get()
            ->map(function ($agreement) {
                $daysRemaining = $agreement->days_until_expiry;

                return [
                    'company' => $agreement->company->company_name ?? 'Unknown',
                    'type' => $agreement->agreement_type,
                    'expiry_date' => $agreement->end_date->format('d M Y'),
                    'days_remaining' => $daysRemaining,
                    'urgency' => $daysRemaining < 90 ? 'critical' : ($daysRemaining < 180 ? 'warning' : 'normal'),
                ];
            })->toArray();
    }

    /**
     * Get workplace issue statistics.
     */
    private function getWorkplaceIssueStats(): array
    {
        $totalIssues = WorkplaceIssueReport::count();
        $openIssues = WorkplaceIssueReport::open()->count();
        $criticalIssues = WorkplaceIssueReport::open()->where('severity', 'critical')->count();
        $highIssues = WorkplaceIssueReport::open()->where('severity', 'high')->count();
        $resolvedThisMonth = WorkplaceIssueReport::where('status', 'resolved')
            ->where('resolved_at', '>=', now()->startOfMonth())
            ->count();

        return [
            'total' => $totalIssues,
            'open' => $openIssues,
            'critical' => $criticalIssues,
            'high' => $highIssues,
            'critical_high' => $criticalIssues + $highIssues,
            'resolved_this_month' => $resolvedThisMonth,
        ];
    }

    /**
     * Get workplace issues by status.
     */
    private function getWorkplaceIssuesByStatus(): array
    {
        $statusCounts = WorkplaceIssueReport::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();

        return [
            'labels' => ['New', 'Under Review', 'In Progress', 'Resolved', 'Closed'],
            'data' => [
                $statusCounts['new'] ?? 0,
                $statusCounts['under_review'] ?? 0,
                $statusCounts['in_progress'] ?? 0,
                $statusCounts['resolved'] ?? 0,
                $statusCounts['closed'] ?? 0,
            ],
            'colors' => ['#7C3AED', '#0084C5', '#F59E0B', '#10B981', '#6B7280'],
        ];
    }

    /**
     * Get workplace issues by severity.
     */
    private function getWorkplaceIssuesBySeverity(): array
    {
        $severityCounts = WorkplaceIssueReport::select('severity', DB::raw('count(*) as count'))
            ->groupBy('severity')
            ->get()
            ->pluck('count', 'severity')
            ->toArray();

        return [
            'labels' => ['Critical', 'High', 'Medium', 'Low'],
            'data' => [
                $severityCounts['critical'] ?? 0,
                $severityCounts['high'] ?? 0,
                $severityCounts['medium'] ?? 0,
                $severityCounts['low'] ?? 0,
            ],
            'colors' => ['#EF4444', '#F97316', '#F59E0B', '#3B82F6'],
        ];
    }

    /**
     * Get critical workplace issues requiring attention.
     */
    private function getCriticalWorkplaceIssues(): array
    {
        return WorkplaceIssueReport::with(['student', 'group', 'company', 'assignedTo'])
            ->open()
            ->whereIn('severity', ['critical', 'high'])
            ->orderByRaw("FIELD(severity, 'critical', 'high')")
            ->orderBy('submitted_at', 'asc')
            ->limit(10)
            ->get()
            ->map(function ($issue) {
                $daysOpen = $issue->submitted_at ? now()->diffInDays($issue->submitted_at) : 0;

                return [
                    'id' => $issue->id,
                    'student_name' => $issue->student->name ?? 'Unknown',
                    'matric_no' => $issue->student->matric_no ?? '',
                    'group' => $issue->group->name ?? 'N/A',
                    'company' => $issue->company->company_name ?? 'Not Specified',
                    'category' => $issue->category_display,
                    'severity' => $issue->severity,
                    'severity_display' => $issue->severity_display,
                    'status' => $issue->status,
                    'status_display' => $issue->status_display,
                    'days_open' => $daysOpen,
                    'assigned_to' => $issue->assignedTo->name ?? 'Unassigned',
                    'title' => $issue->title,
                ];
            })->toArray();
    }

    /**
     * Get workplace issue response metrics.
     */
    private function getWorkplaceIssueMetrics(): array
    {
        // Average response time (time to first review)
        $avgResponseTime = WorkplaceIssueReport::whereNotNull('reviewed_at')
            ->whereNotNull('submitted_at')
            ->get()
            ->map(function ($issue) {
                return $issue->submitted_at->diffInHours($issue->reviewed_at);
            })
            ->average();

        // Average resolution time
        $avgResolutionTime = WorkplaceIssueReport::whereNotNull('resolved_at')
            ->whereNotNull('submitted_at')
            ->get()
            ->map(function ($issue) {
                return $issue->submitted_at->diffInDays($issue->resolved_at);
            })
            ->average();

        // Student satisfaction (count of issues with feedback)
        $totalResolved = WorkplaceIssueReport::where('status', 'resolved')->count();
        $withFeedback = WorkplaceIssueReport::where('status', 'resolved')
            ->whereNotNull('student_feedback')
            ->count();

        return [
            'avg_response_hours' => $avgResponseTime ? round($avgResponseTime, 1) : 0,
            'avg_resolution_days' => $avgResolutionTime ? round($avgResolutionTime, 1) : 0,
            'feedback_rate' => $totalResolved > 0 ? round(($withFeedback / $totalResolved) * 100) : 0,
            'total_resolved' => $totalResolved,
            'with_feedback' => $withFeedback,
        ];
    }

    /**
     * Get companies ranked by number of workplace issues.
     */
    private function getCompaniesWithMostIssues(): array
    {
        return WorkplaceIssueReport::with('company')
            ->select('company_id', DB::raw('count(*) as total_issues'))
            ->selectRaw('SUM(CASE WHEN severity = "critical" THEN 1 ELSE 0 END) as critical_count')
            ->selectRaw('SUM(CASE WHEN severity = "high" THEN 1 ELSE 0 END) as high_count')
            ->selectRaw('SUM(CASE WHEN severity = "medium" THEN 1 ELSE 0 END) as medium_count')
            ->selectRaw('SUM(CASE WHEN severity = "low" THEN 1 ELSE 0 END) as low_count')
            ->selectRaw('SUM(CASE WHEN status IN ("new", "under_review", "in_progress") THEN 1 ELSE 0 END) as open_issues')
            ->whereNotNull('company_id')
            ->groupBy('company_id')
            ->orderByDesc('total_issues')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'company_id' => $item->company_id,
                    'company_name' => $item->company->company_name ?? 'Unknown',
                    'total_issues' => $item->total_issues,
                    'critical_count' => $item->critical_count,
                    'high_count' => $item->high_count,
                    'medium_count' => $item->medium_count,
                    'low_count' => $item->low_count,
                    'open_issues' => $item->open_issues,
                    'risk_level' => $this->calculateCompanyRiskLevel($item),
                ];
            })->toArray();
    }

    /**
     * Calculate company risk level based on issues.
     */
    private function calculateCompanyRiskLevel($companyIssues): string
    {
        // High risk: 3+ critical OR 5+ high severity issues
        if ($companyIssues->critical_count >= 3 || $companyIssues->high_count >= 5) {
            return 'high';
        }

        // Medium risk: 1-2 critical OR 2-4 high severity issues
        if ($companyIssues->critical_count > 0 || $companyIssues->high_count >= 2) {
            return 'medium';
        }

        // Low risk: only low/medium severity issues
        return 'low';
    }

    /**
     * Get assessment completion overview.
     */
    private function getAssessmentCompletion(): array
    {
        $courses = ['PPE', 'IP', 'OSH', 'FYP', 'LI'];
        $completion = [];

        foreach ($courses as $course) {
            $assessments = Assessment::forCourse($course)->active()->count();

            // Get total possible marks (assessments × students)
            $totalStudents = Student::inActiveGroups()->count();
            $totalPossible = $assessments * $totalStudents;

            // Get submitted marks
            $submittedMarks = StudentAssessmentMark::whereHas('assessment', function ($q) use ($course) {
                $q->where('course_code', $course)->where('is_active', true);
            })->whereNotNull('mark')->count();

            $completion[$course] = [
                'assessments' => $assessments,
                'submitted' => $submittedMarks,
                'total_possible' => $totalPossible,
                'percentage' => $totalPossible > 0 ? round(($submittedMarks / $totalPossible) * 100) : 0,
            ];
        }

        return $completion;
    }

    /**
     * Get system alerts.
     */
    private function getSystemAlerts(): array
    {
        $alerts = [];

        // Pending resume inspections (only count those with uploaded files)
        $pendingResumes = StudentResumeInspection::where('status', 'PENDING')
            ->whereNotNull('resume_file_path')
            ->count();
        if ($pendingResumes > 0) {
            $alerts[] = [
                'type' => 'warning',
                'message' => "{$pendingResumes} resume(s) pending inspection",
                'link' => route('coordinator.resume.index'),
            ];
        }

        // Students with SAL not released after resume approval
        $awaitingSal = StudentResumeInspection::where('status', 'PASSED')
            ->whereDoesntHave('student.placementTracking', function ($q) {
                $q->whereNotNull('sal_released_at');
            })
            ->count();
        if ($awaitingSal > 0) {
            $alerts[] = [
                'type' => 'info',
                'message' => "{$awaitingSal} student(s) awaiting SAL release",
                'link' => route('placement.index'),
            ];
        }

        // Agreements expiring within 90 days
        $expiringAgreements = CompanyAgreement::expiringWithin(3)->count();
        if ($expiringAgreements > 0) {
            $alerts[] = [
                'type' => 'danger',
                'message' => "{$expiringAgreements} agreement(s) expiring within 90 days",
                'link' => route('admin.agreements.index'),
            ];
        }

        // Critical workplace issues
        $criticalIssues = WorkplaceIssueReport::open()
            ->where('severity', 'critical')
            ->count();
        if ($criticalIssues > 0) {
            $alerts[] = [
                'type' => 'danger',
                'message' => "{$criticalIssues} critical workplace issue(s) requiring immediate attention",
                'link' => route('workplace-issues.index'),
            ];
        }

        // High severity workplace issues
        $highIssues = WorkplaceIssueReport::open()
            ->where('severity', 'high')
            ->count();
        if ($highIssues > 0) {
            $alerts[] = [
                'type' => 'warning',
                'message' => "{$highIssues} high-severity workplace issue(s) need review",
                'link' => route('workplace-issues.index'),
            ];
        }

        // Overdue company follow-up actions
        $overdueActions = CompanyNote::getOverdueActions();
        if ($overdueActions->count() > 0) {
            $firstOverdue = $overdueActions->first();
            $message = $overdueActions->count() === 1
                ? "1 overdue follow-up: {$firstOverdue->company->company_name}"
                : "{$overdueActions->count()} overdue company follow-up action(s) - view {$firstOverdue->company->company_name}";

            $alerts[] = [
                'type' => 'danger',
                'message' => $message,
                'link' => route('admin.companies.show', ['company' => $firstOverdue->company_id, 'tab' => 'notes']),
            ];
        }

        // Upcoming company follow-up actions (within 7 days, not overdue)
        $upcomingActions = CompanyNote::getPendingActions()->filter(function ($note) {
            return ! $note->isOverdue();
        });
        if ($upcomingActions->count() > 0) {
            $firstUpcoming = $upcomingActions->first();
            $message = $upcomingActions->count() === 1
                ? "1 follow-up due soon: {$firstUpcoming->company->company_name} ({$firstUpcoming->next_action_date->format('d M Y')})"
                : "{$upcomingActions->count()} company follow-up action(s) due soon - view {$firstUpcoming->company->company_name}";

            $alerts[] = [
                'type' => 'info',
                'message' => $message,
                'link' => route('admin.companies.show', ['company' => $firstUpcoming->company_id, 'tab' => 'notes']),
            ];
        }

        return $alerts;
    }

    /**
     * Staff dashboard (for non-admin staff).
     */
    private function staffDashboard(Request $request): View
    {
        $user = auth()->user();
        $groupFilter = $request->get('group_filter', 'all');

        // Basic stats
        $stats = [
            'students' => Student::count(),
            'groups' => WblGroup::count(),
            'active_groups' => WblGroup::where('status', 'ACTIVE')->count(),
            'completed_groups' => WblGroup::where('status', 'COMPLETED')->count(),
            'active_students' => Student::inActiveGroups()->count(),
            'completed_students' => Student::inCompletedGroups()->count(),
            'companies' => Company::count(),
        ];

        $changes = [
            'students' => ['value' => '+15%', 'type' => 'positive'],
            'groups' => ['value' => '+5%', 'type' => 'positive'],
            'companies' => ['value' => '+8%', 'type' => 'positive'],
        ];

        // Students by group
        $groupsQuery = WblGroup::withCount('students');
        if ($groupFilter === 'active') {
            $groupsQuery->where('status', 'ACTIVE');
        } elseif ($groupFilter === 'completed') {
            $groupsQuery->where('status', 'COMPLETED');
        }
        $studentsByGroup = $groupsQuery->orderBy('status')->orderBy('name')->get();

        $barChartData = [
            'labels' => $studentsByGroup->map(fn ($g) => $g->name.($g->isCompleted() ? ' (Completed)' : ''))->toArray(),
            'data' => $studentsByGroup->pluck('students_count')->toArray(),
        ];

        // Students by company
        $studentsByCompany = Company::withCount('students')
            ->orderBy('students_count', 'desc')
            ->limit(5)
            ->get();

        $donutChartData = [
            'labels' => $studentsByCompany->pluck('company_name')->toArray(),
            'data' => $studentsByCompany->pluck('students_count')->toArray(),
        ];

        // Students by Programme
        $studentsByProgram = Student::select('programme', DB::raw('count(*) as total'))
            ->whereNotNull('programme')
            ->groupBy('programme')
            ->orderBy('programme')
            ->get();

        $programChartData = [
            'labels' => $studentsByProgram->pluck('programme')->toArray(),
            'data' => $studentsByProgram->pluck('total')->toArray(),
        ];

        // Programme and Group chart
        $groups = WblGroup::orderBy('status')->orderBy('name')->get();
        $programmes = Student::distinct()->pluck('programme')->filter()->sort()->values();

        $studentsByProgramAndGroup = [];
        foreach ($programmes as $programme) {
            $programmeData = [];
            foreach ($groups as $group) {
                $count = Student::where('programme', $programme)
                    ->where('group_id', $group->id)
                    ->count();
                $programmeData[] = $count;
            }
            $studentsByProgramAndGroup[$programme] = $programmeData;
        }

        $colors = [
            ['bg' => '#003A6C', 'border' => '#003A6C'],
            ['bg' => '#0084C5', 'border' => '#0084C5'],
            ['bg' => '#00AEEF', 'border' => '#00AEEF'],
            ['bg' => '#002244', 'border' => '#002244'],
            ['bg' => '#E6ECF2', 'border' => '#003A6C'],
        ];

        $programGroupChartData = [
            'labels' => $groups->pluck('name')->toArray(),
            'datasets' => [],
        ];

        $colorIndex = 0;
        foreach ($studentsByProgramAndGroup as $programme => $data) {
            $color = $colors[$colorIndex % count($colors)];
            $programGroupChartData['datasets'][] = [
                'label' => $programme ?: 'Unknown Programme',
                'data' => $data,
                'backgroundColor' => $color['bg'],
                'borderColor' => $color['border'],
                'borderWidth' => 1,
                'borderRadius' => 4,
            ];
            $colorIndex++;
        }

        return view('dashboard', compact(
            'stats',
            'changes',
            'programGroupChartData',
            'barChartData',
            'donutChartData',
            'programChartData',
            'groupFilter'
        ));
    }

    /**
     * Display the student dashboard with assignments and progress.
     */
    private function studentDashboard(): View|RedirectResponse
    {
        $user = auth()->user();

        $student = $user->student;
        if (! $student) {
            $student = Student::where('user_id', $user->id)->first();
        }

        if (! $student) {
            $student = Student::where('name', $user->name)->first();
        }

        if (! $student) {
            return view('dashboard-student', [
                'student' => null,
                'assignedAt' => null,
                'assignedIc' => null,
                'assignedSupervisorLi' => null,
                'assignedPpeLecturer' => null,
                'assignedOshLecturer' => null,
                'assignedIpLecturer' => null,
                'courseScores' => [
                    'PPE' => ['score' => 0, 'at_score' => 0, 'ic_score' => 0, 'max' => 100, 'at_max' => 40, 'ic_max' => 60],
                    'FYP' => ['score' => 0, 'max' => 100],
                    'IP' => ['score' => 0, 'max' => 100],
                    'OSH' => ['score' => 0, 'max' => 100],
                    'LI' => ['score' => 0, 'max' => 100],
                ],
                'barChartData' => [
                    'labels' => ['PPE', 'FYP', 'IP', 'OSH', 'LI'],
                    'scores' => [0, 0, 0, 0, 0],
                ],
                'ppeDonutData' => [
                    'labels' => ['AT Contribution', 'IC Contribution'],
                    'data' => [0, 0],
                ],
                'resumeInspection' => null,
                'needsProfile' => true,
                'placementTracking' => null,
                'assessmentWindows' => collect(),
            ]);
        }

        if (! $student->user_id) {
            $student->user_id = $user->id;
            $student->save();
        }

        $student->load([
            'group',
            'company',
            'academicTutor',
            'industryCoach',
            'courseAssignments.lecturer',
        ]);

        $courseAssignments = $student->courseAssignments()->with('lecturer')->get()->keyBy('course_type');

        $assignedAt = $student->academicTutor ?? $courseAssignments->get('FYP')?->lecturer;
        $assignedIc = $student->industryCoach;
        $assignedSupervisorLi = $courseAssignments->get('Industrial Training')?->lecturer;
        $assignedPpeLecturer = $courseAssignments->get('PPE')?->lecturer;
        $assignedOshLecturer = $courseAssignments->get('OSH')?->lecturer;
        $assignedIpLecturer = $courseAssignments->get('IP')?->lecturer;

        // Calculate PPE Score
        $ppeAtMarks = PpeStudentAtMark::where('student_id', $student->id)
            ->with('assessment')
            ->get();

        $ppeAtTotal = 0;
        $ppeAtMax = 0;
        foreach ($ppeAtMarks as $mark) {
            if ($mark->mark !== null && $mark->assessment) {
                $contribution = ($mark->mark / $mark->assessment->max_mark) * $mark->assessment->weight;
                $ppeAtTotal += $contribution;
                $ppeAtMax += $mark->assessment->weight;
            }
        }

        $ppeIcMarks = PpeStudentIcMark::where('student_id', $student->id)->get();
        $ppeIcTotal = 0;
        $ppeIcMax = 60;
        foreach ($ppeIcMarks as $mark) {
            if ($mark->rubric_value !== null) {
                $contribution = ($mark->rubric_value / 5) * 15;
                $ppeIcTotal += $contribution;
            }
        }

        $ppeScore = $ppeAtTotal + $ppeIcTotal;
        $ppeAtScore = $ppeAtTotal;
        $ppeIcScore = $ppeIcTotal;

        $fypScore = 0;
        $ipScore = 0;
        $oshScore = 0;
        $liScore = 0;

        $courseScores = [
            'PPE' => [
                'score' => round($ppeScore, 2),
                'at_score' => round($ppeAtScore, 2),
                'ic_score' => round($ppeIcScore, 2),
                'max' => 100,
                'at_max' => 40,
                'ic_max' => 60,
            ],
            'FYP' => ['score' => $fypScore, 'max' => 100],
            'IP' => ['score' => $ipScore, 'max' => 100],
            'OSH' => ['score' => $oshScore, 'max' => 100],
            'LI' => ['score' => $liScore, 'max' => 100],
        ];

        $barChartData = [
            'labels' => ['PPE', 'FYP', 'IP', 'OSH', 'LI'],
            'scores' => [
                round($ppeScore, 2),
                $fypScore,
                $ipScore,
                $oshScore,
                $liScore,
            ],
        ];

        $ppeDonutData = [
            'labels' => ['AT Contribution', 'IC Contribution'],
            'data' => [
                round($ppeAtScore, 2),
                round($ppeIcScore, 2),
            ],
        ];

        $resumeInspection = $student->resumeInspection;
        $isInCompletedGroup = $student->isInCompletedGroup();

        // Fetch placement tracking
        $placementTracking = $student->placementTracking;

        // Fetch all assessment windows for timeline
        $assessmentWindows = collect();

        // PPE Windows
        $ppeWindows = PpeAssessmentWindow::all()->map(function ($window) {
            $window->module = 'PPE';
            $window->module_name = 'Professional Practice & Ethics';

            return $window;
        });
        $assessmentWindows = $assessmentWindows->merge($ppeWindows);

        // FYP Windows
        $fypWindows = FypAssessmentWindow::all()->map(function ($window) {
            $window->module = 'FYP';
            $window->module_name = 'Final Year Project';

            return $window;
        });
        $assessmentWindows = $assessmentWindows->merge($fypWindows);

        // IP Windows
        $ipWindows = IpAssessmentWindow::all()->map(function ($window) {
            $window->module = 'IP';
            $window->module_name = 'Internship Preparation';

            return $window;
        });
        $assessmentWindows = $assessmentWindows->merge($ipWindows);

        // OSH Windows
        $oshWindows = OshAssessmentWindow::all()->map(function ($window) {
            $window->module = 'OSH';
            $window->module_name = 'Occupational Safety & Health';

            return $window;
        });
        $assessmentWindows = $assessmentWindows->merge($oshWindows);

        // LI Windows
        $liWindows = LiAssessmentWindow::all()->map(function ($window) {
            $window->module = 'LI';
            $window->module_name = 'Industrial Training';

            return $window;
        });
        $assessmentWindows = $assessmentWindows->merge($liWindows);

        // Sort by status priority (open first, then upcoming, then closed)
        $assessmentWindows = $assessmentWindows->sortBy(function ($window) {
            return match ($window->status) {
                'open' => 0,
                'upcoming' => 1,
                'closed' => 2,
                'disabled' => 3,
                default => 4,
            };
        });

        return view('dashboard-student', compact(
            'student',
            'assignedAt',
            'assignedIc',
            'assignedSupervisorLi',
            'assignedPpeLecturer',
            'assignedOshLecturer',
            'assignedIpLecturer',
            'courseScores',
            'barChartData',
            'ppeDonutData',
            'resumeInspection',
            'isInCompletedGroup',
            'placementTracking',
            'assessmentWindows'
        ));
    }
}
