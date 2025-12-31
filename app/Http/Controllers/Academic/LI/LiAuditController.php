<?php

namespace App\Http\Controllers\Academic\LI;

use App\Http\Controllers\Controller;
use App\Models\LI\LiAuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LiAuditController extends Controller
{
    /**
     * Display audit log index page.
     */
    public function index(Request $request): View
    {
        if (! auth()->user()->isAdmin() && ! auth()->user()->isLiCoordinator() && ! auth()->user()->isWblCoordinator()) {
            abort(403, 'Unauthorized access.');
        }

        // Build query
        $query = LiAuditLog::with(['user', 'student', 'assessment']);

        // Apply filters
        if ($request->filled('action_type')) {
            $query->where('action_type', $request->action_type);
        }

        if ($request->filled('action')) {
            $query->where('action', 'like', "%{$request->action}%");
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Get action types for filter dropdown
        $actionTypes = LiAuditLog::distinct()->pluck('action_type')->sort()->values();

        // Get unique actions for filter
        $actions = LiAuditLog::distinct()->pluck('action')->sort()->values();

        // Get users for filter
        $users = \App\Models\User::whereIn('id', LiAuditLog::distinct()->pluck('user_id'))
            ->orderBy('name')
            ->get();

        // Paginate results
        $auditLogs = $query->orderBy('created_at', 'desc')->paginate(50);

        return view('academic.li.audit.index', [
            'auditLogs' => $auditLogs,
            'actionTypes' => $actionTypes,
            'actions' => $actions,
            'users' => $users,
            'filters' => $request->only(['action_type', 'action', 'user_id', 'student_id', 'date_from', 'date_to']),
        ]);
    }

    /**
     * Export audit logs.
     */
    public function export(Request $request)
    {
        if (! auth()->user()->isAdmin() && ! auth()->user()->isLiCoordinator() && ! auth()->user()->isWblCoordinator()) {
            abort(403, 'Unauthorized access.');
        }

        // Build query (same as index)
        $query = LiAuditLog::with(['user', 'student', 'assessment']);

        if ($request->filled('action_type')) {
            $query->where('action_type', $request->action_type);
        }

        if ($request->filled('action')) {
            $query->where('action', 'like', "%{$request->action}%");
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $auditLogs = $query->orderBy('created_at', 'desc')->get();

        // Generate CSV
        $filename = 'li_audit_log_'.now()->format('Y-m-d_His').'.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($auditLogs) {
            $file = fopen('php://output', 'w');

            // Header row
            fputcsv($file, [
                'Date & Time',
                'Action',
                'Action Type',
                'User',
                'Role',
                'Student',
                'Assessment',
                'Description',
                'IP Address',
            ]);

            // Data rows
            foreach ($auditLogs as $log) {
                $studentInfo = 'N/A';
                if ($log->student) {
                    $studentInfo = $log->student->name.' ('.$log->student->matric_no.')';
                }

                fputcsv($file, [
                    $log->created_at->format('Y-m-d H:i:s'),
                    $log->action,
                    $log->action_type,
                    $log->user->name ?? 'N/A',
                    $log->user_role,
                    $studentInfo,
                    $log->assessment->assessment_name ?? 'N/A',
                    $log->description,
                    $log->ip_address ?? 'N/A',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
