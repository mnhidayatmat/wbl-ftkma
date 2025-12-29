<?php

namespace App\Http\Controllers;

use App\Imports\CompaniesPreviewImport;
use App\Models\Company;
use App\Models\CompanyAgreement;
use App\Models\CompanyContact;
use App\Models\CompanyDocument;
use App\Models\CompanyNote;
use App\Models\Moa;
use App\Models\Mou;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            ->with(['mou', 'agreements' => function ($query) {
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
                $query->whereHas('agreements', function ($q) use ($request) {
                    $q->where('agreement_type', $request->agreement_type)
                        ->where('status', 'Active');
                });
            }
        }

        // Filter by agreement status
        if ($request->filled('agreement_status')) {
            $query->whereHas('agreements', function ($q) use ($request) {
                $q->where('status', $request->agreement_status);
            });
        }

        // Filter by expiring dates
        if ($request->filled('expiring')) {
            $months = (int) $request->expiring;
            $query->whereHas('agreements', function ($q) use ($months) {
                $q->where('status', 'Active')
                    ->where('end_date', '<=', now()->addMonths($months))
                    ->where('end_date', '>=', now());
            });
        }

        // Filter by company category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Handle per_page parameter (supports 'all' for showing all records)
        $perPage = $request->input('per_page', 15);
        if ($perPage === 'all') {
            $companies = $query->latest()->get();
            // Wrap in a paginator-like object for view compatibility
            $companies = new \Illuminate\Pagination\LengthAwarePaginator(
                $companies,
                $companies->count(),
                $companies->count(),
                1,
                ['path' => $request->url(), 'query' => $request->query()]
            );
        } else {
            $companies = $query->latest()
                ->paginate((int) $perPage)
                ->withQueryString();
        }

        $stats = $this->getCompanyStatistics();

        return view('companies.index', compact('companies', 'search', 'stats'));
    }

    /**
     * Get statistics for unified view.
     */
    private function getCompanyStatistics(): array
    {
        $totalCompanies = Company::count();
        $totalAgreements = CompanyAgreement::count();

        // Count agreements by type
        $mouActive = CompanyAgreement::where('agreement_type', 'MoU')->where('status', 'Active')->count();
        $moaActive = CompanyAgreement::where('agreement_type', 'MoA')->where('status', 'Active')->count();
        $loiActive = CompanyAgreement::where('agreement_type', 'LOI')->where('status', 'Active')->count();

        // Count agreements by status
        $activeAgreements = CompanyAgreement::where('status', 'Active')->count();
        $pendingAgreements = CompanyAgreement::where('status', 'Pending')->count();
        $draftAgreements = CompanyAgreement::where('status', 'Draft')->count();
        $notStartedAgreements = CompanyAgreement::where('status', 'Not Started')->count();
        $expiredAgreements = CompanyAgreement::where('status', 'Expired')->count();

        // Expiring agreements (within next 3 and 6 months)
        $expiringIn3Months = CompanyAgreement::where('status', 'Active')
            ->where('end_date', '<=', now()->addMonths(3))
            ->where('end_date', '>=', now())
            ->count();

        $expiringIn6Months = CompanyAgreement::where('status', 'Active')
            ->where('end_date', '<=', now()->addMonths(6))
            ->where('end_date', '>=', now())
            ->count();

        // Companies without any agreements
        $companiesWithoutAgreements = Company::doesntHave('agreements')->count();

        // Category distribution
        $categoryDistribution = Company::selectRaw('COALESCE(category, "Uncategorized") as category, COUNT(*) as count')
            ->groupBy('category')
            ->orderByDesc('count')
            ->get()
            ->map(fn ($item) => ['name' => $item->category ?: 'Uncategorized', 'count' => $item->count])
            ->toArray();

        // Agreement type distribution (all statuses)
        $agreementTypeDistribution = CompanyAgreement::selectRaw('agreement_type, status, COUNT(*) as count')
            ->groupBy('agreement_type', 'status')
            ->get()
            ->groupBy('agreement_type')
            ->map(function ($items) {
                return $items->pluck('count', 'status')->toArray();
            })
            ->toArray();

        // Monthly agreement trends (last 12 months)
        $agreementTrends = CompanyAgreement::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(fn ($item) => ['month' => $item->month, 'count' => $item->count])
            ->toArray();

        // Expiring soon list (for attention required section)
        $expiringSoon = CompanyAgreement::with('company')
            ->where('status', 'Active')
            ->where('end_date', '<=', now()->addMonths(3))
            ->where('end_date', '>=', now())
            ->orderBy('end_date')
            ->limit(10)
            ->get();

        return [
            'total_companies' => $totalCompanies,
            'total_agreements' => $totalAgreements,
            'with_active_agreements' => Company::whereHas('agreements', function ($q) {
                $q->where('status', 'Active');
            })->count(),
            'with_pending_agreements' => Company::whereHas('agreements', function ($q) {
                $q->where('status', 'Pending');
            })->count(),
            'with_draft_agreements' => Company::whereHas('agreements', function ($q) {
                $q->where('status', 'Draft');
            })->count(),
            'with_expired_agreements' => Company::whereHas('agreements', function ($q) {
                $q->where('status', 'Expired');
            })->count(),
            'companies_without_agreements' => $companiesWithoutAgreements,
            'total_students' => Student::whereNotNull('company_id')->count(),
            'mou_count' => $mouActive,
            'moa_count' => $moaActive,
            'loi_count' => $loiActive,
            'active_agreements' => $activeAgreements,
            'pending_agreements' => $pendingAgreements,
            'draft_agreements' => $draftAgreements,
            'not_started_agreements' => $notStartedAgreements,
            'expired_agreements' => $expiredAgreements,
            'expiring_3_months' => $expiringIn3Months,
            'expiring_6_months' => $expiringIn6Months,
            'category_distribution' => $categoryDistribution,
            'agreement_type_distribution' => $agreementTypeDistribution,
            'agreement_trends' => $agreementTrends,
            'expiring_soon' => $expiringSoon,
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
        // Consolidated validation for both company and agreement
        $validated = $request->validate([
            // Company fields
            'company_name' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'pic_name' => ['nullable', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'industry_type' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'website' => ['nullable', 'url', 'max:255'],

            // Agreement fields
            'agreement_type' => ['required', 'in:MoU,MoA,LOI'],
            'agreement_title' => ['nullable', 'string', 'max:255'],
            'reference_no' => ['nullable', 'string', 'max:100'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'signed_date' => ['nullable', 'date'],
            'status' => ['required', 'in:Not Started,Draft,Pending,Active,Expired,Terminated'],
            'faculty' => ['nullable', 'string', 'max:255'],
            'programme' => ['nullable', 'string', 'max:255'],
            'remarks' => ['nullable', 'string', 'max:2000'],
            'document' => ['nullable', 'file', 'mimes:pdf', 'max:10240'], // 10MB max
        ]);

        // Handle "Other" fields (preserve existing logic)
        if ($request->has('position_other') && $request->position_other) {
            $validated['position'] = $request->position_other;
        }
        if ($request->has('category_other') && $request->category_other) {
            $validated['category'] = $request->category_other;
        }

        // Start database transaction for atomic creation
        DB::beginTransaction();

        try {
            // Create Company
            $company = Company::create([
                'company_name' => $validated['company_name'],
                'category' => $validated['category'] ?? null,
                'pic_name' => $validated['pic_name'],
                'position' => $validated['position'] ?? null,
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'industry_type' => $validated['industry_type'] ?? null,
                'address' => $validated['address'] ?? null,
                'website' => $validated['website'] ?? null,
            ]);

            // IC auto-linking for HR positions (preserve existing logic)
            if ($validated['position'] === 'HR' || strtolower($validated['position'] ?? '') === 'hr') {
                $emailDomain = substr(strrchr($company->email, '@'), 1);
                $icUsers = \App\Models\User::whereHas('roles', function ($query) {
                    $query->where('name', 'ic');
                })
                    ->where('email', 'like', "%@{$emailDomain}")
                    ->get();

                foreach ($icUsers as $icUser) {
                    $icUser->update(['company_id' => $company->id]);
                }
            }

            // Handle agreement document upload
            $documentPath = null;
            if ($request->hasFile('document')) {
                $documentPath = $request->file('document')->store('agreements', 'public');
            }

            // Create Agreement
            CompanyAgreement::create([
                'company_id' => $company->id,
                'agreement_type' => $validated['agreement_type'],
                'agreement_title' => $validated['agreement_title'] ?? null,
                'reference_no' => $validated['reference_no'] ?? null,
                'start_date' => $validated['start_date'] ?? null,
                'end_date' => $validated['end_date'] ?? null,
                'signed_date' => $validated['signed_date'] ?? null,
                'status' => $validated['status'],
                'faculty' => $validated['faculty'] ?? null,
                'programme' => $validated['programme'] ?? null,
                'remarks' => $validated['remarks'] ?? null,
                'document_path' => $documentPath,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);

            DB::commit();

            return redirect()->route('admin.companies.show', $company)
                ->with('success', 'Company and Agreement created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            // Clean up uploaded file if transaction fails
            if (isset($documentPath) && $documentPath) {
                Storage::disk('public')->delete($documentPath);
            }

            return redirect()->back()
                ->with('error', 'Failed to create company and agreement: '.$e->getMessage())
                ->withInput();
        }
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
            'pic_name' => ['nullable', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'industry_type' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'website' => ['nullable', 'url', 'max:255'],
        ]);

        // Handle position_other field
        if ($request->has('position_other') && $request->position_other) {
            $validated['position'] = $request->position_other;
        }

        // Handle category_other field (Industry Type)
        if ($request->has('category_other') && $request->category_other) {
            $validated['category'] = $request->category_other;
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
     * Preview companies import before committing to database.
     */
    public function previewImport(Request $request): RedirectResponse|View
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240', // 10MB max
        ]);

        try {
            // Parse and validate the file
            $import = new CompaniesPreviewImport;
            Excel::import($import, $request->file('file'));
            $previewData = $import->getPreviewData();

            // Calculate statistics
            $stats = [
                'total' => count($previewData),
                'valid' => count(array_filter($previewData, fn ($row) => $row['valid'])),
                'invalid' => count(array_filter($previewData, fn ($row) => ! $row['valid'])),
            ];

            // Store in session
            session([
                'company_import_preview' => [
                    'data' => $previewData,
                    'stats' => $stats,
                    'uploaded_at' => now()->timestamp,
                ],
            ]);

            // Redirect to preview page
            return view('companies.import_preview', [
                'previewData' => $previewData,
                'stats' => $stats,
            ]);

        } catch (\Exception $e) {
            return redirect()->route('admin.companies.index')
                ->with('error', 'Failed to process file: '.$e->getMessage());
        }
    }

    /**
     * Confirm and execute the import after preview.
     */
    public function confirmImport(Request $request): RedirectResponse
    {
        // Check if preview data exists in session
        $previewSession = session('company_import_preview');

        if (! $previewSession) {
            return redirect()->route('admin.companies.index')
                ->with('error', 'No import data found. Please upload a file first.');
        }

        $previewData = $previewSession['data'];
        $stats = $previewSession['stats'];

        try {
            $imported = 0;
            $skipped = 0;

            // Import only valid rows
            foreach ($previewData as $row) {
                if ($row['valid']) {
                    $data = $row['data'];

                    Company::create([
                        'company_name' => $data['company_name'],
                        'category' => $data['category'] ?? 'Other',
                        'pic_name' => $data['pic_name'],
                        'email' => $data['email'],
                        'phone' => $data['phone'],
                        'address' => $data['address'],
                        'city' => $data['city'],
                        'state' => $data['state'],
                        'postcode' => $data['postcode'],
                        'country' => $data['country'] ?? 'Malaysia',
                        'website' => $data['website'],
                        'industry' => $data['industry'],
                        'company_size' => $data['company_size'],
                        'notes' => $data['notes'],
                    ]);

                    $imported++;
                } else {
                    $skipped++;
                }
            }

            // Clear session
            session()->forget('company_import_preview');

            $message = "Successfully imported {$imported} companies.";
            if ($skipped > 0) {
                $message .= " {$skipped} rows were skipped due to validation errors.";
            }

            return redirect()->route('admin.companies.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->route('admin.companies.index')
                ->with('error', 'Import failed: '.$e->getMessage());
        }
    }

    /**
     * Cancel the import preview.
     */
    public function cancelImport(): RedirectResponse
    {
        session()->forget('company_import_preview');

        return redirect()->route('admin.companies.index')
            ->with('info', 'Import cancelled.');
    }

    /**
     * Download Excel template.
     */
    public function downloadTemplate()
    {
        return Excel::download(
            new \App\Exports\CompaniesTemplateExport,
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
     * Update a note.
     */
    public function updateNote(Request $request, Company $company, CompanyNote $note): RedirectResponse
    {
        if (! auth()->user()->isAdmin() && ! auth()->user()->isLecturer()) {
            abort(403, 'Unauthorized access.');
        }

        // Only allow update by creator or admin
        if ($note->created_by !== auth()->id() && ! auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'note' => ['required', 'string'],
            'follow_up_type' => ['required', 'string', 'in:Email,Call,Meeting,Reminder sent'],
            'next_action_date' => ['nullable', 'date'],
        ]);

        $note->update($validated);

        return redirect()->route('admin.companies.show', ['company' => $company, 'tab' => 'notes'])
            ->with('success', 'Note updated successfully.');
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

    /**
     * Update note action status (mark as completed or dismissed).
     */
    public function updateNoteStatus(Request $request, Company $company, CompanyNote $note): RedirectResponse
    {
        if (! auth()->user()->isAdmin() && ! auth()->user()->isLecturer()) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'action_status' => ['required', 'string', 'in:pending,completed,dismissed'],
        ]);

        $note->update($validated);

        $statusLabel = match ($validated['action_status']) {
            'completed' => 'completed',
            'dismissed' => 'dismissed',
            default => 'pending',
        };

        return redirect()->back()
            ->with('success', "Follow-up action marked as {$statusLabel}.");
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
