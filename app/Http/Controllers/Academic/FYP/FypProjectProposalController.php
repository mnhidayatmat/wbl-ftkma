<?php

namespace App\Http\Controllers\Academic\FYP;

use App\Http\Controllers\Controller;
use App\Models\FYP\FypProjectProposal;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FypProjectProposalController extends Controller
{
    /**
     * Display a listing of all project proposals (Admin view).
     */
    public function index(Request $request): View
    {
        if (! auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $query = FypProjectProposal::with(['student.user', 'student.company', 'student.academicTutor', 'student.industryCoach']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('matric_no', 'like', "%{$search}%");
            })->orWhere('project_title', 'like', "%{$search}%");
        }

        $proposals = $query->orderBy('updated_at', 'desc')->paginate(20);

        // Get statistics
        $stats = [
            'total' => FypProjectProposal::count(),
            'draft' => FypProjectProposal::where('status', 'draft')->count(),
            'submitted' => FypProjectProposal::where('status', 'submitted')->count(),
            'approved' => FypProjectProposal::where('status', 'approved')->count(),
            'rejected' => FypProjectProposal::where('status', 'rejected')->count(),
        ];

        return view('academic.fyp.project-proposal.index', compact('proposals', 'stats'));
    }

    /**
     * Show form for student to create/edit their proposal.
     */
    public function edit(): View
    {
        $user = auth()->user();

        // Check if user is a student
        if (! $user->student) {
            abort(403, 'Only students can access this page.');
        }

        $student = $user->student->load(['company', 'academicTutor', 'industryCoach', 'group']);

        // Get or create proposal
        $proposal = FypProjectProposal::firstOrCreate(
            ['student_id' => $student->id],
            [
                'project_title' => '',
                'proposal_items' => [],
                'status' => 'draft',
            ]
        );

        return view('academic.fyp.project-proposal.edit', compact('student', 'proposal'));
    }

    /**
     * Update the student's proposal.
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        if (! $user->student) {
            abort(403, 'Only students can access this page.');
        }

        $proposal = FypProjectProposal::where('student_id', $user->student->id)->firstOrFail();

        if (! $proposal->isEditable()) {
            return redirect()->back()->with('error', 'This proposal cannot be edited.');
        }

        $validated = $request->validate([
            'project_title' => ['required', 'string', 'max:500'],
            'proposal_items' => ['required', 'array', 'min:1'],
            'proposal_items.*.problem_statement' => ['required', 'string'],
            'proposal_items.*.objective' => ['required', 'string'],
            'proposal_items.*.methodology' => ['required', 'string'],
        ]);

        $proposal->update([
            'project_title' => $validated['project_title'],
            'proposal_items' => $validated['proposal_items'],
        ]);

        return redirect()->back()->with('success', 'Project proposal saved successfully.');
    }

    /**
     * Submit the proposal for review.
     */
    public function submit()
    {
        $user = auth()->user();

        if (! $user->student) {
            abort(403, 'Only students can access this page.');
        }

        $proposal = FypProjectProposal::where('student_id', $user->student->id)->firstOrFail();

        if (! $proposal->canBeSubmitted()) {
            return redirect()->back()->with('error', 'Please fill in all required fields before submitting.');
        }

        $proposal->update([
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Project proposal submitted for review.');
    }

    /**
     * Show a specific proposal (Admin/AT view).
     */
    public function show(FypProjectProposal $proposal): View
    {
        $user = auth()->user();

        // Allow admin, AT, or the student themselves
        if (! $user->isAdmin() && $user->id !== $proposal->student->at_id && $user->id !== $proposal->student->user_id) {
            abort(403, 'Unauthorized access.');
        }

        $proposal->load(['student.user', 'student.company', 'student.academicTutor', 'student.industryCoach', 'student.group']);

        return view('academic.fyp.project-proposal.show', compact('proposal'));
    }

    /**
     * Approve a proposal (Admin/AT).
     */
    public function approve(Request $request, FypProjectProposal $proposal)
    {
        $user = auth()->user();

        if (! $user->isAdmin() && $user->id !== $proposal->student->at_id) {
            abort(403, 'Unauthorized access.');
        }

        if ($proposal->status !== 'submitted') {
            return redirect()->back()->with('error', 'Only submitted proposals can be approved.');
        }

        $proposal->update([
            'status' => 'approved',
            'approved_by' => $user->id,
            'approved_at' => now(),
            'remarks' => $request->remarks,
        ]);

        return redirect()->back()->with('success', 'Project proposal approved.');
    }

    /**
     * Reject a proposal (Admin/AT).
     */
    public function reject(Request $request, FypProjectProposal $proposal)
    {
        $user = auth()->user();

        if (! $user->isAdmin() && $user->id !== $proposal->student->at_id) {
            abort(403, 'Unauthorized access.');
        }

        if ($proposal->status !== 'submitted') {
            return redirect()->back()->with('error', 'Only submitted proposals can be rejected.');
        }

        $validated = $request->validate([
            'remarks' => ['required', 'string', 'max:1000'],
        ]);

        $proposal->update([
            'status' => 'rejected',
            'remarks' => $validated['remarks'],
        ]);

        return redirect()->back()->with('success', 'Project proposal sent back for revision.');
    }

    /**
     * Export proposal as PDF.
     */
    public function exportPdf(FypProjectProposal $proposal)
    {
        $user = auth()->user();

        // Allow admin, AT, IC, or the student themselves
        if (! $user->isAdmin() &&
            $user->id !== $proposal->student->at_id &&
            $user->id !== $proposal->student->ic_id &&
            $user->id !== $proposal->student->user_id) {
            abort(403, 'Unauthorized access.');
        }

        $proposal->load(['student.user', 'student.company', 'student.academicTutor', 'student.industryCoach', 'student.group']);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('academic.fyp.project-proposal.pdf', compact('proposal'))
            ->setPaper('a4', 'portrait');

        $filename = 'FYP_Proposal_'.$proposal->student->matric_no.'_'.now()->format('Ymd').'.pdf';

        return $pdf->download($filename);
    }
}
