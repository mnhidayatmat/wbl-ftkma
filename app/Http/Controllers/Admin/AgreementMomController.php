<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AgreementMom;
use App\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AgreementMomController extends Controller
{
    /**
     * Display a listing of MoMs.
     */
    public function index(Request $request): View
    {
        $query = AgreementMom::with(['companies', 'uploader']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhereHas('companies', function ($cq) use ($search) {
                        $cq->where('company_name', 'like', "%{$search}%");
                    });
            });
        }

        $moms = $query->orderBy('meeting_date', 'desc')->paginate(20);
        $companies = Company::orderBy('company_name')->get();

        return view('admin.agreements.moms.index', compact('moms', 'companies'));
    }

    /**
     * Show the form for creating a new MoM.
     */
    public function create(Request $request): View
    {
        $companies = Company::orderBy('company_name')->get();
        $selectedCompanyIds = $request->has('company_ids') ? (array) $request->company_ids : [];

        return view('admin.agreements.moms.create', compact('companies', 'selectedCompanyIds'));
    }

    /**
     * Store a newly created MoM.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'meeting_date' => ['required', 'date'],
            'company_ids' => ['required', 'array', 'min:1'],
            'company_ids.*' => ['exists:companies,id'],
            'document' => ['required', 'file', 'mimes:pdf', 'max:10240'],
            'remarks' => ['nullable', 'string', 'max:2000'],
        ]);

        // Upload document
        $documentPath = null;
        $documentName = null;
        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $documentName = $file->getClientOriginalName();
            $documentPath = $file->store('agreements/mom', 'public');
        }

        // Create MoM
        $mom = AgreementMom::create([
            'title' => $validated['title'],
            'meeting_date' => $validated['meeting_date'],
            'document_path' => $documentPath,
            'document_name' => $documentName,
            'remarks' => $validated['remarks'] ?? null,
            'uploaded_by' => auth()->id(),
        ]);

        // Attach companies
        $mom->companies()->attach($validated['company_ids']);

        return redirect()
            ->route('admin.agreements.moms.index')
            ->with('success', 'Minute of Meeting uploaded successfully and linked to ' . count($validated['company_ids']) . ' company(ies).');
    }

    /**
     * Show the form for editing the specified MoM.
     */
    public function edit(AgreementMom $mom): View
    {
        $companies = Company::orderBy('company_name')->get();
        $selectedCompanyIds = $mom->companies->pluck('id')->toArray();

        return view('admin.agreements.moms.edit', compact('mom', 'companies', 'selectedCompanyIds'));
    }

    /**
     * Update the specified MoM.
     */
    public function update(Request $request, AgreementMom $mom): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'meeting_date' => ['required', 'date'],
            'company_ids' => ['required', 'array', 'min:1'],
            'company_ids.*' => ['exists:companies,id'],
            'document' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'remarks' => ['nullable', 'string', 'max:2000'],
        ]);

        // Handle document upload
        if ($request->hasFile('document')) {
            // Delete old document
            if ($mom->document_path) {
                Storage::disk('public')->delete($mom->document_path);
            }

            $file = $request->file('document');
            $validated['document_name'] = $file->getClientOriginalName();
            $validated['document_path'] = $file->store('agreements/mom', 'public');
        }

        // Update MoM
        $mom->update([
            'title' => $validated['title'],
            'meeting_date' => $validated['meeting_date'],
            'document_path' => $validated['document_path'] ?? $mom->document_path,
            'document_name' => $validated['document_name'] ?? $mom->document_name,
            'remarks' => $validated['remarks'] ?? null,
        ]);

        // Sync companies
        $mom->companies()->sync($validated['company_ids']);

        return redirect()
            ->route('admin.agreements.moms.index')
            ->with('success', 'Minute of Meeting updated successfully.');
    }

    /**
     * Remove the specified MoM.
     */
    public function destroy(AgreementMom $mom): RedirectResponse
    {
        // Delete document
        if ($mom->document_path) {
            Storage::disk('public')->delete($mom->document_path);
        }

        // Detach companies first (for safety, even though cascade should handle it)
        $mom->companies()->detach();

        $mom->delete();

        return redirect()
            ->back()
            ->with('success', 'Minute of Meeting deleted successfully.');
    }

    /**
     * Download the MoM document.
     */
    public function download(AgreementMom $mom)
    {
        if (! $mom->document_path || ! Storage::disk('public')->exists($mom->document_path)) {
            return redirect()->back()->with('error', 'Document not found.');
        }

        $fileName = $mom->document_name ?? 'MoM_' . $mom->meeting_date->format('Y-m-d') . '.pdf';

        return Storage::disk('public')->download($mom->document_path, $fileName);
    }
}
