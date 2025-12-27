<?php

namespace App\Mail;

use App\Models\Company;
use App\Models\RecruitmentHandover;
use App\Exports\RecruitmentPoolExport;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use ZipArchive;

class RecruitmentPackageMail extends Mailable
{
    use Queueable, SerializesModels;

    public $students;
    public $company;
    public $customMessage;
    public $handover;
    public $includeExcel;
    public $includePdf;
    public $includeResumes;

    /**
     * Create a new message instance.
     */
    public function __construct(Collection $students, Company $company, ?string $customMessage, RecruitmentHandover $handover, Request $request)
    {
        $this->students = $students;
        $this->company = $company;
        $this->customMessage = $customMessage;
        $this->handover = $handover;
        $this->includeExcel = $request->boolean('include_excel', true);
        $this->includePdf = $request->boolean('include_pdf', true);
        $this->includeResumes = $request->boolean('include_resumes', true);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Student Recruitment Package - UMPSA WBL Programme',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.recruitment-package',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $attachments = [];

        $studentIds = $this->students->pluck('id')->toArray();

        // Excel attachment
        if ($this->includeExcel) {
            $excelPath = storage_path('app/temp/recruitment_' . uniqid() . '.xlsx');
            Excel::store(new RecruitmentPoolExport($studentIds), 'temp/recruitment_' . basename($excelPath));

            $attachments[] = Attachment::fromPath($excelPath)
                ->as('student_list.xlsx')
                ->withMime('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        }

        // PDF catalog attachment
        if ($this->includePdf) {
            $filters = $this->handover->filters_applied ?? [];
            $pdf = PDF::loadView('recruitment.exports.catalog', [
                'students' => $this->students,
                'filters' => $filters,
            ]);

            $pdfPath = storage_path('app/temp/catalog_' . uniqid() . '.pdf');
            $pdf->save($pdfPath);

            $attachments[] = Attachment::fromPath($pdfPath)
                ->as('student_catalog.pdf')
                ->withMime('application/pdf');
        }

        // Resumes ZIP attachment
        if ($this->includeResumes) {
            $studentsWithResumes = $this->students->filter(function ($student) {
                return $student->resume_pdf_path && file_exists(storage_path('app/' . $student->resume_pdf_path));
            });

            if ($studentsWithResumes->isNotEmpty()) {
                $zipPath = storage_path('app/temp/resumes_' . uniqid() . '.zip');

                $zip = new ZipArchive();
                if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
                    foreach ($studentsWithResumes as $student) {
                        $resumePath = storage_path('app/' . $student->resume_pdf_path);
                        $filename = $student->matric_no . '_' . str_replace(' ', '_', $student->name) . '_Resume.pdf';
                        $zip->addFile($resumePath, $filename);
                    }
                    $zip->close();

                    $attachments[] = Attachment::fromPath($zipPath)
                        ->as('student_resumes.zip')
                        ->withMime('application/zip');
                }
            }
        }

        return $attachments;
    }
}
