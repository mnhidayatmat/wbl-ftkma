<?php

namespace App\Http\Controllers\Academic\PPE;

use App\Http\Controllers\Controller;
use App\Models\PPE\PpeAssessmentSetting;
use App\Models\PPE\PpeStudentAtMark;
use App\Models\PPE\PpeStudentIcMark;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PpeFinalScoreController extends Controller
{
    /**
     * Display the list of students filtered by group.
     */
    public function index(Request $request): View|RedirectResponse
    {
        $groupId = $request->query('group');

        if (! $groupId) {
            return redirect()->route('academic.ppe.groups.index');
        }

        $group = \App\Models\WblGroup::findOrFail($groupId);
        $query = Student::where('group_id', $groupId);

        // Filter based on role
        $user = auth()->user();
        if ($user->isIndustry() && ! $user->isAdmin()) {
            // IC can only see assigned students
            $query->where('ic_id', $user->id);
        } elseif ($user->isStudent()) {
            // Student can only see their own
            $query->where('user_id', $user->id);
        }
        // Admin and Lecturer can see all students in group

        $students = $query->with(['group', 'company'])
            ->orderBy('name')
            ->get();

        return view('academic.ppe.final.index', compact('students', 'group'));
    }

    /**
     * Show the final score breakdown for a student.
     */
    public function show(Student $student): View
    {
        // Check authorization - all roles can view final scores
        $this->authorize('view', $student);

        // Lecturer Marks (40%)
        $atSettings = PpeAssessmentSetting::where('clo', 'CLO1')->orderBy('id')->get();
        $atMarks = PpeStudentAtMark::where('student_id', $student->id)
            ->get()
            ->keyBy('assignment_id');

        $atBreakdown = [];
        $atTotalContribution = 0;
        foreach ($atSettings as $setting) {
            $mark = $atMarks->get($setting->id);
            $rawMark = $mark?->mark ?? 0;
            $contribution = ($rawMark / $setting->max_mark) * $setting->weight;
            $atTotalContribution += $contribution;

            $atBreakdown[] = [
                'name' => $setting->name,
                'raw_mark' => $rawMark,
                'max_mark' => $setting->max_mark,
                'weight' => $setting->weight,
                'contribution' => $contribution,
            ];
        }

        // IC Marks (60%) - Based on rubric (1-5 scale)
        // Q1 (CLO2 - 15%), Q2 (CLO2 - 15%), Q3 (CLO3 - 15%), Q4 (CLO4 - 15%)
        $icMarks = PpeStudentIcMark::where('student_id', $student->id)
            ->get()
            ->keyBy(function ($item) {
                return $item->question_no.'_'.$item->clo;
            });

        $q1Mark = $icMarks->get('1_CLO2');
        $q2Mark = $icMarks->get('2_CLO2');
        $q3Mark = $icMarks->get('3_CLO3');
        $q4Mark = $icMarks->get('4_CLO4');

        $q1Rubric = $q1Mark?->rubric_value ?? 0;
        $q2Rubric = $q2Mark?->rubric_value ?? 0;
        $q3Rubric = $q3Mark?->rubric_value ?? 0;
        $q4Rubric = $q4Mark?->rubric_value ?? 0;

        // Calculate contributions: (rubric/5) * weight
        $q1Contribution = ($q1Rubric / 5) * 15; // Q1 - CLO2 (15%)
        $q2Contribution = ($q2Rubric / 5) * 15; // Q2 - CLO2 (15%)
        $q3Contribution = ($q3Rubric / 5) * 15; // Q3 - CLO3 (15%)
        $q4Contribution = ($q4Rubric / 5) * 15; // Q4 - CLO4 (15%)

        $icTotalContribution = $q1Contribution + $q2Contribution + $q3Contribution + $q4Contribution;

        $icBreakdown = [
            [
                'question' => 'Q1',
                'clo' => 'CLO2',
                'name' => 'Q1 - CLO2 (Engineering Ethics & Public Responsibility)',
                'rubric_value' => $q1Rubric,
                'max_rubric' => 5,
                'weight' => 15,
                'contribution' => $q1Contribution,
            ],
            [
                'question' => 'Q2',
                'clo' => 'CLO2',
                'name' => 'Q2 - CLO2 (Engineer and Law)',
                'rubric_value' => $q2Rubric,
                'max_rubric' => 5,
                'weight' => 15,
                'contribution' => $q2Contribution,
            ],
            [
                'question' => 'Q3',
                'clo' => 'CLO3',
                'name' => 'Q3 - CLO3 (Engineer and Research)',
                'rubric_value' => $q3Rubric,
                'max_rubric' => 5,
                'weight' => 15,
                'contribution' => $q3Contribution,
            ],
            [
                'question' => 'Q4',
                'clo' => 'CLO4',
                'name' => 'Q4 - CLO4 (Leadership & Teamwork)',
                'rubric_value' => $q4Rubric,
                'max_rubric' => 5,
                'weight' => 15,
                'contribution' => $q4Contribution,
            ],
        ];

        // Final Score
        $finalScore = $atTotalContribution + $icTotalContribution;

        return view('academic.ppe.final.show', compact(
            'student',
            'atBreakdown',
            'atTotalContribution',
            'icBreakdown',
            'icTotalContribution',
            'finalScore'
        ));
    }
}
