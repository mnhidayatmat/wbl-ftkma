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
        if ($request->has('sal_release_date')) {
            $settings['sal_release_date'] = $validated['sal_release_date'];
        }
        if ($request->has('sal_reference_number')) {
            $settings['sal_reference_number'] = $validated['sal_reference_number'];
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
        return view('admin.documents.mou');
    }

    /**
     * Preview MoU PDF with sample data.
     */
    public function previewMou()
    {
        $templatePath = resource_path('views/admin/documents/pdf/mou.blade.php');
        if (! file_exists($templatePath)) {
            return back()->with('error', 'MoU template not configured yet.');
        }

        $pdf = Pdf::loadView('admin.documents.pdf.mou', [
            'generatedAt' => now(),
        ])->setPaper('a4', 'portrait');

        return $pdf->stream('MoU_Template_Preview.pdf');
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

        $student = Student::with(['user', 'group', 'company'])->first();
        if (! $student) {
            return back()->with('error', 'No student found for preview.');
        }

        // Generate Word document with sample data
        $docxPath = $this->generateWordDocument($template, $student);

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

        // Replace variables using ${variable} format
        $variables = [
            'student_name' => $student->name ?? $student->user?->name ?? '',
            'student_matric' => $student->matric_no ?? '',
            'student_ic' => $student->ic_no ?? '',
            'student_faculty' => $student->faculty ?? 'Faculty of Technology and Management',
            'student_programme' => $student->programme ?? '',
            'student_email' => $student->user?->email ?? '',
            'student_phone' => $student->phone ?? '',
            'wbl_duration' => $wblDuration,
            'current_date' => now()->format('d F Y'),
            'group_name' => $student->group?->name ?? '',
            'group_start_date' => $groupStartDate ? \Carbon\Carbon::parse($groupStartDate)->format('d F Y') : '',
            'group_end_date' => $groupEndDate ? \Carbon\Carbon::parse($groupEndDate)->format('d F Y') : '',
            'sal_release_date' => $salReleaseDate ? \Carbon\Carbon::parse($salReleaseDate)->format('d F Y') : '',
            'sal_reference_number' => $salReferenceNumber,
        ];

        // Set normal variables
        foreach ($variables as $key => $value) {
            $templateProcessor->setValue($key, $value);
        }

        // Set uppercase versions (with :upper suffix)
        foreach ($variables as $key => $value) {
            $templateProcessor->setValue($key.':upper', strtoupper($value));
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
            '${student_email}' => 'Student\'s email',
            '${student_phone}' => 'Student\'s phone number',
            '${wbl_duration}' => 'WBL training duration',
            '${current_date}' => 'Current date',
            '${group_name}' => 'WBL group name',
            '${group_start_date}' => 'Group start date',
            '${group_end_date}' => 'Group end date',
            '${sal_release_date}' => 'SAL release/issue date',
            '${sal_reference_number}' => 'SAL reference number',
        ];
    }
}
