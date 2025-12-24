<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\IpLogbookEvaluation;
use App\Models\IP\IpAssessmentWindow;
use Illuminate\Http\Request;

class StudentIpOverviewController extends Controller
{
    public function index()
    {
        $student = auth()->user()->student;

        if (!$student) {
            return redirect()->route('students.profile.create')
                ->with('error', 'Please create your student profile first.');
        }

        // Fetch logbook evaluations
        $logbooks = IpLogbookEvaluation::where('student_id', $student->id)
            ->with('evaluator')
            ->orderBy('month')
            ->get();

        // Get assessment windows
        $assessmentWindows = IpAssessmentWindow::all();

        // Calculate scores
        $totalScore = $logbooks->sum('score');
        $averageScore = $logbooks->avg('score');
        $completionPercentage = ($logbooks->count() / 6) * 100;

        // Prepare chart data
        $barChartData = $this->prepareMonthlyBarChartData($logbooks);

        return view('student.ip.overview', compact(
            'student',
            'logbooks',
            'assessmentWindows',
            'totalScore',
            'averageScore',
            'completionPercentage',
            'barChartData'
        ));
    }

    /**
     * Prepare monthly bar chart data
     */
    private function prepareMonthlyBarChartData($logbooks)
    {
        $labels = [];
        $scores = [];

        for ($month = 1; $month <= 6; $month++) {
            $labels[] = 'M' . $month;
            $logbook = $logbooks->firstWhere('month', $month);
            $scores[] = $logbook ? $logbook->score : 0;
        }

        return [
            'labels' => $labels,
            'scores' => $scores,
        ];
    }
}
