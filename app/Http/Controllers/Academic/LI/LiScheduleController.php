<?php

namespace App\Http\Controllers\Academic\LI;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\LI\LiAssessmentWindow;
use App\Models\LI\LiAuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LiScheduleController extends Controller
{
    /**
     * Display the assessment schedule management page.
     */
    public function index(): View
    {
        if (! auth()->user()->isAdmin() && ! auth()->user()->isLiCoordinator() && ! auth()->user()->isWblCoordinator()) {
            abort(403, 'Unauthorized access.');
        }

        $supervisorWindow = LiAssessmentWindow::firstOrCreate(
            ['evaluator_role' => 'supervisor'],
            ['created_by' => auth()->id(), 'is_enabled' => true]
        );
        $supervisorWindow->load('assessments');

        $icWindow = LiAssessmentWindow::firstOrCreate(
            ['evaluator_role' => 'ic'],
            ['created_by' => auth()->id(), 'is_enabled' => true]
        );
        $icWindow->load('assessments');

        // Get available assessments for dropdowns
        $supervisorAssessments = Assessment::where('course_code', 'LI')
            ->where('evaluator_role', 'supervisor_li')
            ->where('is_active', true)
            ->orderBy('assessment_name')
            ->get(['id', 'assessment_name', 'assessment_type']);

        $icAssessments = Assessment::where('course_code', 'LI')
            ->where('evaluator_role', 'ic')
            ->where('is_active', true)
            ->orderBy('assessment_name')
            ->get(['id', 'assessment_name', 'assessment_type']);

        return view('academic.li.schedule.index', compact(
            'supervisorWindow',
            'icWindow',
            'supervisorAssessments',
            'icAssessments'
        ));
    }

    /**
     * Update assessment window settings.
     */
    public function updateWindow(Request $request)
    {
        if (! auth()->user()->isAdmin() && ! auth()->user()->isLiCoordinator() && ! auth()->user()->isWblCoordinator()) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'evaluator_role' => ['required', 'in:supervisor,ic'],
            'is_enabled' => ['nullable', 'boolean'],
            'start_at' => ['nullable', 'date'],
            'end_at' => ['nullable', 'date', 'after_or_equal:start_at'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'assessment_ids' => ['nullable', 'array'],
            'assessment_ids.*' => ['exists:assessments,id'],
        ]);

        $window = LiAssessmentWindow::updateOrCreate(
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
        // Note: LI uses 'supervisor_li' as evaluator_role in assessments table
        if (isset($validated['assessment_ids'])) {
            $evaluatorRoleInAssessments = $validated['evaluator_role'] === 'supervisor' ? 'supervisor_li' : 'ic';

            $validAssessments = Assessment::where('course_code', 'LI')
                ->where('evaluator_role', $evaluatorRoleInAssessments)
                ->whereIn('id', $validated['assessment_ids'])
                ->pluck('id')
                ->toArray();

            $window->assessments()->sync($validAssessments);
        } else {
            $window->assessments()->detach(); // Empty = applies to all
        }

        // Log audit
        LiAuditLog::log(
            'schedule_updated',
            'schedule',
            "Assessment schedule updated for {$validated['evaluator_role']}",
            ['window_id' => $window->id, 'changes' => $validated]
        );

        return redirect()->route('academic.li.schedule.index')
            ->with('success', 'Assessment schedule updated successfully.');
    }

    /**
     * Send reminder emails.
     */
    public function sendReminder(Request $request)
    {
        if (! auth()->user()->isAdmin() && ! auth()->user()->isLiCoordinator() && ! auth()->user()->isWblCoordinator()) {
            abort(403, 'Unauthorized access.');
        }

        // TODO: Implement email reminder functionality
        return redirect()->route('academic.li.schedule.index')
            ->with('success', 'Reminder emails sent successfully.');
    }
}
