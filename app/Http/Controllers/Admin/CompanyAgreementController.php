<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\CompanyAgreement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class CompanyAgreementController extends Controller
{
    /**
     * Display a listing of agreements.
     */
    public function index(Request $request): View
    {
        $query = CompanyAgreement::with(['company', 'creator']);

        // Filter by agreement type
        if ($request->filled('type')) {
            $query->where('agreement_type', $request->type);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by company
        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        // Search by title or reference
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('agreement_title', 'like', "%{$search}%")
                  ->orWhere('reference_no', 'like', "%{$search}%")
                  ->orWhereHas('company', function($cq) use ($search) {
                      $cq->where('company_name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter expiring soon
        if ($request->filled('expiring')) {
            $months = (int) $request->expiring;
            $query->expiringWithin($months);
        }

        $agreements = $query->orderBy('created_at', 'desc')->paginate(20);
        $companies = Company::orderBy('company_name')->get();
        $stats = CompanyAgreement::getSummaryStats();

        return view('admin.agreements.index', compact('agreements', 'companies', 'stats'));
    }

    /**
     * Show the form for creating a new agreement.
     */
    public function create(Request $request): View
    {
        $companies = Company::orderBy('company_name')->get();
        $selectedCompanyId = $request->company_id;
        
        return view('admin.agreements.create', compact('companies', 'selectedCompanyId'));
    }

    /**
     * Store a newly created agreement.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'company_id' => ['required', 'exists:companies,id'],
            'agreement_type' => ['required', 'in:MoU,MoA,LOI'],
            'agreement_title' => ['nullable', 'string', 'max:255'],
            'reference_no' => ['nullable', 'string', 'max:100'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'signed_date' => ['nullable', 'date'],
            'status' => ['required', 'in:Active,Expired,Terminated,Pending,Draft'],
            'faculty' => ['nullable', 'string', 'max:255'],
            'programme' => ['nullable', 'string', 'max:255'],
            'remarks' => ['nullable', 'string', 'max:2000'],
            'document' => ['nullable', 'file', 'mimes:pdf', 'max:10240'], // 10MB max
        ]);

        // Check for duplicate
        $exists = CompanyAgreement::where('company_id', $validated['company_id'])
            ->where('agreement_type', $validated['agreement_type'])
            ->where('reference_no', $validated['reference_no'])
            ->whereNotNull('reference_no')
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->with('error', 'An agreement with this reference number already exists for this company.')
                ->withInput();
        }

        // Handle file upload
        $documentPath = null;
        if ($request->hasFile('document')) {
            $documentPath = $request->file('document')->store('agreements', 'public');
        }

        CompanyAgreement::create([
            ...$validated,
            'document_path' => $documentPath,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('admin.agreements.index')
            ->with('success', 'Agreement created successfully.');
    }

    /**
     * Display the specified agreement.
     */
    public function show(CompanyAgreement $agreement): View
    {
        $agreement->load(['company', 'creator', 'updater']);
        return view('admin.agreements.show', compact('agreement'));
    }

    /**
     * Show the form for editing the specified agreement.
     */
    public function edit(CompanyAgreement $agreement): View
    {
        $companies = Company::orderBy('company_name')->get();
        return view('admin.agreements.edit', compact('agreement', 'companies'));
    }

    /**
     * Update the specified agreement.
     */
    public function update(Request $request, CompanyAgreement $agreement): RedirectResponse
    {
        $validated = $request->validate([
            'company_id' => ['required', 'exists:companies,id'],
            'agreement_type' => ['required', 'in:MoU,MoA,LOI'],
            'agreement_title' => ['nullable', 'string', 'max:255'],
            'reference_no' => ['nullable', 'string', 'max:100'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'signed_date' => ['nullable', 'date'],
            'status' => ['required', 'in:Active,Expired,Terminated,Pending,Draft'],
            'faculty' => ['nullable', 'string', 'max:255'],
            'programme' => ['nullable', 'string', 'max:255'],
            'remarks' => ['nullable', 'string', 'max:2000'],
            'document' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
        ]);

        // Check for duplicate (excluding current record)
        $exists = CompanyAgreement::where('company_id', $validated['company_id'])
            ->where('agreement_type', $validated['agreement_type'])
            ->where('reference_no', $validated['reference_no'])
            ->whereNotNull('reference_no')
            ->where('id', '!=', $agreement->id)
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->with('error', 'An agreement with this reference number already exists for this company.')
                ->withInput();
        }

        // Handle file upload
        if ($request->hasFile('document')) {
            // Delete old file
            if ($agreement->document_path) {
                Storage::disk('public')->delete($agreement->document_path);
            }
            $validated['document_path'] = $request->file('document')->store('agreements', 'public');
        }

        $agreement->update([
            ...$validated,
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('admin.agreements.index')
            ->with('success', 'Agreement updated successfully.');
    }

    /**
     * Remove the specified agreement.
     */
    public function destroy(CompanyAgreement $agreement): RedirectResponse
    {
        // Delete document file
        if ($agreement->document_path) {
            Storage::disk('public')->delete($agreement->document_path);
        }

        $agreement->delete();

        return redirect()->route('admin.agreements.index')
            ->with('success', 'Agreement deleted successfully.');
    }

    /**
     * Show import form.
     */
    public function importForm(): View
    {
        return view('admin.agreements.import');
    }

    /**
     * Preview import data.
     */
    public function importPreview(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:10240'],
        ]);

        try {
            $file = $request->file('file');
            $spreadsheet = IOFactory::load($file->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Check if file is empty
            if (empty($rows)) {
                return redirect()->back()
                    ->with('error', 'The uploaded file appears to be empty. Please check the file and try again.');
            }

            // Get header row
            $headers = array_shift($rows);
            
            // Check if headers are empty
            if (empty($headers) || count(array_filter($headers)) === 0) {
                return redirect()->back()
                    ->with('error', 'No column headers found in the file. Please ensure your file has headers in the first row.');
            }
            
            $originalHeaders = $headers;
            $headers = array_map(function($h) {
                return strtolower(trim($h ?? ''));
            }, $headers);

            // Map columns
            $columnMap = $this->mapColumns($originalHeaders);

            // Check for required columns
            $missingRequired = [];
            if (!isset($columnMap['company_name'])) {
                $missingRequired[] = 'Company Name';
            }
            if (!isset($columnMap['agreement_type'])) {
                $missingRequired[] = 'Agreement Type';
            }

            if (!empty($missingRequired)) {
                return redirect()->back()
                    ->with('error', 'Required columns not found: ' . implode(', ', $missingRequired) . '. Your file headers are: ' . implode(', ', array_filter($originalHeaders)));
            }

            // Check if there's any data rows
            if (empty($rows)) {
                return redirect()->back()
                    ->with('error', 'No data rows found in the file. The file only contains headers.');
            }

            // Filter out completely empty rows
            $rows = array_filter($rows, function($row) {
                return count(array_filter($row, function($cell) {
                    return $cell !== null && $cell !== '';
                })) > 0;
            });

            if (empty($rows)) {
                return redirect()->back()
                    ->with('error', 'No data rows found. All rows in the file appear to be empty.');
            }

            $validRows = [];
            $invalidRows = [];

            foreach ($rows as $index => $row) {
                $rowNumber = $index + 2; // Excel row number (1-indexed + header)
                $rowData = $this->parseRow($row, $columnMap);

                // Validate row
                $errors = $this->validateImportRow($rowData);

                if (empty($errors)) {
                    $rowData['row_number'] = $rowNumber;
                    $validRows[] = $rowData;
                } else {
                    $invalidRows[] = [
                        'row_number' => $rowNumber,
                        'data' => $rowData,
                        'errors' => $errors,
                    ];
                }
            }

            // Store in session for actual import
            session(['import_data' => $validRows]);

            // Pass column mapping info for debugging
            $mappedColumns = [];
            foreach ($columnMap as $field => $index) {
                $mappedColumns[$field] = $originalHeaders[$index] ?? "Column {$index}";
            }

            return view('admin.agreements.import-preview', compact('validRows', 'invalidRows', 'mappedColumns'));

        } catch (\Exception $e) {
            \Log::error('Agreement Import Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()
                ->with('error', 'Error reading file: ' . $e->getMessage());
        }
    }

    /**
     * Execute the import.
     */
    public function importExecute(Request $request): RedirectResponse
    {
        $validRows = session('import_data', []);

        if (empty($validRows)) {
            return redirect()->route('admin.agreements.import')
                ->with('error', 'No valid data to import. Please upload a file first.');
        }

        $skipInvalid = $request->boolean('skip_invalid', true);
        $imported = 0;
        $skipped = 0;
        $errors = [];

        DB::beginTransaction();

        try {
            foreach ($validRows as $row) {
                // Find or create company
                $company = Company::firstOrCreate(
                    ['company_name' => $row['company_name']],
                    [
                        'industry_type' => $row['industry_type'] ?? null,
                        'address' => $row['address'] ?? null,
                        'email' => $row['email'] ?? null,
                        'pic_name' => $row['contact_person'] ?? null,
                    ]
                );

                // Check for duplicate agreement
                $exists = CompanyAgreement::where('company_id', $company->id)
                    ->where('agreement_type', $row['agreement_type'])
                    ->where('reference_no', $row['reference_no'])
                    ->whereNotNull('reference_no')
                    ->exists();

                if ($exists) {
                    if ($skipInvalid) {
                        $skipped++;
                        continue;
                    } else {
                        $errors[] = "Row {$row['row_number']}: Duplicate agreement exists.";
                        continue;
                    }
                }

                // Create agreement
                CompanyAgreement::create([
                    'company_id' => $company->id,
                    'agreement_type' => $row['agreement_type'],
                    'agreement_title' => $row['agreement_title'] ?? null,
                    'reference_no' => $row['reference_no'] ?? null,
                    'start_date' => $row['start_date'] ?? null,
                    'end_date' => $row['end_date'] ?? null,
                    'signed_date' => $row['signed_date'] ?? null,
                    'status' => $this->determineStatus($row),
                    'faculty' => $row['faculty'] ?? null,
                    'programme' => $row['programme'] ?? null,
                    'remarks' => $row['remarks'] ?? null,
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),
                ]);

                $imported++;
            }

            DB::commit();

            // Clear session data
            session()->forget('import_data');

            $message = "Successfully imported {$imported} agreements.";
            if ($skipped > 0) {
                $message .= " Skipped {$skipped} duplicates.";
            }

            return redirect()->route('admin.agreements.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.agreements.import')
                ->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    /**
     * Download import template.
     */
    public function downloadTemplate()
    {
        $headers = [
            'Company Name',
            'Agreement Type',
            'Agreement Title',
            'Reference No',
            'Start Date',
            'End Date',
            'Signed Date',
            'Status',
            'Faculty',
            'Programme',
            'Industry Type',
            'Address',
            'Email',
            'Contact Person',
            'Remarks',
        ];

        $callback = function() use ($headers) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for Excel to recognize UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            fputcsv($file, $headers);
            
            // Sample row 1
            fputcsv($file, [
                'ABC Company Sdn Bhd',
                'MoU',
                'Industrial Training Collaboration',
                'MOU/2024/001',
                '2024-01-01',
                '2027-01-01',
                '2024-01-15',
                'Active',
                'Faculty of Technology',
                'Bachelor of Computer Science',
                'Technology',
                '123 Main Street, Kuala Lumpur',
                'contact@abc.com',
                'John Doe',
                'Signed for 3 years',
            ]);

            // Sample row 2
            fputcsv($file, [
                'XYZ Industries',
                'MoA',
                'Research Collaboration Agreement',
                'MOA/2024/002',
                '2024-06-01',
                '2026-06-01',
                '2024-05-20',
                'Active',
                'Faculty of Engineering',
                'Mechanical Engineering',
                'Manufacturing',
                '456 Industrial Park, Kuantan',
                'info@xyz.com',
                'Jane Smith',
                'Two-year research partnership',
            ]);

            // Sample row 3
            fputcsv($file, [
                'Tech Solutions Bhd',
                'LOI',
                'Intent for Future Collaboration',
                'LOI/2024/003',
                '2024-03-01',
                '',
                '2024-02-28',
                'Pending',
                '',
                '',
                'Information Technology',
                'Tech Hub, Cyberjaya',
                'hr@techsolutions.my',
                'Ahmad Ali',
                'Initial discussion for partnership',
            ]);
            
            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="agreement_import_template.csv"',
        ]);
    }

    /**
     * Map Excel columns to expected fields.
     */
    private function mapColumns(array $headers): array
    {
        $map = [];
        $expectedColumns = [
            'company_name' => ['company name', 'company', 'nama syarikat', 'organization', 'companyname', 'company_name', 'nama_syarikat'],
            'agreement_type' => ['agreement type', 'type', 'jenis', 'jenis perjanjian', 'agreementtype', 'agreement_type', 'jenis_perjanjian'],
            'agreement_title' => ['agreement title', 'title', 'tajuk', 'nama perjanjian', 'agreementtitle', 'agreement_title', 'nama_perjanjian'],
            'reference_no' => ['reference no', 'reference', 'ref no', 'no rujukan', 'reference number', 'referenceno', 'reference_no', 'no_rujukan', 'ref_no', 'refno'],
            'start_date' => ['start date', 'tarikh mula', 'effective date', 'commencement date', 'startdate', 'start_date', 'tarikh_mula', 'effective_date'],
            'end_date' => ['end date', 'tarikh tamat', 'expiry date', 'expiration date', 'enddate', 'end_date', 'tarikh_tamat', 'expiry_date'],
            'signed_date' => ['signed date', 'tarikh tandatangan', 'signing date', 'date signed', 'signeddate', 'signed_date', 'tarikh_tandatangan', 'signing_date'],
            'status' => ['status'],
            'faculty' => ['faculty', 'fakulti'],
            'programme' => ['programme', 'program'],
            'industry_type' => ['industry type', 'industry', 'jenis industri', 'industrytype', 'industry_type', 'jenis_industri'],
            'address' => ['address', 'alamat'],
            'email' => ['email', 'e-mail', 'e_mail'],
            'contact_person' => ['contact person', 'contact', 'pic', 'person in charge', 'contactperson', 'contact_person', 'person_in_charge'],
            'remarks' => ['remarks', 'notes', 'catatan', 'remark', 'note'],
        ];

        // Normalize headers by removing special characters and extra whitespace
        $normalizedHeaders = array_map(function($header) {
            $header = strtolower(trim($header ?? ''));
            $header = preg_replace('/[^a-z0-9\s_]/', '', $header);
            $header = preg_replace('/\s+/', ' ', $header);
            return $header;
        }, $headers);

        foreach ($expectedColumns as $field => $possibleNames) {
            foreach ($normalizedHeaders as $index => $header) {
                if (empty($header)) continue;
                
                // Direct match
                if (in_array($header, $possibleNames)) {
                    $map[$field] = $index;
                    break;
                }
                
                // Partial match (contains)
                foreach ($possibleNames as $possibleName) {
                    if (str_contains($header, $possibleName) || str_contains($possibleName, $header)) {
                        $map[$field] = $index;
                        break 2;
                    }
                }
            }
        }

        // Log mapping for debugging
        \Log::info('Agreement Import Column Mapping', [
            'original_headers' => $headers,
            'normalized_headers' => $normalizedHeaders,
            'mapped_columns' => $map,
        ]);

        return $map;
    }

    /**
     * Parse a row using column map.
     */
    private function parseRow(array $row, array $columnMap): array
    {
        $data = [];
        foreach ($columnMap as $field => $index) {
            $value = $row[$index] ?? null;
            
            // Handle empty values
            if ($value === null || $value === '') {
                $data[$field] = null;
                continue;
            }
            
            // Parse dates
            if (in_array($field, ['start_date', 'end_date', 'signed_date'])) {
                $data[$field] = $this->parseDate($value);
            } elseif ($field === 'agreement_type') {
                // Normalize agreement type
                $normalized = strtoupper(trim($value));
                if (in_array($normalized, ['MOU', 'MOA', 'LOI'])) {
                    $data[$field] = $normalized === 'MOU' ? 'MoU' : ($normalized === 'MOA' ? 'MoA' : 'LOI');
                } else {
                    $data[$field] = $value; // Keep original for error display
                }
            } else {
                $data[$field] = is_string($value) ? trim($value) : $value;
            }
        }
        return $data;
    }

    /**
     * Parse date from various formats.
     */
    private function parseDate($value): ?string
    {
        if (empty($value)) {
            return null;
        }

        // If it's a numeric Excel date
        if (is_numeric($value)) {
            try {
                $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
                return $date->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }

        // Try parsing as date string
        try {
            return \Carbon\Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Validate import row.
     */
    private function validateImportRow(array $row): array
    {
        $errors = [];

        // Company name is required
        if (empty($row['company_name'])) {
            $errors[] = 'Company name is required.';
        }

        // Agreement type must be valid
        $validTypes = ['MoU', 'MoA', 'LOI', 'mou', 'moa', 'loi', 'MOU', 'MOA'];
        $agreementType = $row['agreement_type'] ?? null;
        
        if (empty($agreementType)) {
            $errors[] = 'Agreement type is required.';
        } elseif (!in_array($agreementType, $validTypes)) {
            $errors[] = "Agreement type '{$agreementType}' is invalid. Must be MoU, MoA, or LOI.";
        }

        // End date must be after start date
        if (!empty($row['start_date']) && !empty($row['end_date'])) {
            $startTimestamp = strtotime($row['start_date']);
            $endTimestamp = strtotime($row['end_date']);
            
            if ($startTimestamp && $endTimestamp && $startTimestamp > $endTimestamp) {
                $errors[] = 'End date must be after start date.';
            }
        }

        return $errors;
    }

    /**
     * Determine status based on dates.
     */
    private function determineStatus(array $row): string
    {
        if (!empty($row['status'])) {
            $status = ucfirst(strtolower($row['status']));
            if (in_array($status, ['Active', 'Expired', 'Terminated', 'Pending', 'Draft'])) {
                return $status;
            }
        }

        // Auto-determine based on dates
        if (!empty($row['end_date']) && strtotime($row['end_date']) < time()) {
            return 'Expired';
        }

        if (!empty($row['signed_date'])) {
            return 'Active';
        }

        return 'Draft';
    }

    /**
     * Update agreement status (mark as expired/terminated).
     */
    public function updateStatus(Request $request, CompanyAgreement $agreement): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:Active,Expired,Terminated,Pending,Draft'],
        ]);

        $agreement->update([
            'status' => $validated['status'],
            'updated_by' => auth()->id(),
        ]);

        return redirect()->back()
            ->with('success', 'Agreement status updated successfully.');
    }
}

