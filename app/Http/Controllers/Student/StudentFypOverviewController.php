<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\FYP\FypProjectProposal;
use App\Models\FYP\FypRubricTemplate;
use App\Models\FYP\FypRubricEvaluation;
use App\Models\FYP\FypRubricOverallFeedback;
use App\Models\FypLogbookEvaluation;
use App\Models\FYP\FypAssessmentWindow;
use Illuminate\Http\Request;

class StudentFypOverviewController extends Controller
{
    public function index()
    {
        $student = auth()->user()->student;

        if (!$student) {
            return redirect()->route('students.profile.create')
                ->with('error', 'Please create your student profile first.');
        }

        // Fetch project proposal
        $proposal = FypProjectProposal::where('student_id', $student->id)->first();

        // Fetch rubric templates with elements
        $templates = FypRubricTemplate::where('is_locked', true)
            ->with('elements')
            ->get();

        // Fetch student's rubric evaluations
        $evaluations = FypRubricEvaluation::where('student_id', $student->id)
            ->with(['template', 'element', 'evaluator', 'selectedDescriptor'])
            ->get();

        // Fetch overall feedback
        $overallFeedback = FypRubricOverallFeedback::where('student_id', $student->id)
            ->where('status', 'released')
            ->with(['template', 'evaluator'])
            ->get();

        // Fetch logbook evaluations
        $logbooks = FypLogbookEvaluation::where('student_id', $student->id)
            ->with('evaluator')
            ->orderBy('month')
            ->get();

        // Get assessment windows
        $assessmentWindows = FypAssessmentWindow::all();

        // Calculate total FYP score from rubric evaluations
        $totalScore = $this->calculateTotalScore($evaluations, $templates);

        // Prepare evaluation summary data
        $evaluationSummary = $this->prepareEvaluationSummary($templates, $evaluations, $overallFeedback);

        // Prepare radar chart data from rubric evaluations
        $radarChartData = $this->prepareRadarChartData($evaluations);

        // Calculate overall completion percentage
        $completionPercentage = $this->calculateCompletionPercentage($proposal, $evaluations, $logbooks, $templates);

        return view('student.fyp.overview', compact(
            'student',
            'proposal',
            'templates',
            'evaluations',
            'overallFeedback',
            'logbooks',
            'assessmentWindows',
            'totalScore',
            'evaluationSummary',
            'radarChartData',
            'completionPercentage'
        ));
    }

    /**
     * Calculate total FYP score from all rubric evaluations
     */
    private function calculateTotalScore($evaluations, $templates)
    {
        $totalWeightedScore = 0;
        $totalPossibleScore = 0;

        foreach ($templates as $template) {
            $templateEvaluations = $evaluations->where('rubric_template_id', $template->id);

            if ($templateEvaluations->isNotEmpty()) {
                $templateScore = $templateEvaluations->sum('weighted_score');
                $totalWeightedScore += $templateScore;
            }

            // Add to total possible score
            $totalPossibleScore += $template->component_marks ?? 100;
        }

        return $totalPossibleScore > 0 ? ($totalWeightedScore / $totalPossibleScore) * 100 : 0;
    }

    /**
     * Prepare evaluation summary for each template
     */
    private function prepareEvaluationSummary($templates, $evaluations, $overallFeedback)
    {
        $summary = [];

        foreach ($templates as $template) {
            $templateEvaluations = $evaluations->where('rubric_template_id', $template->id);
            $templateFeedback = $overallFeedback->firstWhere('rubric_template_id', $template->id);

            $totalElements = $template->elements->count();
            $evaluatedElements = $templateEvaluations->count();

            $summary[] = [
                'template' => $template,
                'evaluations' => $templateEvaluations,
                'feedback' => $templateFeedback,
                'total_elements' => $totalElements,
                'evaluated_elements' => $evaluatedElements,
                'completion_percentage' => $totalElements > 0 ? ($evaluatedElements / $totalElements) * 100 : 0,
                'total_score' => $templateEvaluations->sum('weighted_score'),
                'status' => $templateFeedback ? $templateFeedback->status : 'pending',
            ];
        }

        return collect($summary);
    }

    /**
     * Prepare radar chart data from rubric evaluations
     */
    private function prepareRadarChartData($evaluations)
    {
        // Group evaluations by element name and calculate average performance
        $elementPerformance = $evaluations->groupBy('element.name')->map(function ($group) {
            return [
                'name' => $group->first()->element->name ?? 'Unknown',
                'average_level' => $group->avg('selected_level'),
            ];
        })->take(8)->values(); // Limit to 8 elements for readability

        return [
            'labels' => $elementPerformance->pluck('name')->toArray(),
            'data' => $elementPerformance->pluck('average_level')->toArray(),
        ];
    }

    /**
     * Calculate overall completion percentage
     */
    private function calculateCompletionPercentage($proposal, $evaluations, $logbooks, $templates)
    {
        $components = [];

        // Proposal (20%)
        $components['proposal'] = $proposal && $proposal->status === 'approved' ? 20 : 0;

        // Rubric Evaluations (60%)
        $totalElements = $templates->sum(function ($template) {
            return $template->elements->count();
        });
        $evaluatedElements = $evaluations->count();
        $components['evaluations'] = $totalElements > 0 ? ($evaluatedElements / $totalElements) * 60 : 0;

        // Logbook (20%)
        $components['logbook'] = ($logbooks->count() / 6) * 20;

        return array_sum($components);
    }
}
