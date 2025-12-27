<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CompanyAgreement;
use App\Models\CompanyContact;
use App\Models\CompanyDocument;
use App\Models\CompanyNote;
use App\Models\Moa;
use App\Models\Mou;
use App\Models\Student;
use App\Imports\CompaniesImport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource with unified companies and agreements view.
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $query = Company::withCount('students')
            ->with(['mou', 'agreements' => function($query) {
                $query->orderByRaw("CASE WHEN status = 'Active' THEN 1 WHEN status = 'Pending' THEN 2 ELSE 3 END")
                      ->orderBy('created_at', 'desc');
            }]);

        // Search filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('company_name', 'LIKE', "%{$search}%")
                  ->orWhere('pic_name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        }

        // Filter by agreement type
        if ($request->filled('agreement_type')) {
            if ($request->agreement_type === 'none') {
                $query->doesntHave('agreements');
            } else {
                $query->whereHas('agreements', function($q) use ($request) {
                    $q->where('agreement_type', $request->agreement_type)
                      ->where('status', 'Active');
                });
            }
        }

        // Filter by agreement status
        if ($request->filled('agreement_status')) {
            $query->whereHas('agreements', function($q) use ($request) {
                $q->where('status', $request->agreement_status);
            });
        }

        // Filter by expiring dates
        if ($request->filled('expiring')) {
            $months = (int) $request->expiring;
            $query->whereHas('agreements', function($q) use ($months) {
                $q->where('status', 'Active')
                  ->where('end_date', '<=', now()->addMonths($months))
                  ->where('end_date', '>=', now());
            });
        }

        // Filter by company category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $companies = $query->latest()
            ->paginate(15)
            ->withQueryString();

        $stats = $this->getCompanyStatistics();

        return view('companies.index', compact('companies', 'search', 'stats'));
    }

    /**
     * Get statistics for unified view.
     */
    private function getCompanyStatistics(): array
    {
        return [
            'total_companies' => Company::count(),
            'with_active_agreements' => Company::whereHas('agreements', function($q) {
                $q->where('status', 'Active');
            })->count(),
            'with_expiring_agreements' => Company::whereHas('agreements', function($q) {
                $q->where('status', 'Active')
                  ->where('end_date', '<=', now()->addMonths(3))
                  ->where('end_date', '>=', now());
            })->count(),
            'total_students' => Student::whereNotNull('company_id')->count(),
            'mou_count' => CompanyAgreement::where('agreement_type', 'MoU')
                ->where('status', 'Active')->count(),
            'moa_count' => CompanyAgreement::where('agreement_type', 'MoA')
                ->where('status', 'Active')->count(),
        ];
    }

    /**
     * Search for companies by name (AJAX endpoint).
     */
    public function search(Request $request)
    {
        $query = $request->input('query', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $companies = Company::where('company_name', 'LIKE', "%{$query}%")
            ->select('id', 'company_name', 'email', 'phone', 'pic_name')
            ->limit(10)
            ->get();

        return response()->json($companies);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('companies.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'pic_name' => ['required', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'industry_type' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'website' => ['nullable', 'url', 'max:255'],
        ]);

        // Handle position_other field
        if ($request->has('position_other') && $request->position_other) {
            $validated['position'] = $request->position_other;
        }

        // Handle category_other field
        if ($request->has('category_other') && $request->category_other) {
            $validated['category'] = $request->category_other;
        }

        $company = Company::create($validated);

        // If position is HR, link IC users from the same company
        if ($validated['position'] === 'HR' || strtolower($validated['position'] ?? '') === 'hr') {
            // Find IC users with matching email domain
            $emailDomain = substr(strrchr($company->email, '@'), 1);

            $icUsers = \App\Models\User::whereHas('roles', function ($query) {
                $query->where('name', 'ic');
            })
                ->where('email', 'like', "%@{$emailDomain}")
                ->get();

            // Link IC users to this company
            foreach ($icUsers as $icUser) {
                $icUser->update(['company_id' => $company->id]);
            }
        }

        return redirect()->route('admin.companies.index')
            ->with('success', 'Company created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Company $company, Request $request): View
    {
        $tab = $request->get('tab', 'overview');

        $company->load([
            'students.group',
            'contacts',
            'notes.creator',
            'documents.uploader',
            'mou',
            'moas.students',
            'industryCoaches',
        ]);

        return view('companies.show', compact('company', 'tab'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Company $company): View
    {
        return view('companies.edit', compact('company'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Company $company): RedirectResponse
    {
        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'pic_name' => ['required', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'industry_type' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'website' => ['nullable', 'url', 'max:255'],
        ]);

        // Handle position_other field
        if ($request->has('position_other') && $request->position_other) {
            $validated['position'] = $request->position_other;
        }

        $company->update($validated);

        // If position is HR, link IC users from the same company
        if ($validated['position'] === 'HR' || strtolower($validated['position'] ?? '') === 'hr') {
            // Find IC users with matching email domain
            $emailDomain = substr(strrchr($company->email, '@'), 1);

            $icUsers = \App\Models\User::whereHas('roles', function ($query) {
                $query->where('name', 'ic');
            })
                ->where(function ($query) use ($company, $emailDomain) {
                    // Match by email domain
                    $query->where('email', 'like', "%@{$emailDomain}")
                        ->orWhere(function ($q) use ($company) {
                            // Or if already linked to this company
                            $q->where('company_id', $company->id);
                        });
                })
                ->get();

            // Link IC users to this company
            foreach ($icUsers as $icUser) {
                $icUser->update(['company_id' => $company->id]);
            }
        }

        return redirect()->route('admin.companies.show', $company)
            ->with('success', 'Company updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company): RedirectResponse
    {
        $company->delete();

        return redirect()->route('admin.companies.index')
            ->with('success', 'Company deleted successfully.');
    }

    // ==================== IMPORT/EXPORT ====================

    /**
     * Show the import form.
     */
    public function showImportForm(): View
    {
        return view('companies.import');
    }

    /**
     * Import companies from Excel file.
     */
    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240', // Max 10MB
        ]);

        try {
            $import = new CompaniesImport();
            Excel::import($import, $request->file('file'));

            $importedCount = $import->getImportedCount();
            $errors = $import->getErrors();
            $failures = $import->getFailures();

            $message = "Successfully imported {$importedCount} companies.";

            if (count($errors) > 0 || count($failures) > 0) {
                $errorMessages = [];

                foreach ($errors as $error) {
                    $errorMessages[] = $error;
                }

                foreach ($failures as $failure) {
                    $errorMessages[] = "Row {$failure['row']}: " . implode(', ', $failure['errors']);
                }

                session()->flash('import_errors', $errorMessages);

                if ($importedCount > 0) {
                    $message .= " Some records had errors and were skipped.";
                }
            }

            return redirect()->route('admin.companies.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->route('admin.companies.index')
                ->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    /**
     * Download Excel template.
     */
    public function downloadTemplate()
    {
        return Excel::download(
            new \App\Exports\CompaniesTemplateExport(),
            'companies_import_template.xlsx'
        );
    }

    // ==================== CONTACT MANAGEMENT ====================

    /**
     * Store a new contact for the company.
     */
    public function storeContact(Request $request, Company $company): RedirectResponse
    {
        if (! auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'role' => ['required', 'string', 'in:HR,Supervisor,Manager,Industry Coach'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'is_primary' => ['sometimes', 'boolean'],
        ]);

        // If this is set as primary, unset other primary contacts
        if ($request->has('is_primary') && $request->is_primary) {
            $company->contacts()->update(['is_primary' => false]);
        }

        $company->contacts()->create($validated);

        return redirect()->route('admin.companies.show', ['company' => $company, 'tab' => 'contacts'])
            ->with('success', 'Contact added successfully.');
    }

    /**
     * Update a contact.
     */
    public function updateContact(Request $request, Company $company, CompanyContact $contact): RedirectResponse
    {
        if (! auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'role' => ['required', 'string', 'in:HR,Supervisor,Manager,Industry Coach'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'is_primary' => ['sometimes', 'boolean'],
        ]);

        // If this is set as primary, unset other primary contacts
        if ($request->has('is_primary') && $request->is_primary) {
            $company->contacts()->where('id', '!=', $contact->id)->update(['is_primary' => false]);
        }

        $contact->update($validated);

        return redirect()->route('admin.companies.show', ['company' => $company, 'tab' => 'contacts'])
            ->with('success', 'Contact updated successfully.');
    }

    /**
     * Delete a contact.
     */
    public function destroyContact(Company $company, CompanyContact $contact): RedirectResponse
    {
        if (! auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $contact->delete();

        return redirect()->route('admin.companies.show', ['company' => $company, 'tab' => 'contacts'])
            ->with('success', 'Contact deleted successfully.');
    }

    // ==================== NOTE MANAGEMENT ====================

    /**
     * Store a new note for the company.
     */
    public function storeNote(Request $request, Company $company): RedirectResponse
    {
        if (! auth()->user()->isAdmin() && ! auth()->user()->isLecturer()) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'note' => ['required', 'string'],
            'follow_up_type' => ['required', 'string', 'in:Email,Call,Meeting,Reminder sent'],
            'next_action_date' => ['nullable', 'date'],
        ]);

        $validated['created_by'] = auth()->id();

        $company->notes()->create($validated);

        return redirect()->route('admin.companies.show', ['company' => $company, 'tab' => 'notes'])
            ->with('success', 'Note added successfully.');
    }

    /**
     * Delete a note.
     */
    public function destroyNote(Company $company, CompanyNote $note): RedirectResponse
    {
        if (! auth()->user()->isAdmin() && ! auth()->user()->isLecturer()) {
            abort(403, 'Unauthorized access.');
        }

        // Only allow deletion by creator or admin
        if ($note->created_by !== auth()->id() && ! auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $note->delete();

        return redirect()->route('admin.companies.show', ['company' => $company, 'tab' => 'notes'])
            ->with('success', 'Note deleted successfully.');
    }

    // ==================== DOCUMENT MANAGEMENT ====================

    /**
     * Store a new document for the company.
     */
    public function storeDocument(Request $request, Company $company): RedirectResponse
    {
        if (! auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'document_type' => ['required', 'string', 'in:MoU,MoA,NDA,Letter,Other'],
            'file' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:10240'], // 10MB max
            'description' => ['nullable', 'string'],
        ]);

        $file = $request->file('file');
        $path = $file->store('company-documents', 'public');

        $company->documents()->create([
            'title' => $validated['title'],
            'document_type' => $validated['document_type'],
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'uploaded_by' => auth()->id(),
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()->route('admin.companies.show', ['company' => $company, 'tab' => 'documents'])
            ->with('success', 'Document uploaded successfully.');
    }

    /**
     * Download a document.
     */
    public function downloadDocument(Company $company, CompanyDocument $document)
    {
        if (! Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'File not found.');
        }

        return Storage::disk('public')->download($document->file_path, $document->file_name);
    }

    /**
     * Delete a document.
     */
    public function destroyDocument(Company $company, CompanyDocument $document): RedirectResponse
    {
        if (! auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        Storage::disk('public')->delete($document->file_path);
        $document->delete();

        return redirect()->route('admin.companies.show', ['company' => $company, 'tab' => 'documents'])
            ->with('success', 'Document deleted successfully.');
    }

    // ==================== MoU MANAGEMENT ====================

    /**
     * Store or update MoU for the company.
     */
    public function storeMou(Request $request, Company $company): RedirectResponse
    {
        if (! auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'status' => ['required', 'string', 'in:Not Initiated,In Progress,Signed,Expired,Not Responding'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'signed_date' => ['nullable', 'date'],
            'file' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'remarks' => ['nullable', 'string'],
        ]);

        $mou = $company->mou ?? new Mou(['company_id' => $company->id]);

        // Handle file upload
        if ($request->hasFile('file')) {
            if ($mou->file_path && Storage::disk('public')->exists($mou->file_path)) {
                Storage::disk('public')->delete($mou->file_path);
            }
            $file = $request->file('file');
            $validated['file_path'] = $file->store('mous', 'public');
        }

        $validated['updated_by'] = auth()->id();
        if (! $mou->exists) {
            $validated['created_by'] = auth()->id();
        }

        $mou->fill($validated)->save();

        return redirect()->route('admin.companies.show', ['company' => $company, 'tab' => 'mou'])
            ->with('success', 'MoU updated successfully.');
    }

    // ==================== MoA MANAGEMENT ====================

    /**
     * Store a new MoA for the company.
     */
    public function storeMoa(Request $request, Company $company): RedirectResponse
    {
        if (! auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'moa_type' => ['required', 'string', 'in:Programme-based,Student-based'],
            'course_code' => ['nullable', 'string', 'in:PPE,IP,OSH,FYP,LI'],
            'status' => ['required', 'string', 'in:Draft,In Progress,Signed,Expired'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'signed_date' => ['nullable', 'date'],
            'file' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'remarks' => ['nullable', 'string'],
            'student_ids' => ['nullable', 'array'],
            'student_ids.*' => ['exists:students,id'],
        ]);

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $validated['file_path'] = $file->store('moas', 'public');
        }

        $validated['created_by'] = auth()->id();
        $validated['updated_by'] = auth()->id();

        $moa = $company->moas()->create($validated);

        // Attach students if provided
        if ($request->has('student_ids')) {
            $moa->students()->attach($request->student_ids);
        }

        return redirect()->route('admin.companies.show', ['company' => $company, 'tab' => 'moa'])
            ->with('success', 'MoA created successfully.');
    }

    /**
     * Update a MoA.
     */
    public function updateMoa(Request $request, Company $company, Moa $moa): RedirectResponse
    {
        if (! auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'moa_type' => ['required', 'string', 'in:Programme-based,Student-based'],
            'course_code' => ['nullable', 'string', 'in:PPE,IP,OSH,FYP,LI'],
            'status' => ['required', 'string', 'in:Draft,In Progress,Signed,Expired'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'signed_date' => ['nullable', 'date'],
            'file' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'remarks' => ['nullable', 'string'],
            'student_ids' => ['nullable', 'array'],
            'student_ids.*' => ['exists:students,id'],
        ]);

        // Handle file upload
        if ($request->hasFile('file')) {
            if ($moa->file_path && Storage::disk('public')->exists($moa->file_path)) {
                Storage::disk('public')->delete($moa->file_path);
            }
            $file = $request->file('file');
            $validated['file_path'] = $file->store('moas', 'public');
        }

        $validated['updated_by'] = auth()->id();
        $moa->update($validated);

        // Sync students
        if ($request->has('student_ids')) {
            $moa->students()->sync($request->student_ids);
        } else {
            $moa->students()->detach();
        }

        return redirect()->route('admin.companies.show', ['company' => $company, 'tab' => 'moa'])
            ->with('success', 'MoA updated successfully.');
    }

    /**
     * Delete a MoA.
     */
    public function destroyMoa(Company $company, Moa $moa): RedirectResponse
    {
        if (! auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        if ($moa->file_path && Storage::disk('public')->exists($moa->file_path)) {
            Storage::disk('public')->delete($moa->file_path);
        }

        $moa->delete();

        return redirect()->route('admin.companies.show', ['company' => $company, 'tab' => 'moa'])
            ->with('success', 'MoA deleted successfully.');
    }
}
