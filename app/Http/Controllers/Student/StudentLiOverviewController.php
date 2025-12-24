<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\LiLogbookEvaluation;
use App\Models\LI\LiAssessmentWindow;
use Illuminate\Http\Request;

class StudentLiOverviewController extends Controller
{
    public function index()
    {
        $student = auth()->user()->student;

        if (!$student) {
            return redirect()->route('students.profile.create')
                ->with('error', 'Please create your student profile first.');
        }

        // Fetch logbook evaluations
        $logbooks = LiLogbookEvaluation::where('student_id', $student->id)
            ->with('evaluator')
            ->orderBy('month')
            ->get();

        // Get assessment windows
        $assessmentWindows = LiAssessmentWindow::all();

        // Get supervisor
        $supervisor = $student->supervisorLi;

        // Calculate overall score
        $totalScore = $logbooks->sum('score');
        $averageScore = $logbooks->avg('score');
        $completionPercentage = ($logbooks->count() / 6) * 100;

        // Prepare line chart data for logbook trend
        $lineChartData = $this->prepareLogbookTrendData($logbooks);

        return view('student.li.overview', compact(
            'student',
            'logbooks',
            'assessmentWindows',
            'supervisor',
            'totalScore',
            'averageScore',
            'completionPercentage',
            'lineChartData'
        ));
    }

    /**
     * Prepare logbook trend data for line chart
     */
    private function prepareLogbookTrendData($logbooks)
    {
        $labels = [];
        $scores = [];

        for ($month = 1; $month <= 6; $month++) {
            $labels[] = 'Month ' . $month;
            $logbook = $logbooks->firstWhere('month', $month);
            $scores[] = $logbook ? $logbook->score : 0;
        }

        return [
            'labels' => $labels,
            'scores' => $scores,
        ];
    }
}
