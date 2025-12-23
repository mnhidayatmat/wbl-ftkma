<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\CloPloMapping;
use App\Models\Company;
use App\Models\CompanyAgreement;
use App\Models\PPE\PpeAssessmentSetting;
use App\Models\PPE\PpeStudentAtMark;
use App\Models\PPE\PpeStudentIcMark;
use App\Models\Student;
use App\Models\StudentAssessmentMark;
use App\Models\StudentCourseAssignment;
use App\Models\StudentPlacementTracking;
use App\Models\StudentResumeInspection;
use App\Models\WblGroup;
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
        // If user is a student, show student dashboard
        if (auth()->user()->isStudent()) {
            return $this->studentDashboard();
        }

        // Admin Dashboard
        if (auth()->user()->isAdmin()) {
            return $this->adminDashboard($request);
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
        // 5. ACADEMIC & QUALITY ASSURANCE
        // =====================================================

        // CLO-PLO Coverage Status
        $cloPloStatus = $this->getCloPloStatus();

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
            'cloPloStatus',
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
        $resumeApproved = StudentResumeInspection::where('status', 'RECOMMENDED')->count();

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
     * Get CLO-PLO coverage status per course.
     */
    private function getCloPloStatus(): array
    {
        $courses = ['PPE', 'IP', 'OSH', 'FYP', 'LI'];
        $status = [];

        foreach ($courses as $course) {
            // Get total CLOs defined for this course
            $totalClos = CloPloMapping::forCourse($course)->count();
            
            // Get CLOs with PLO mappings
            $mappedClos = CloPloMapping::forCourse($course)
                ->whereHas('ploRelationships')
                ->count();
            
            // Get CLOs allowed for assessment
            $assessmentClos = CloPloMapping::forCourse($course)
                ->where('allow_for_assessment', true)
                ->count();
            
            // Get CLOs actually used in assessments
            $usedInAssessments = Assessment::forCourse($course)
                ->active()
                ->distinct('clo_code')
                ->count('clo_code');

            $status[$course] = [
                'total' => $totalClos,
                'mapped' => $mappedClos,
                'assessment_allowed' => $assessmentClos,
                'used' => $usedInAssessments,
                'coverage' => $totalClos > 0 ? round(($mappedClos / $totalClos) * 100) : 0,
            ];
        }

        return $status;
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

        // Pending resume inspections
        $pendingResumes = StudentResumeInspection::where('status', 'PENDING')->count();
        if ($pendingResumes > 0) {
            $alerts[] = [
                'type' => 'warning',
                'message' => "{$pendingResumes} resume(s) pending inspection",
                'link' => route('coordinator.resume.index'),
            ];
        }

        // Students with SAL not released after resume approval
        $awaitingSal = StudentResumeInspection::where('status', 'RECOMMENDED')
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
            'labels' => $studentsByGroup->map(fn($g) => $g->name . ($g->isCompleted() ? ' (Completed)' : ''))->toArray(),
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
            'datasets' => []
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
                'borderRadius' => 4
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
        if (!$student) {
            $student = Student::where('user_id', $user->id)->first();
        }
        
        if (!$student) {
            $student = Student::where('name', $user->name)->first();
        }

        if (!$student) {
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
            ]);
        }
        
        if (!$student->user_id) {
            $student->user_id = $user->id;
            $student->save();
        }

        $student->load([
            'group',
            'company',
            'academicTutor',
            'industryCoach',
            'courseAssignments.lecturer'
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
            'isInCompletedGroup'
        ));
    }
}
