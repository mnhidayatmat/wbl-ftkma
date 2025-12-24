<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\PPE\PpeStudentAtMark;
use App\Models\PPE\PpeStudentIcMark;
use App\Models\PpeLogbookEvaluation;
use App\Models\PPE\PpeAssessmentWindow;
use Illuminate\Http\Request;

class StudentPpeOverviewController extends Controller
{
    public function index()
    {
        $student = auth()->user()->student;

        if (!$student) {
            return redirect()->route('students.profile.create')
                ->with('error', 'Please create your student profile first.');
        }

        // Fetch AT marks with assessment details
        $atMarks = PpeStudentAtMark::where('student_id', $student->id)
            ->with('assessment')
            ->get();

        // Fetch IC marks
        $icMarks = PpeStudentIcMark::where('student_id', $student->id)->get();

        // Fetch logbook evaluations
        $logbooks = PpeLogbookEvaluation::where('student_id', $student->id)
            ->with('evaluator')
            ->orderBy('month')
            ->get();

        // Get assessment windows
        $assessmentWindows = PpeAssessmentWindow::all();

        // Calculate AT score (based on DashboardController pattern)
        $atScore = $this->calculateAtScore($atMarks);
        $atMaxScore = 40;

        // Calculate IC score (rubric_value/5 * 15 per CLO)
        $icScore = $this->calculateIcScore($icMarks);
        $icMaxScore = 60;

        // Calculate total score
        $totalScore = $atScore + $icScore;

        // Prepare CLO chart data
        $cloChartData = $this->prepareCloChartData($icMarks);

        // Prepare AT/IC pie chart data
        $pieChartData = [
            'labels' => ['AT Contribution (40%)', 'IC Contribution (60%)'],
            'data' => [round($atScore, 2), round($icScore, 2)],
        ];

        return view('student.ppe.overview', compact(
            'student',
            'atMarks',
            'icMarks',
            'logbooks',
            'assessmentWindows',
            'atScore',
            'atMaxScore',
            'icScore',
            'icMaxScore',
            'totalScore',
            'cloChartData',
            'pieChartData'
        ));
    }

    /**
     * Calculate AT score from assessment marks
     */
    private function calculateAtScore($atMarks)
    {
        $atTotal = 0;

        foreach ($atMarks as $mark) {
            if ($mark->mark !== null && $mark->assessment) {
                // Calculate weighted contribution
                $contribution = ($mark->mark / $mark->assessment->max_mark) * $mark->assessment->weight;
                $atTotal += $contribution;
            }
        }

        return $atTotal;
    }

    /**
     * Calculate IC score from rubric marks
     * Each CLO is worth 15 marks, rubric value is 1-5
     */
    private function calculateIcScore($icMarks)
    {
        $icTotal = 0;

        foreach ($icMarks as $mark) {
            if ($mark->rubric_value !== null) {
                // Convert rubric value (1-5) to marks out of 15
                $contribution = ($mark->rubric_value / 5) * 15;
                $icTotal += $contribution;
            }
        }

        return $icTotal;
    }

    /**
     * Prepare CLO breakdown data for bar chart
     */
    private function prepareCloChartData($icMarks)
    {
        $cloData = [
            'CLO1' => 0,
            'CLO2' => 0,
            'CLO3' => 0,
            'CLO4' => 0,
        ];

        // Group marks by CLO
        foreach ($icMarks as $mark) {
            if ($mark->clo && $mark->rubric_value !== null) {
                $cloKey = strtoupper($mark->clo);
                if (isset($cloData[$cloKey])) {
                    $cloData[$cloKey] += ($mark->rubric_value / 5) * 15;
                }
            }
        }

        return [
            'labels' => array_keys($cloData),
            'scores' => array_values($cloData),
        ];
    }
}
