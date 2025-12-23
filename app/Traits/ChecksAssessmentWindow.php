<?php

namespace App\Traits;

use App\Models\IP\IpAssessmentWindow;
use App\Models\OSH\OshAssessmentWindow;
use App\Models\PPE\PpeAssessmentWindow;

trait ChecksAssessmentWindow
{
    /**
     * Check if assessment window is open for the given evaluator role and course.
     */
    protected function checkAssessmentWindow(string $evaluatorRole, string $course = 'PPE'): bool
    {
        if ($course === 'OSH') {
            $window = OshAssessmentWindow::where('evaluator_role', $evaluatorRole)->first();
        } elseif ($course === 'IP') {
            $window = IpAssessmentWindow::where('evaluator_role', $evaluatorRole)->first();
        } else {
            $window = PpeAssessmentWindow::where('evaluator_role', $evaluatorRole)->first();
        }

        if (! $window || ! $window->is_enabled) {
            return false;
        }

        return $window->isOpen();
    }

    /**
     * Abort if assessment window is not open.
     */
    protected function requireOpenWindow(string $evaluatorRole, string $course = 'PPE'): void
    {
        if (! $this->checkAssessmentWindow($evaluatorRole, $course)) {
            if ($course === 'OSH') {
                $window = OshAssessmentWindow::where('evaluator_role', $evaluatorRole)->first();
            } elseif ($course === 'IP') {
                $window = IpAssessmentWindow::where('evaluator_role', $evaluatorRole)->first();
            } else {
                $window = PpeAssessmentWindow::where('evaluator_role', $evaluatorRole)->first();
            }

            $status = $window ? $window->status_label : 'Closed';

            abort(403, "Assessment window is currently {$status}. Please contact an administrator.");
        }
    }
}
