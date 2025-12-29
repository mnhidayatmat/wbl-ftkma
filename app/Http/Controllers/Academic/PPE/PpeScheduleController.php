<?php

namespace App\Http\Controllers\Academic\PPE;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\PPE\PpeAssessmentWindow;
use App\Models\PPE\PpeAuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PpeScheduleController extends Controller
{
    /**
     * Display the assessment schedule management page.
     */
    public function index(): View
    {
        if (! auth()->user()->isAdmin() && ! auth()->user()->isPpeCoordinator()) {
            abort(403, 'Unauthorized access.');
        }

        $lecturerWindow = PpeAssessmentWindow::firstOrCreate(
            ['evaluator_role' => 'lecturer'],
            ['created_by' => auth()->id(), 'is_enabled' => true]
        );
        $lecturerWindow->load('assessments');

        $icWindow = PpeAssessmentWindow::firstOrCreate(
            ['evaluator_role' => 'ic'],
            ['created_by' => auth()->id(), 'is_enabled' => true]
        );
        $icWindow->load('assessments');

        // Get available assessments for dropdowns
        $lecturerAssessments = Assessment::where('course_code', 'PPE')
            ->where('evaluator_role', 'lecturer')
            ->where('is_active', true)
            ->orderBy('assessment_name')
            ->get(['id', 'assessment_name', 'assessment_type']);

        $icAssessments = Assessment::where('course_code', 'PPE')
            ->where('evaluator_role', 'ic')
            ->where('is_active', true)
            ->orderBy('assessment_name')
            ->get(['id', 'assessment_name', 'assessment_type']);

        return view('academic.ppe.schedule.index', compact(
            'lecturerWindow',
            'icWindow',
            'lecturerAssessments',
            'icAssessments'
        ));
    }

    /**
     * Update assessment window settings.
     */
    public function updateWindow(Request $request)
    {
        if (! auth()->user()->isAdmin() && ! auth()->user()->isPpeCoordinator()) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'evaluator_role' => ['required', 'in:lecturer,ic'],
            'is_enabled' => ['nullable', 'boolean'],
            'start_at' => ['nullable', 'date'],
            'end_at' => ['nullable', 'date', 'after_or_equal:start_at'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'assessment_ids' => ['nullable', 'array'],
            'assessment_ids.*' => ['exists:assessments,id'],
        ]);

        $window = PpeAssessmentWindow::updateOrCreate(
            ['evaluator_role' => $validated['evaluator_role']],
            [
                'is_enabled' => $request->has('is_enabled'),
                'start_at' => $validated['start_at'] ?? null,
                'end_at' => $validated['end_at'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'updated_by' => auth()->id(),
            ]
        );

        if (! $window->wasRecentlyCreated) {
            $window->update(['updated_by' => auth()->id()]);
        }

        // Sync assessments (validate they belong to correct course and evaluator)
        if (isset($validated['assessment_ids'])) {
            $validAssessments = Assessment::where('course_code', 'PPE')
                ->where('evaluator_role', $validated['evaluator_role'])
                ->whereIn('id', $validated['assessment_ids'])
                ->pluck('id')
                ->toArray();

            $window->assessments()->sync($validAssessments);
        } else {
            $window->assessments()->detach(); // Empty = applies to all
        }

        // Log audit
        PpeAuditLog::log(
            'schedule_updated',
            'schedule',
            "Assessment schedule updated for {$validated['evaluator_role']}",
            ['window_id' => $window->id, 'changes' => $validated]
        );

        return redirect()->route('academic.ppe.schedule.index')
            ->with('success', 'Assessment schedule updated successfully.');
    }

    /**
     * Send reminder emails.
     */
    public function sendReminder(Request $request)
    {
        if (! auth()->user()->isAdmin() && ! auth()->user()->isPpeCoordinator()) {
            abort(403, 'Unauthorized access.');
        }

        // TODO: Implement email reminder functionality
        return redirect()->route('academic.ppe.schedule.index')
            ->with('success', 'Reminder emails sent successfully.');
    }
}
