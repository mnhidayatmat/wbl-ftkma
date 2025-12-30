<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentTemplate;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DocumentTemplateController extends Controller
{
    /**
     * Display SAL template page.
     */
    public function sal(): View
    {
        $template = DocumentTemplate::getSalTemplate();
        $sampleStudent = Student::first();
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
        if (isset($validated['settings'])) {
            $settings = $validated['settings'];
            // For form submissions, process checkboxes
            if (! $request->expectsJson()) {
                $settings['show_logo'] = $request->has('settings.show_logo');
                $settings['show_date'] = $request->has('settings.show_date');
            }
            $updateData['settings'] = $settings;
        }

        $template->update($updateData);

        // Return JSON for AJAX requests
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Template saved successfully.',
            ]);
        }

        return redirect()->route('admin.documents.sal.edit')->with('success', 'SAL template updated successfully.');
    }

    /**
     * Preview SAL PDF with sample data.
     */
    public function previewSal()
    {
        $student = Student::first();
        if (! $student) {
            return back()->with('error', 'No student found for preview.');
        }

        $template = DocumentTemplate::getSalTemplate();
        $wblDuration = '6 months';

        $pdf = Pdf::loadView('placement.pdf.sal-template', [
            'student' => $student,
            'wblDuration' => $wblDuration,
            'generatedAt' => now(),
            'template' => $template,
        ])->setPaper('a4', 'portrait');

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
        ]);

        $template = DocumentTemplate::getSalTemplate();

        $template->update([
            'canvas_elements' => $validated['canvas_elements'],
            'canvas_width' => $validated['canvas_width'] ?? 595,
            'canvas_height' => $validated['canvas_height'] ?? 842,
            'updated_by' => auth()->id(),
        ]);

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
}
