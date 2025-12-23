<?php

namespace App\Http\Controllers\Academic\IP;

use App\Http\Controllers\Controller;
use App\Models\IP\IpAssessmentWindow;
use App\Models\IP\IpAuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IpScheduleController extends Controller
{
    /**
     * Display the assessment schedule management page.
     */
    public function index(): View
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $lecturerWindow = IpAssessmentWindow::firstOrCreate(
            ['evaluator_role' => 'lecturer'],
            ['created_by' => auth()->id(), 'is_enabled' => true]
        );

        $icWindow = IpAssessmentWindow::firstOrCreate(
            ['evaluator_role' => 'ic'],
            ['created_by' => auth()->id(), 'is_enabled' => true]
        );

        return view('academic.ip.schedule.index', compact('lecturerWindow', 'icWindow'));
    }

    /**
     * Update assessment window settings.
     */
    public function updateWindow(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'evaluator_role' => ['required', 'in:lecturer,ic'],
            'is_enabled' => ['nullable', 'boolean'],
            'start_at' => ['nullable', 'date'],
            'end_at' => ['nullable', 'date', 'after_or_equal:start_at'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $window = IpAssessmentWindow::updateOrCreate(
            ['evaluator_role' => $validated['evaluator_role']],
            [
                'is_enabled' => $request->has('is_enabled'),
                'start_at' => $validated['start_at'] ?? null,
                'end_at' => $validated['end_at'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'updated_by' => auth()->id(),
            ]
        );

        if (!$window->wasRecentlyCreated) {
            $window->update(['updated_by' => auth()->id()]);
        }

        // Log audit
        IpAuditLog::log(
            'schedule_updated',
            'schedule',
            "Assessment schedule updated for {$validated['evaluator_role']}",
            ['window_id' => $window->id, 'changes' => $validated]
        );

        return redirect()->route('academic.ip.schedule.index')
            ->with('success', 'Assessment schedule updated successfully.');
    }

    /**
     * Send reminder emails.
     */
    public function sendReminder(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        // TODO: Implement email reminder functionality
        return redirect()->route('academic.ip.schedule.index')
            ->with('success', 'Reminder emails sent successfully.');
    }
}
