<?php

namespace App\Traits;

use App\Models\FYP\FypAssessmentWindow;
use App\Models\IP\IpAssessmentWindow;
use App\Models\LI\LiAssessmentWindow;
use App\Models\OSH\OshAssessmentWindow;
use App\Models\PPE\PpeAssessmentWindow;

trait ChecksAssessmentWindow
{
    /**
     * Check if assessment window is open for the given evaluator role and course.
     *
     * @param  string  $evaluatorRole  The evaluator role (lecturer, at, ic, supervisor)
     * @param  string  $course  The course code (PPE, FYP, IP, OSH, LI)
     * @param  int|null  $assessmentId  Optional assessment ID to check specific assessment
     * @return bool
     */
    protected function checkAssessmentWindow(
        string $evaluatorRole,
        string $course = 'PPE',
        ?int $assessmentId = null
    ): bool {
        $window = $this->getAssessmentWindow($evaluatorRole, $course);

        if (! $window || ! $window->is_enabled) {
            return false;
        }

        return $window->isOpenFor($assessmentId);
    }

    /**
     * Abort if assessment window is not open.
     *
     * @param  string  $evaluatorRole  The evaluator role
     * @param  string  $course  The course code
     * @param  int|null  $assessmentId  Optional assessment ID
     */
    protected function requireOpenWindow(
        string $evaluatorRole,
        string $course = 'PPE',
        ?int $assessmentId = null
    ): void {
        if (! $this->checkAssessmentWindow($evaluatorRole, $course, $assessmentId)) {
            $window = $this->getAssessmentWindow($evaluatorRole, $course);
            $status = $window ? $window->status_label : 'Closed';

            // Enhanced error message for assessment-specific restrictions
            if ($assessmentId && $window && $window->is_enabled && $window->isOpen()) {
                abort(403, 'This assessment is not available in the current evaluation window. Please contact an administrator.');
            }

            abort(403, "Assessment window is currently {$status}. Please contact an administrator.");
        }
    }

    /**
     * Get the assessment window for a given evaluator role and course.
     *
     * @param  string  $evaluatorRole
     * @param  string  $course
     * @return PpeAssessmentWindow|FypAssessmentWindow|IpAssessmentWindow|OshAssessmentWindow|LiAssessmentWindow|null
     */
    protected function getAssessmentWindow(string $evaluatorRole, string $course)
    {
        return match ($course) {
            'FYP' => FypAssessmentWindow::where('evaluator_role', $evaluatorRole)->first(),
            'IP' => IpAssessmentWindow::where('evaluator_role', $evaluatorRole)->first(),
            'OSH' => OshAssessmentWindow::where('evaluator_role', $evaluatorRole)->first(),
            'LI' => LiAssessmentWindow::where('evaluator_role', $evaluatorRole)->first(),
            default => PpeAssessmentWindow::where('evaluator_role', $evaluatorRole)->first(),
        };
    }
}
