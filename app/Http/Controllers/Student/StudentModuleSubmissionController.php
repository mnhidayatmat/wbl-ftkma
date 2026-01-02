<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\StudentSubmission;
use Illuminate\View\View;

class StudentModuleSubmissionController extends Controller
{
    /**
     * Display FYP Assessment Submissions.
     */
    public function fyp(): View
    {
        return $this->showSubmissions('FYP', 'FYP Assessment Submissions', 'Final Year Project');
    }

    /**
     * Display IP Assessment Submissions.
     */
    public function ip(): View
    {
        return $this->showSubmissions('IP', 'IP Assessment Submissions', 'Internship Preparation');
    }

    /**
     * Display OSH Assessment Submissions.
     */
    public function osh(): View
    {
        return $this->showSubmissions('OSH', 'OSH Assessment Submissions', 'Occupational Safety & Health');
    }

    /**
     * Display PPE Assessment Submissions.
     */
    public function ppe(): View
    {
        return $this->showSubmissions('PPE', 'PPE Assessment Submissions', 'Professional Practice Evaluation');
    }

    /**
     * Display LI Assessment Submissions.
     */
    public function li(): View
    {
        return $this->showSubmissions('LI', 'LI Assessment Submissions', 'Industrial Training');
    }

    /**
     * Show submissions for a specific module.
     */
    private function showSubmissions(string $module, string $title, string $moduleFullName): View
    {
        $student = auth()->user()->student;

        if (!$student) {
            abort(403, 'Student profile not found.');
        }

        // Get assessments that require submission for this module
        $assessments = Assessment::where('wbl_module', $module)
            ->where('requires_submission', true)
            ->where('is_active', true)
            ->orderBy('submission_deadline', 'asc')
            ->get();

        // Get submission status for each assessment
        $submissionAssessments = $assessments->map(function ($assessment) use ($student) {
            $submissions = StudentSubmission::where('student_id', $student->id)
                ->where('assessment_id', $assessment->id)
                ->orderBy('attempt_number', 'desc')
                ->get();

            $latestSubmission = $submissions->first();
            $attemptCount = $submissions->count();

            // Determine status
            $now = now();
            $isLate = false;
            $canSubmit = false;
            $status = ['label' => 'Not Submitted', 'color' => 'gray'];

            if ($latestSubmission) {
                if ($latestSubmission->status === 'evaluated') {
                    $status = ['label' => 'Evaluated', 'color' => 'green'];
                } else {
                    $status = ['label' => 'Submitted', 'color' => 'blue'];
                }
            }

            // Check if submission is still possible
            if ($attemptCount < $assessment->max_attempts) {
                if ($assessment->submission_deadline) {
                    if ($now->lt($assessment->submission_deadline)) {
                        $canSubmit = true;
                    } elseif ($assessment->allow_late_submission) {
                        $maxLateDate = $assessment->submission_deadline->addDays($assessment->max_late_days ?? 7);
                        if ($now->lt($maxLateDate)) {
                            $canSubmit = true;
                            $isLate = true;
                            $status = ['label' => 'Late Submission Window', 'color' => 'orange'];
                        } else {
                            $status = ['label' => 'Deadline Passed', 'color' => 'red'];
                        }
                    } else {
                        $status = ['label' => 'Deadline Passed', 'color' => 'red'];
                    }
                } else {
                    $canSubmit = true;
                }
            } else {
                if (!$latestSubmission) {
                    $status = ['label' => 'Max Attempts Reached', 'color' => 'red'];
                }
            }

            // Override status if not submitted but can still submit
            if (!$latestSubmission && $canSubmit) {
                $status = $isLate
                    ? ['label' => 'Late - Not Submitted', 'color' => 'orange']
                    : ['label' => 'Pending Submission', 'color' => 'yellow'];
            }

            return [
                'assessment' => $assessment,
                'latest_submission' => $latestSubmission,
                'submissions' => $submissions,
                'attempt_count' => $attemptCount,
                'can_submit' => $canSubmit,
                'is_late' => $isLate,
                'status' => $status,
            ];
        });

        return view('student.module-submissions.index', [
            'module' => $module,
            'title' => $title,
            'moduleFullName' => $moduleFullName,
            'submissionAssessments' => $submissionAssessments,
            'student' => $student,
        ]);
    }
}
