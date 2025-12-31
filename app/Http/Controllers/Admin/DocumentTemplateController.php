<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentTemplate;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\TemplateProcessor;

class DocumentTemplateController extends Controller
{
    /**
     * Display SAL template page.
     */
    public function sal(): View
    {
        $template = DocumentTemplate::getSalTemplate();
        $sampleStudent = Student::with('group')->first();
        $variables = DocumentTemplate::getSalVariables();

        return view('admin.documents.sal', [
            'template' => $template,
            'sampleStudent' => $sampleStudent,
            'variables' => $variables,
        ]);
    }

    /**
     * Display SAL template editor.
     */
    public function editSal(): View
    {
        $template = DocumentTemplate::getSalTemplate();
        $variables = DocumentTemplate::getSalVariables();
        $sampleStudent = Student::first();

        return view('admin.documents.sal-editor', [
            'template' => $template,
            'variables' => $variables,
            'sampleStudent' => $sampleStudent,
        ]);
    }

    /**
     * Update SAL template.
     */
    public function updateSal(Request $request)
    {
        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'salutation' => ['nullable', 'string', 'max:255'],
            'body_content' => ['nullable', 'string'],
            'closing_text' => ['nullable', 'string', 'max:255'],
            'signatory_name' => ['nullable', 'string', 'max:255'],
            'signatory_title' => ['nullable', 'string', 'max:255'],
            'signatory_department' => ['nullable', 'string', 'max:255'],
            'sal_release_date' => ['nullable', 'date'],
            'sal_reference_number' => ['nullable', 'string', 'max:255'],
            'director_name' => ['nullable', 'string', 'max:255'],
            'director_signature' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
            'settings' => ['nullable', 'array'],
            'settings.font_size' => ['nullable', 'string'],
            'settings.line_height' => ['nullable', 'string'],
            'settings.margin_top' => ['nullable', 'string'],
            'settings.margin_bottom' => ['nullable', 'string'],
            'settings.margin_left' => ['nullable', 'string'],
            'settings.margin_right' => ['nullable', 'string'],
            'settings.show_logo' => ['nullable'],
            'settings.show_date' => ['nullable'],
            'settings.date_format' => ['nullable', 'string'],
        ]);

        $template = DocumentTemplate::getSalTemplate();

        // Build update data - only include fields that were provided
        $updateData = ['updated_by' => auth()->id()];

        if (isset($validated['title'])) {
            $updateData['title'] = $validated['title'];
        }
        if (isset($validated['subtitle'])) {
            $updateData['subtitle'] = $validated['subtitle'];
        }
        if (isset($validated['salutation'])) {
            $updateData['salutation'] = $validated['salutation'];
        }
        if (isset($validated['body_content'])) {
            $updateData['body_content'] = $validated['body_content'];
        }
        if (isset($validated['closing_text'])) {
            $updateData['closing_text'] = $validated['closing_text'];
        }
        if (isset($validated['signatory_name'])) {
            $updateData['signatory_name'] = $validated['signatory_name'];
        }
        if (isset($validated['signatory_title'])) {
            $updateData['signatory_title'] = $validated['signatory_title'];
        }
        if (isset($validated['signatory_department'])) {
            $updateData['signatory_department'] = $validated['signatory_department'];
        }

        // Handle settings - merge with existing settings to preserve other values
        $currentSettings = $template->settings ?? [];

        if (isset($validated['settings'])) {
            $settings = array_merge($currentSettings, $validated['settings']);
            // For form submissions, process checkboxes
            if (! $request->expectsJson()) {
                $settings['show_logo'] = $request->has('settings.show_logo');
                $settings['show_date'] = $request->has('settings.show_date');
            }
        } else {
            $settings = $currentSettings;
        }

        // Store sal_release_date and sal_reference_number in settings
        if (array_key_exists('sal_release_date', $validated)) {
            $settings['sal_release_date'] = $validated['sal_release_date'];
        }
        if (array_key_exists('sal_reference_number', $validated)) {
            $settings['sal_reference_number'] = $validated['sal_reference_number'];
        }

        // Store director_name in settings - always update from form
        if (array_key_exists('director_name', $validated)) {
            $settings['director_name'] = $validated['director_name'];
        }

        // Handle director signature upload
        if ($request->hasFile('director_signature')) {
            // Delete old signature if exists
            if (isset($currentSettings['director_signature_path'])) {
                Storage::disk('public')->delete($currentSettings['director_signature_path']);
            }

            // Store new signature
            $signaturePath = $request->file('director_signature')->store('document-templates/signatures', 'public');
            $settings['director_signature_path'] = $signaturePath;
        }

        $updateData['settings'] = $settings;

        $template->update($updateData);

        // Return JSON for AJAX requests
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Template saved successfully.',
            ]);
        }

        return redirect()->route('admin.documents.sal')->with('success', 'SAL settings saved successfully.');
    }

    /**
     * Preview SAL PDF with sample data.
     */
    public function previewSal()
    {
        $student = Student::with(['user', 'group', 'company'])->first();
        if (! $student) {
            return back()->with('error', 'No student found for preview.');
        }

        $template = DocumentTemplate::getSalTemplate();

        // Calculate WBL duration from group dates
        $wblDuration = '6 months';
        $groupStartDate = null;
        $groupEndDate = null;

        if ($student->group) {
            $groupStartDate = $student->group->start_date;
            $groupEndDate = $student->group->end_date;

            if ($groupStartDate && $groupEndDate) {
                $start = \Carbon\Carbon::parse($groupStartDate);
                $end = \Carbon\Carbon::parse($groupEndDate);
                $months = $start->diffInMonths($end);
                $wblDuration = $months.' '.($months == 1 ? 'month' : 'months');
            }
        }

        // Get paper size and orientation from template settings
        $pageSize = $template->settings['page_size'] ?? 'a4';
        $orientation = $template->settings['orientation'] ?? 'portrait';

        $pdf = Pdf::loadView('placement.pdf.sal-template', [
            'student' => $student,
            'wblDuration' => $wblDuration,
            'groupStartDate' => $groupStartDate,
            'groupEndDate' => $groupEndDate,
            'generatedAt' => now(),
            'template' => $template,
        ])->setPaper($pageSize, $orientation);

        return $pdf->stream('SAL_Template_Preview.pdf');
    }

    /**
     * Reset SAL template to defaults.
     */
    public function resetSal(): RedirectResponse
    {
        $template = DocumentTemplate::where('type', 'SAL')->first();

        if ($template) {
            $defaults = DocumentTemplate::getDefaultSalTemplate();
            $template->update(array_merge($defaults, ['updated_by' => auth()->id()]));
        }

        return redirect()->route('admin.documents.sal.edit')->with('success', 'SAL template reset to defaults.');
    }

    /**
     * Display SAL template designer (drag-and-drop editor).
     */
    public function designerSal(): View
    {
        $template = DocumentTemplate::getSalTemplate();
        $variables = DocumentTemplate::getSalVariables();

        return view('admin.documents.sal-designer', [
            'template' => $template,
            'variables' => $variables,
        ]);
    }

    /**
     * Update SAL template canvas elements.
     */
    public function updateSalCanvas(Request $request)
    {
        $validated = $request->validate([
            'canvas_elements' => ['required', 'array'],
            'canvas_width' => ['nullable', 'integer', 'min:100'],
            'canvas_height' => ['nullable', 'integer', 'min:100'],
            'settings' => ['nullable', 'array'],
            'settings.page_size' => ['nullable', 'string'],
            'settings.orientation' => ['nullable', 'string'],
            'settings.margins' => ['nullable', 'array'],
            'settings.background' => ['nullable', 'string'],
        ]);

        $template = DocumentTemplate::getSalTemplate();

        $updateData = [
            'canvas_elements' => $validated['canvas_elements'],
            'canvas_width' => $validated['canvas_width'] ?? 595,
            'canvas_height' => $validated['canvas_height'] ?? 842,
            'updated_by' => auth()->id(),
        ];

        // Merge settings to preserve sal_release_date, sal_reference_number, etc.
        if (isset($validated['settings'])) {
            $currentSettings = $template->settings ?? [];
            $updateData['settings'] = array_merge($currentSettings, $validated['settings']);
        }

        $template->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Template saved successfully.',
        ]);
    }

    /**
     * Display SCL template page.
     */
    public function scl(): View
    {
        $template = DocumentTemplate::getSclTemplate();
        $sampleStudent = Student::whereHas('placementTracking', function ($q) {
            $q->whereNotNull('scl_file_path');
        })->first() ?? Student::first();
        $variables = DocumentTemplate::getSclVariables();

        return view('admin.documents.scl', [
            'template' => $template,
            'sampleStudent' => $sampleStudent,
            'variables' => $variables,
        ]);
    }

    /**
     * Preview SCL PDF with sample data.
     */
    public function previewScl()
    {
        $student = Student::with(['placementTracking', 'group', 'company'])->first();
        if (! $student) {
            return back()->with('error', 'No student found for preview.');
        }

        $tracking = $student->placementTracking;
        $group = $student->group;

        $pdf = Pdf::loadView('placement.pdf.scl', [
            'student' => $student,
            'tracking' => $tracking,
            'group' => $group,
            'generatedAt' => now(),
        ])->setPaper('a4', 'portrait');

        return $pdf->stream('SCL_Template_Preview.pdf');
    }

    /**
     * Display MoU template page.
     */
    public function mou(): View
    {
        $template = DocumentTemplate::getMouTemplate();
        $variables = DocumentTemplate::getMouVariables();

        return view('admin.documents.mou', [
            'template' => $template,
            'variables' => $variables,
        ]);
    }

    /**
     * Update MoU template settings.
     */
    public function updateMou(Request $request): RedirectResponse
    {
        $template = DocumentTemplate::getMouTemplate();

        $settings = $template->settings ?? [];
        $settings['mou_number'] = $request->input('mou_number');
        $settings['company_shortname'] = $request->input('company_shortname');
        $settings['signed_behalf_name'] = $request->input('signed_behalf_name');
        $settings['signed_behalf_position'] = $request->input('signed_behalf_position');
        $settings['witness_name'] = $request->input('witness_name');
        $settings['witness_position'] = $request->input('witness_position');
        $settings['liaison_officer'] = $request->input('liaison_officer');
        $settings['vc_name'] = $request->input('vc_name');
        $settings['dvc_name'] = $request->input('dvc_name');

        $template->update([
            'settings' => $settings,
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('admin.documents.mou')
            ->with('success', 'MoU default settings saved successfully.');
    }

    /**
     * Display MoU Word template management page.
     */
    public function wordTemplateMou(): View
    {
        $template = DocumentTemplate::getMouTemplate();
        $variables = DocumentTemplate::getMouVariables();
        $detectedVariables = [];

        // Scan template to detect which variables are present
        if ($template->word_template_path && Storage::disk('public')->exists($template->word_template_path)) {
            try {
                $templatePath = Storage::disk('public')->path($template->word_template_path);
                $templateProcessor = new TemplateProcessor($templatePath);
                $detectedVariables = $templateProcessor->getVariables();
            } catch (\Exception $e) {
                // Silently fail - template might be corrupted
            }
        }

        return view('admin.documents.mou-word-template', [
            'template' => $template,
            'variables' => $variables,
            'detectedVariables' => $detectedVariables,
        ]);
    }

    /**
     * Upload Word template for MOU.
     */
    public function uploadMouWordTemplate(Request $request): RedirectResponse
    {
        $request->validate([
            'word_template' => ['required', 'file', 'mimes:docx', 'max:10240'],
        ]);

        $template = DocumentTemplate::getMouTemplate();

        // Delete old template if exists
        if ($template->word_template_path && Storage::disk('public')->exists($template->word_template_path)) {
            Storage::disk('public')->delete($template->word_template_path);
        }

        // Store new template
        $file = $request->file('word_template');
        $path = $file->store('document-templates/mou', 'public');

        $template->update([
            'word_template_path' => $path,
            'word_template_original_name' => $file->getClientOriginalName(),
            'template_mode' => 'word',
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('admin.documents.mou')
            ->with('success', 'MOU Word template uploaded successfully.');
    }

    /**
     * Delete MOU Word template.
     */
    public function deleteMouWordTemplate(): RedirectResponse
    {
        $template = DocumentTemplate::getMouTemplate();

        if ($template->word_template_path && Storage::disk('public')->exists($template->word_template_path)) {
            Storage::disk('public')->delete($template->word_template_path);
        }

        $template->update([
            'word_template_path' => null,
            'word_template_original_name' => null,
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('admin.documents.mou')
            ->with('success', 'MOU Word template deleted successfully.');
    }

    /**
     * Download MOU Word template.
     */
    public function downloadMouWordTemplate()
    {
        $template = DocumentTemplate::getMouTemplate();

        if (! $template->word_template_path || ! Storage::disk('public')->exists($template->word_template_path)) {
            return redirect()->route('admin.documents.mou')
                ->with('error', 'No MOU Word template uploaded.');
        }

        return Storage::disk('public')->download(
            $template->word_template_path,
            $template->word_template_original_name ?? 'MOU_Template.docx'
        );
    }

    /**
     * Preview MoU PDF with example data.
     */
    public function previewMou()
    {
        $template = DocumentTemplate::getMouTemplate();

        if (! $template->word_template_path || ! Storage::disk('public')->exists($template->word_template_path)) {
            return redirect()->route('admin.documents.mou')
                ->with('error', 'No MOU Word template uploaded. Please upload a template first.');
        }

        // Generate Word document with example data
        $docxPath = $this->generateMouWithExampleData($template);

        // Convert to PDF using LibreOffice
        $pdfPath = $this->convertWordToPdfLibreOffice($docxPath);

        // Clean up Word file
        if (file_exists($docxPath)) {
            unlink($docxPath);
        }

        // Stream PDF inline
        return response()->file($pdfPath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="MOU_Preview.pdf"',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Generate MOU Word document with example data for preview.
     */
    protected function generateMouWithExampleData(DocumentTemplate $template): string
    {
        $templatePath = Storage::disk('public')->path($template->word_template_path);
        $templateProcessor = new TemplateProcessor($templatePath);

        // Example placeholder data
        $variables = [
            'company_number' => 'MOU/UMPSA/2025/001',
            'company_shortname' => 'TMJ',
            'signed_behalf_name' => 'Dato\' Ahmad bin Ibrahim',
            'signed_behalf_position' => 'Chief Executive Officer',
            'witness_name' => 'Encik Mohd Hafiz bin Osman',
            'witness_position' => 'General Manager',
            'liaison_officer' => 'Encik Ahmad bin Ali',
            'vc_name' => 'Professor Dr. Yatimah Alias',
            'dvc_name' => 'Professor Dato Ir. Ts. Dr. Ahmad Ziad Sulaiman',
            'company_name' => 'Syarikat Teknologi Maju Sdn Bhd',
            'hr_name' => 'Puan Siti Aminah binti Hassan',
            'hr_phone' => '09-5551234',
            'hr_email' => 'hr@teknologimaju.com.my',
            'company_address' => 'No. 123, Jalan Teknologi 1, Taman Perindustrian Gebeng, 26080 Kuantan, Pahang',
            'current_date' => now()->format('d F Y'),
        ];

        // Set normal variables
        foreach ($variables as $key => $value) {
            $templateProcessor->setValue($key, $value);
        }

        // Set uppercase versions
        foreach ($variables as $key => $value) {
            $templateProcessor->setValue($key.':upper', strtoupper($value));
        }

        // Save to temp file
        $outputPath = storage_path('app/temp/MOU_Preview_'.time().'.docx');

        if (! file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $templateProcessor->saveAs($outputPath);

        return $outputPath;
    }

    /**
     * Delete director signature.
     */
    public function deleteDirectorSignature(): RedirectResponse
    {
        $template = DocumentTemplate::getSalTemplate();
        $settings = $template->settings ?? [];

        if (isset($settings['director_signature_path'])) {
            Storage::disk('public')->delete($settings['director_signature_path']);
            unset($settings['director_signature_path']);
            $template->update([
                'settings' => $settings,
                'updated_by' => auth()->id(),
            ]);
        }

        return redirect()->route('admin.documents.sal')->with('success', 'Director signature deleted successfully.');
    }

    /**
     * Display Word template upload page for SAL.
     */
    public function wordTemplateSal(): View
    {
        $template = DocumentTemplate::getSalTemplate();
        $variables = DocumentTemplate::getSalVariables();

        return view('admin.documents.sal-word-template', [
            'template' => $template,
            'variables' => $variables,
        ]);
    }

    /**
     * Upload Word template for SAL.
     */
    public function uploadWordTemplate(Request $request)
    {
        $request->validate([
            'word_template' => ['required', 'file', 'mimes:docx', 'max:10240'], // Max 10MB
        ]);

        $template = DocumentTemplate::getSalTemplate();

        // Delete old template if exists
        if ($template->word_template_path) {
            Storage::disk('public')->delete($template->word_template_path);
        }

        // Store new template
        $file = $request->file('word_template');
        $originalName = $file->getClientOriginalName();
        $path = $file->store('document-templates', 'public');

        $template->update([
            'word_template_path' => $path,
            'word_template_original_name' => $originalName,
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('admin.documents.sal.word-template')
            ->with('success', 'Word template uploaded successfully.');
    }

    /**
     * Delete Word template for SAL.
     */
    public function deleteWordTemplate()
    {
        $template = DocumentTemplate::getSalTemplate();

        if ($template->word_template_path) {
            Storage::disk('public')->delete($template->word_template_path);
            $template->update([
                'word_template_path' => null,
                'word_template_original_name' => null,
                'template_mode' => 'canvas', // Revert to canvas mode
                'updated_by' => auth()->id(),
            ]);
        }

        return redirect()->route('admin.documents.sal.word-template')
            ->with('success', 'Word template deleted successfully.');
    }

    /**
     * Set template mode (canvas or word).
     */
    public function setTemplateMode(Request $request)
    {
        $request->validate([
            'mode' => ['required', 'in:canvas,word'],
        ]);

        $template = DocumentTemplate::getSalTemplate();

        // Check if Word template exists when switching to word mode
        if ($request->mode === 'word' && ! $template->word_template_path) {
            return back()->with('error', 'Please upload a Word template first.');
        }

        $template->update([
            'template_mode' => $request->mode,
            'updated_by' => auth()->id(),
        ]);

        return back()->with('success', 'Template mode changed to '.ucfirst($request->mode).'.');
    }

    /**
     * Preview SAL from Word template (converts to PDF for browser viewing).
     */
    public function previewWordTemplate()
    {
        $template = DocumentTemplate::getSalTemplate();

        if (! $template->word_template_path) {
            return back()->with('error', 'No Word template uploaded.');
        }

        $student = Student::with(['user', 'group', 'company'])->first();
        if (! $student) {
            return back()->with('error', 'No student found for preview.');
        }

        // Generate Word document with sample data
        $docxPath = $this->generateWordDocument($template, $student);

        // Convert Word to PDF for browser preview
        $pdfPath = $this->convertWordToPdf($docxPath);

        // Clean up the temporary docx file
        if (file_exists($docxPath)) {
            unlink($docxPath);
        }

        // Stream the PDF in browser
        return response()->file($pdfPath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="SAL_Preview.pdf"',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Preview SAL from Word template (convert to PDF and display in browser).
     */
    public function previewWordTemplateDocx()
    {
        $template = DocumentTemplate::getSalTemplate();

        if (! $template->word_template_path) {
            return back()->with('error', 'No Word template uploaded.');
        }

        // Generate Word document with example placeholder data
        $docxPath = $this->generateSalWithExampleData($template);

        // Convert Word to PDF using LibreOffice
        $pdfPath = $this->convertWordToPdfLibreOffice($docxPath);

        // Clean up Word file
        if (file_exists($docxPath)) {
            unlink($docxPath);
        }

        // Stream PDF inline (preview in browser)
        return response()->file($pdfPath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="SAL_Preview.pdf"',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Convert Word document to PDF using LibreOffice.
     */
    protected function convertWordToPdfLibreOffice(string $docxPath): string
    {
        $outputDir = dirname($docxPath);
        $pdfPath = str_replace('.docx', '.pdf', $docxPath);

        // Try to find LibreOffice executable
        $libreOfficePaths = [
            '/Applications/LibreOffice.app/Contents/MacOS/soffice', // macOS
            '/usr/bin/libreoffice', // Linux
            '/usr/bin/soffice', // Linux alternative
            'C:\\Program Files\\LibreOffice\\program\\soffice.exe', // Windows
        ];

        $libreOfficePath = null;
        foreach ($libreOfficePaths as $path) {
            if (file_exists($path)) {
                $libreOfficePath = $path;
                break;
            }
        }

        if (! $libreOfficePath) {
            // Fallback to PhpWord PDF conversion
            return $this->convertWordToPdfFallback($docxPath);
        }

        // Use LibreOffice for accurate conversion
        $command = sprintf(
            '"%s" --headless --convert-to pdf --outdir "%s" "%s" 2>&1',
            $libreOfficePath,
            $outputDir,
            $docxPath
        );

        exec($command, $output, $returnCode);

        // Check if PDF was created
        if (file_exists($pdfPath)) {
            return $pdfPath;
        }

        // If LibreOffice failed, try fallback
        return $this->convertWordToPdfFallback($docxPath);
    }

    /**
     * Fallback Word to PDF conversion using PhpWord (less accurate).
     */
    protected function convertWordToPdfFallback(string $docxPath): string
    {
        // Set PDF renderer to DomPDF
        $domPdfPath = base_path('vendor/dompdf/dompdf');
        Settings::setPdfRendererPath($domPdfPath);
        Settings::setPdfRendererName('DomPDF');

        // Load the Word document
        $phpWord = IOFactory::load($docxPath);

        // Generate PDF path
        $pdfPath = str_replace('.docx', '.pdf', $docxPath);

        // Save as PDF
        $pdfWriter = IOFactory::createWriter($phpWord, 'PDF');
        $pdfWriter->save($pdfPath);

        return $pdfPath;
    }

    /**
     * Download Word template (original uploaded file).
     */
    public function downloadWordTemplate()
    {
        $template = DocumentTemplate::getSalTemplate();

        if (! $template->word_template_path) {
            return back()->with('error', 'No Word template uploaded.');
        }

        $path = Storage::disk('public')->path($template->word_template_path);
        $filename = $template->word_template_original_name ?? 'SAL_Template.docx';

        return response()->download($path, $filename);
    }

    /**
     * Generate Word document from template with student data.
     */
    protected function generateWordDocument(DocumentTemplate $template, Student $student): string
    {
        $templatePath = Storage::disk('public')->path($template->word_template_path);
        $templateProcessor = new TemplateProcessor($templatePath);

        // Calculate WBL duration
        $wblDuration = '6 months';
        $groupStartDate = null;
        $groupEndDate = null;

        if ($student->group) {
            $groupStartDate = $student->group->start_date;
            $groupEndDate = $student->group->end_date;

            if ($groupStartDate && $groupEndDate) {
                $start = \Carbon\Carbon::parse($groupStartDate);
                $end = \Carbon\Carbon::parse($groupEndDate);
                $months = $start->diffInMonths($end);
                $wblDuration = $months.' '.($months == 1 ? 'month' : 'months');
            }
        }

        // Get SAL release date and reference number from settings
        $salReleaseDate = $template->settings['sal_release_date'] ?? now()->format('Y-m-d');
        $salReferenceNumber = $template->settings['sal_reference_number'] ?? '';

        // Get WBL Coordinator based on student's programme
        $wblCoordinator = Student::getWblCoordinator($student->programme);

        // Get company information if student has a company assigned
        $company = $student->company;

        // Replace variables using ${variable} format
        $variables = [
            'student_name' => $student->name ?? $student->user?->name ?? '',
            'student_matric' => $student->matric_no ?? '',
            'student_ic' => $student->ic_no ?? '',
            'student_faculty' => $student->faculty ?? 'Faculty of Technology and Management',
            'student_programme' => $student->programme ?? '',
            'student_programme_short' => Student::getProgrammeShortCode($student->programme),
            'student_email' => $student->user?->email ?? '',
            'student_phone' => $student->phone ?? '',
            'wbl_duration' => $wblDuration,
            'current_date' => now()->format('d F Y'),
            'group_name' => $student->group?->name ?? '',
            'group_start_date' => $groupStartDate ? \Carbon\Carbon::parse($groupStartDate)->format('d F Y') : '',
            'group_end_date' => $groupEndDate ? \Carbon\Carbon::parse($groupEndDate)->format('d F Y') : '',
            'sal_release_date' => $salReleaseDate ? \Carbon\Carbon::parse($salReleaseDate)->format('d F Y') : '',
            'sal_reference_number' => $salReferenceNumber,
            'wbl_coordinator_name' => $wblCoordinator?->name ?? '',
            'wbl_coordinator_email' => $wblCoordinator?->email ?? '',
            'wbl_coordinator_phone' => $wblCoordinator?->phone ?? '',
            'director_name' => $template->settings['director_name'] ?? '',
            'company_name' => $company?->company_name ?? '',
            'company_address' => $company?->address ?? '',
            'company_pic_name' => $company?->pic_name ?? '',
            'company_pic_position' => $company?->position ?? '',
            'company_email' => $company?->email ?? '',
            'company_phone' => $company?->phone ?? '',
        ];

        // Set normal variables
        foreach ($variables as $key => $value) {
            $templateProcessor->setValue($key, $value);
        }

        // Set uppercase versions (with :upper suffix)
        foreach ($variables as $key => $value) {
            $templateProcessor->setValue($key.':upper', strtoupper($value));
        }

        // Set director signature image if exists
        if (isset($template->settings['director_signature_path'])) {
            $signaturePath = Storage::disk('public')->path($template->settings['director_signature_path']);
            if (file_exists($signaturePath)) {
                try {
                    $templateProcessor->setImageValue('director_signature', [
                        'path' => $signaturePath,
                        'width' => 150,
                        'height' => 50,
                        'ratio' => true,
                    ]);
                } catch (\Exception $e) {
                    // If image replacement fails, just remove the placeholder
                    $templateProcessor->setValue('director_signature', '');
                }
            } else {
                $templateProcessor->setValue('director_signature', '');
            }
        } else {
            $templateProcessor->setValue('director_signature', '');
        }

        // Save to temp file
        $outputPath = storage_path('app/temp/SAL_'.time().'.docx');

        // Ensure temp directory exists
        if (! file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $templateProcessor->saveAs($outputPath);

        return $outputPath;
    }

    /**
     * Get available Word template variables formatted for display.
     */
    public static function getWordTemplateVariables(): array
    {
        return [
            '${student_name}' => 'Student\'s full name',
            '${student_matric}' => 'Student\'s matric number',
            '${student_ic}' => 'Student\'s IC number',
            '${student_faculty}' => 'Student\'s faculty',
            '${student_programme}' => 'Student\'s programme',
            '${student_programme_short}' => 'Programme short code (BTA/BTD/BTG)',
            '${student_email}' => 'Student\'s email',
            '${student_phone}' => 'Student\'s phone number',
            '${wbl_duration}' => 'WBL training duration',
            '${current_date}' => 'Current date',
            '${group_name}' => 'WBL group name',
            '${group_start_date}' => 'Group start date',
            '${group_end_date}' => 'Group end date',
            '${sal_release_date}' => 'SAL release/issue date',
            '${sal_reference_number}' => 'SAL reference number',
            '${wbl_coordinator_name}' => 'WBL Coordinator name (by programme)',
            '${wbl_coordinator_email}' => 'WBL Coordinator email',
            '${wbl_coordinator_phone}' => 'WBL Coordinator phone number',
            '${director_name}' => 'Director of UMPSA Career Centre',
            '${director_signature}' => 'Director signature (image)',
            '${company_name}' => 'Company name',
            '${company_address}' => 'Company address',
            '${company_pic_name}' => 'Company PIC name',
            '${company_pic_position}' => 'Company PIC position',
            '${company_email}' => 'Company email',
            '${company_phone}' => 'Company phone',
        ];
    }

    /**
     * Update SCL template settings.
     */
    public function updateScl(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'scl_release_date' => ['nullable', 'date'],
            'scl_reference_number' => ['nullable', 'string', 'max:255'],
            'scl_director_name' => ['nullable', 'string', 'max:255'],
            'scl_director_signature' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
        ]);

        $template = DocumentTemplate::getSclTemplate();
        $settings = $template->settings ?? [];

        // Store SCL settings - always update from validated form data
        if (array_key_exists('scl_release_date', $validated)) {
            $settings['scl_release_date'] = $validated['scl_release_date'];
        }
        if (array_key_exists('scl_reference_number', $validated)) {
            $settings['scl_reference_number'] = $validated['scl_reference_number'];
        }
        if (array_key_exists('scl_director_name', $validated)) {
            $settings['scl_director_name'] = $validated['scl_director_name'];
        }

        // Handle director signature upload
        if ($request->hasFile('scl_director_signature')) {
            // Delete old signature if exists
            if (isset($settings['scl_director_signature_path'])) {
                Storage::disk('public')->delete($settings['scl_director_signature_path']);
            }

            // Store new signature
            $signaturePath = $request->file('scl_director_signature')->store('document-templates/signatures', 'public');
            $settings['scl_director_signature_path'] = $signaturePath;
        }

        $template->update([
            'settings' => $settings,
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('admin.documents.scl')->with('success', 'SCL settings saved successfully.');
    }

    /**
     * Delete SCL director signature.
     */
    public function deleteSclDirectorSignature(): RedirectResponse
    {
        $template = DocumentTemplate::getSclTemplate();
        $settings = $template->settings ?? [];

        if (isset($settings['scl_director_signature_path'])) {
            Storage::disk('public')->delete($settings['scl_director_signature_path']);
            unset($settings['scl_director_signature_path']);
            $template->update([
                'settings' => $settings,
                'updated_by' => auth()->id(),
            ]);
        }

        return redirect()->route('admin.documents.scl')->with('success', 'Director signature deleted successfully.');
    }

    /**
     * Display Word template upload page for SCL.
     */
    public function wordTemplateScl(): View
    {
        $template = DocumentTemplate::getSclTemplate();
        $variables = self::getSclWordTemplateVariables();

        return view('admin.documents.scl-word-template', [
            'template' => $template,
            'variables' => $variables,
        ]);
    }

    /**
     * Upload Word template for SCL.
     */
    public function uploadSclWordTemplate(Request $request): RedirectResponse
    {
        $request->validate([
            'word_template' => ['required', 'file', 'mimes:docx', 'max:10240'],
        ]);

        $template = DocumentTemplate::getSclTemplate();

        // Delete old template if exists
        if ($template->word_template_path) {
            Storage::disk('public')->delete($template->word_template_path);
        }

        // Store new template
        $file = $request->file('word_template');
        $originalName = $file->getClientOriginalName();
        $path = $file->store('document-templates/scl', 'public');

        $template->update([
            'word_template_path' => $path,
            'word_template_original_name' => $originalName,
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('admin.documents.scl.word-template')
            ->with('success', 'Word template uploaded successfully.');
    }

    /**
     * Delete Word template for SCL.
     */
    public function deleteSclWordTemplate(): RedirectResponse
    {
        $template = DocumentTemplate::getSclTemplate();

        if ($template->word_template_path) {
            Storage::disk('public')->delete($template->word_template_path);
            $template->update([
                'word_template_path' => null,
                'word_template_original_name' => null,
                'updated_by' => auth()->id(),
            ]);
        }

        return redirect()->route('admin.documents.scl.word-template')
            ->with('success', 'Word template deleted successfully.');
    }

    /**
     * Preview SCL Word template with sample data (convert to PDF and display in browser).
     */
    public function previewSclWordTemplateDocx()
    {
        $template = DocumentTemplate::getSclTemplate();

        if (! $template->word_template_path || ! Storage::disk('public')->exists($template->word_template_path)) {
            return redirect()->route('admin.documents.scl.word-template')
                ->with('error', 'No Word template uploaded. Please upload a template first.');
        }

        // Generate Word document with example placeholder data
        $docxPath = $this->generateSclWithExampleData($template);

        // Convert Word to PDF using LibreOffice
        $pdfPath = $this->convertWordToPdfLibreOffice($docxPath);

        // Clean up Word file
        if (file_exists($docxPath)) {
            unlink($docxPath);
        }

        // Stream PDF inline (preview in browser)
        return response()->file($pdfPath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="SCL_Preview.pdf"',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Download SCL Word template.
     */
    public function downloadSclWordTemplate()
    {
        $template = DocumentTemplate::getSclTemplate();

        if (! $template->word_template_path || ! Storage::disk('public')->exists($template->word_template_path)) {
            return redirect()->route('admin.documents.scl.word-template')
                ->with('error', 'No Word template uploaded.');
        }

        return Storage::disk('public')->download(
            $template->word_template_path,
            $template->word_template_original_name ?? 'SCL_Template.docx'
        );
    }

    /**
     * Generate SCL from Word template.
     */
    private function generateSclFromWordTemplate(Student $student, DocumentTemplate $template): string
    {
        $templatePath = Storage::disk('public')->path($template->word_template_path);
        $templateProcessor = new TemplateProcessor($templatePath);

        // Get dates
        $groupStartDate = $student->group?->start_date;
        $groupEndDate = $student->group?->end_date;
        $acceptedDate = $student->placementTracking?->accepted_at;

        // Get settings
        $sclReleaseDate = $template->settings['scl_release_date'] ?? now()->format('Y-m-d');
        $sclReferenceNumber = $template->settings['scl_reference_number'] ?? '';

        // Build variables
        $variables = [
            // Student Info
            'student_name' => $student->name ?? $student->user?->name ?? '',
            'student_matric' => $student->matric_no ?? '',
            'student_ic' => $student->ic_no ?? '',
            'student_programme' => $student->programme ?? '',
            'student_programme_short' => Student::getProgrammeShortCode($student->programme),
            // Company Info
            'company_name' => $student->company?->company_name ?? '',
            'company_address' => $student->company?->address ?? '',
            'hr_name' => $student->company?->pic_name ?? '',
            'hr_position' => $student->company?->position ?? '',
            'company_email' => $student->company?->email ?? '',
            'company_phone' => $student->company?->phone ?? '',
            // Dates
            'group_start_date' => $groupStartDate ? \Carbon\Carbon::parse($groupStartDate)->format('d F Y') : '',
            'group_end_date' => $groupEndDate ? \Carbon\Carbon::parse($groupEndDate)->format('d F Y') : '',
            'accepted_date' => $acceptedDate ? \Carbon\Carbon::parse($acceptedDate)->format('d F Y') : '',
            'current_date' => now()->format('d F Y'),
            'scl_release_date' => $sclReleaseDate ? \Carbon\Carbon::parse($sclReleaseDate)->format('d F Y') : '',
            'scl_reference_number' => $sclReferenceNumber,
            // Supervisors
            'academic_tutor_name' => $student->academicTutor?->name ?? '',
            'academic_tutor_email' => $student->academicTutor?->email ?? '',
            'academic_tutor_phone' => $student->academicTutor?->phone ?? '',
            'industry_coach_name' => $student->industryCoach?->name ?? '',
            'industry_coach_email' => $student->industryCoach?->email ?? '',
            'industry_coach_phone' => $student->industryCoach?->phone ?? '',
            // Director
            'director_name' => $template->settings['scl_director_name'] ?? '',
        ];

        // Set normal variables
        foreach ($variables as $key => $value) {
            $templateProcessor->setValue($key, $value);
        }

        // Set uppercase versions
        foreach ($variables as $key => $value) {
            $templateProcessor->setValue($key.':upper', strtoupper($value));
        }

        // Set director signature image if exists
        if (isset($template->settings['scl_director_signature_path'])) {
            $signaturePath = Storage::disk('public')->path($template->settings['scl_director_signature_path']);
            if (file_exists($signaturePath)) {
                try {
                    $templateProcessor->setImageValue('director_signature', [
                        'path' => $signaturePath,
                        'width' => 150,
                        'height' => 50,
                        'ratio' => true,
                    ]);
                } catch (\Exception $e) {
                    $templateProcessor->setValue('director_signature', '');
                }
            } else {
                $templateProcessor->setValue('director_signature', '');
            }
        } else {
            $templateProcessor->setValue('director_signature', '');
        }

        // Save to temp file
        $outputPath = storage_path('app/temp/SCL_'.time().'.docx');

        // Ensure temp directory exists
        if (! file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $templateProcessor->saveAs($outputPath);

        return $outputPath;
    }

    /**
     * Get available SCL Word template variables formatted for display.
     */
    public static function getSclWordTemplateVariables(): array
    {
        return [
            // Student Info
            '${student_name}' => 'Student\'s full name',
            '${student_matric}' => 'Student\'s matric number',
            '${student_ic}' => 'Student\'s IC number',
            '${student_programme}' => 'Student\'s programme',
            '${student_programme_short}' => 'Programme short code (BTA/BTD/BTG)',
            // Company Info
            '${company_name}' => 'Company name (accepted offer)',
            '${company_address}' => 'Company address',
            '${hr_name}' => 'HR/PIC name',
            '${hr_position}' => 'HR/PIC position',
            '${company_email}' => 'Company email',
            '${company_phone}' => 'Company phone',
            // Dates
            '${group_start_date}' => 'WBL start date',
            '${group_end_date}' => 'WBL end date',
            '${accepted_date}' => 'Offer accepted date',
            '${current_date}' => 'Current date',
            '${scl_release_date}' => 'SCL release/issue date',
            '${scl_reference_number}' => 'SCL reference number',
            // Supervisors
            '${academic_tutor_name}' => 'Academic Tutor (AT) name',
            '${academic_tutor_email}' => 'Academic Tutor email',
            '${academic_tutor_phone}' => 'Academic Tutor phone',
            '${industry_coach_name}' => 'Industry Coach (IC) name',
            '${industry_coach_email}' => 'Industry Coach email',
            '${industry_coach_phone}' => 'Industry Coach phone',
            // Director
            '${director_name}' => 'Director of UMPSA Career Centre',
            '${director_signature}' => 'Director signature (image)',
        ];
    }

    /**
     * Generate SAL Word document with example placeholder data for preview.
     */
    protected function generateSalWithExampleData(DocumentTemplate $template): string
    {
        $templatePath = Storage::disk('public')->path($template->word_template_path);
        $templateProcessor = new TemplateProcessor($templatePath);

        // Get settings values (these are real values from the template)
        $salReleaseDate = $template->settings['sal_release_date'] ?? now()->format('Y-m-d');
        $salReferenceNumber = $template->settings['sal_reference_number'] ?? 'UMPSA/PKU/SAL/2025/001';

        // Example placeholder data
        $variables = [
            'student_name' => 'AHMAD BIN ABDULLAH',
            'student_matric' => 'TM210001',
            'student_ic' => '010101-01-0001',
            'student_faculty' => 'Faculty of Technology and Management',
            'student_programme' => 'Bachelor of Technology Management (Innovation Technology)',
            'student_programme_short' => 'BTD',
            'student_email' => 'tm210001@student.umpsa.edu.my',
            'student_phone' => '012-3456789',
            'wbl_duration' => '6 months',
            'current_date' => now()->format('d F Y'),
            'group_name' => 'WBL 2025/2026 Semester 1',
            'group_start_date' => '01 March 2025',
            'group_end_date' => '31 August 2025',
            'sal_release_date' => $salReleaseDate ? \Carbon\Carbon::parse($salReleaseDate)->format('d F Y') : now()->format('d F Y'),
            'sal_reference_number' => $salReferenceNumber ?: 'UMPSA/PKU/SAL/2025/001',
            'wbl_coordinator_name' => 'Dr. Siti Aminah binti Hassan',
            'wbl_coordinator_email' => 'sitiaminah@umpsa.edu.my',
            'wbl_coordinator_phone' => '09-5492000',
            'director_name' => $template->settings['director_name'] ?? 'Prof. Dr. Mohd Razali bin Muhamad',
            'company_name' => 'Syarikat Teknologi Maju Sdn Bhd',
            'company_address' => 'No. 123, Jalan Teknologi 1, Taman Perindustrian Gebeng, 26080 Kuantan, Pahang',
            'company_pic_name' => 'Encik Kamal bin Ismail',
            'company_pic_position' => 'Human Resource Manager',
            'company_email' => 'hr@teknologimaju.com.my',
            'company_phone' => '09-5551234',
        ];

        // Set normal variables
        foreach ($variables as $key => $value) {
            $templateProcessor->setValue($key, $value);
        }

        // Set uppercase versions (with :upper suffix)
        foreach ($variables as $key => $value) {
            $templateProcessor->setValue($key.':upper', strtoupper($value));
        }

        // Set director signature image if exists
        if (isset($template->settings['director_signature_path'])) {
            $signaturePath = Storage::disk('public')->path($template->settings['director_signature_path']);
            if (file_exists($signaturePath)) {
                try {
                    $templateProcessor->setImageValue('director_signature', [
                        'path' => $signaturePath,
                        'width' => 150,
                        'height' => 50,
                        'ratio' => true,
                    ]);
                } catch (\Exception $e) {
                    $templateProcessor->setValue('director_signature', '');
                }
            } else {
                $templateProcessor->setValue('director_signature', '');
            }
        } else {
            $templateProcessor->setValue('director_signature', '');
        }

        // Save to temp file
        $outputPath = storage_path('app/temp/SAL_Preview_'.time().'.docx');

        // Ensure temp directory exists
        if (! file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $templateProcessor->saveAs($outputPath);

        return $outputPath;
    }

    /**
     * Toggle SCL auto-release setting.
     */
    public function toggleSclAutoRelease(): RedirectResponse
    {
        $template = DocumentTemplate::getSclTemplate();
        $settings = $template->settings ?? [];

        // Toggle the setting
        $isEnabled = ! ($settings['scl_auto_release_enabled'] ?? false);
        $settings['scl_auto_release_enabled'] = $isEnabled;

        if ($isEnabled) {
            $settings['scl_auto_release_enabled_at'] = now()->toIso8601String();
            $settings['scl_auto_release_enabled_by'] = auth()->id();
        } else {
            $settings['scl_auto_release_disabled_at'] = now()->toIso8601String();
            $settings['scl_auto_release_disabled_by'] = auth()->id();
        }

        $template->update([
            'settings' => $settings,
            'updated_by' => auth()->id(),
        ]);

        $message = $isEnabled
            ? 'SCL auto-release enabled. Students will automatically receive SCL when they get offer letters.'
            : 'SCL auto-release disabled.';

        return redirect()->route('admin.documents.scl')->with('success', $message);
    }

    /**
     * Toggle SAL auto-release setting.
     */
    public function toggleSalAutoRelease(): RedirectResponse
    {
        $template = DocumentTemplate::getSalTemplate();
        $settings = $template->settings ?? [];

        // Toggle the setting
        $isEnabled = ! ($settings['sal_auto_release_enabled'] ?? false);
        $settings['sal_auto_release_enabled'] = $isEnabled;

        if ($isEnabled) {
            $settings['sal_auto_release_enabled_at'] = now()->toIso8601String();
            $settings['sal_auto_release_enabled_by'] = auth()->id();
        } else {
            $settings['sal_auto_release_disabled_at'] = now()->toIso8601String();
            $settings['sal_auto_release_disabled_by'] = auth()->id();
        }

        $template->update([
            'settings' => $settings,
            'updated_by' => auth()->id(),
        ]);

        $message = $isEnabled
            ? 'SAL auto-release enabled. Students will automatically receive SAL when they are assigned to a group.'
            : 'SAL auto-release disabled.';

        return redirect()->route('admin.documents.sal')->with('success', $message);
    }

    /**
     * Generate SCL Word document with example placeholder data for preview.
     */
    protected function generateSclWithExampleData(DocumentTemplate $template): string
    {
        $templatePath = Storage::disk('public')->path($template->word_template_path);
        $templateProcessor = new TemplateProcessor($templatePath);

        // Get settings values (these are real values from the template)
        $sclReleaseDate = $template->settings['scl_release_date'] ?? now()->format('Y-m-d');
        $sclReferenceNumber = $template->settings['scl_reference_number'] ?? 'UMPSA/PKU/SCL/2025/001';

        // Example placeholder data
        $variables = [
            // Student Info
            'student_name' => 'AHMAD BIN ABDULLAH',
            'student_matric' => 'TM210001',
            'student_ic' => '010101-01-0001',
            'student_programme' => 'Bachelor of Technology Management (Innovation Technology)',
            'student_programme_short' => 'BTD',
            // Company Info
            'company_name' => 'Syarikat Teknologi Maju Sdn Bhd',
            'company_address' => 'No. 123, Jalan Teknologi 1, Taman Perindustrian Gebeng, 26080 Kuantan, Pahang',
            'hr_name' => 'Encik Kamal bin Ismail',
            'hr_position' => 'Human Resource Manager',
            'company_email' => 'hr@teknologimaju.com.my',
            'company_phone' => '09-5551234',
            // Dates
            'group_start_date' => '01 March 2025',
            'group_end_date' => '31 August 2025',
            'accepted_date' => '15 January 2025',
            'current_date' => now()->format('d F Y'),
            'scl_release_date' => $sclReleaseDate ? \Carbon\Carbon::parse($sclReleaseDate)->format('d F Y') : now()->format('d F Y'),
            'scl_reference_number' => $sclReferenceNumber ?: 'UMPSA/PKU/SCL/2025/001',
            // Supervisors
            'academic_tutor_name' => 'Dr. Siti Aminah binti Hassan',
            'academic_tutor_email' => 'sitiaminah@umpsa.edu.my',
            'academic_tutor_phone' => '09-5492000',
            'industry_coach_name' => 'Encik Hafiz bin Osman',
            'industry_coach_email' => 'hafiz@teknologimaju.com.my',
            'industry_coach_phone' => '012-9876543',
            // Director
            'director_name' => $template->settings['scl_director_name'] ?? 'Prof. Dr. Mohd Razali bin Muhamad',
        ];

        // Set normal variables
        foreach ($variables as $key => $value) {
            $templateProcessor->setValue($key, $value);
        }

        // Set uppercase versions
        foreach ($variables as $key => $value) {
            $templateProcessor->setValue($key.':upper', strtoupper($value));
        }

        // Set director signature image if exists
        if (isset($template->settings['scl_director_signature_path'])) {
            $signaturePath = Storage::disk('public')->path($template->settings['scl_director_signature_path']);
            if (file_exists($signaturePath)) {
                try {
                    $templateProcessor->setImageValue('director_signature', [
                        'path' => $signaturePath,
                        'width' => 150,
                        'height' => 50,
                        'ratio' => true,
                    ]);
                } catch (\Exception $e) {
                    $templateProcessor->setValue('director_signature', '');
                }
            } else {
                $templateProcessor->setValue('director_signature', '');
            }
        } else {
            $templateProcessor->setValue('director_signature', '');
        }

        // Save to temp file
        $outputPath = storage_path('app/temp/SCL_Preview_'.time().'.docx');

        // Ensure temp directory exists
        if (! file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $templateProcessor->saveAs($outputPath);

        return $outputPath;
    }
}
