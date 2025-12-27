<?php

namespace App\Exports;

use App\Models\Student;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RecruitmentPoolExport implements FromCollection, WithColumnWidths, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $studentIds;

    public function __construct(array $studentIds)
    {
        $this->studentIds = $studentIds;
    }

    /**
     * @return Collection
     */
    public function collection()
    {
        return Student::with(['group', 'company', 'placementTracking', 'resumeInspection'])
            ->whereIn('id', $this->studentIds)
            ->orderBy('programme')
            ->orderBy('name')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Matric No',
            'Student Name',
            'Programme',
            'Group',
            'CGPA',
            'Skills',
            'Interests',
            'Preferred Industry',
            'Preferred Location',
            'Company',
            'Resume Status',
            'Placement Status',
            'Mobile Phone',
            'Email',
            'Academic Tutor',
            'Industry Coach',
        ];
    }

    /**
     * @param  mixed  $student
     */
    public function map($student): array
    {
        return [
            $student->matric_no ?? '',
            $student->name ?? '',
            $student->programme ?? '',
            $student->group->name ?? 'N/A',
            $student->cgpa ? number_format($student->cgpa, 2) : 'N/A',
            is_array($student->skills) ? implode(', ', $student->skills) : '',
            $student->interests ?? '',
            $student->preferred_industry ?? '',
            $student->preferred_location ?? '',
            $student->company->company_name ?? 'Not Assigned',
            $student->resumeInspection?->status ?? 'Not Submitted',
            $student->placementTracking?->status ?? 'Not Started',
            $student->mobile_phone ?? '',
            $student->user?->email ?? '',
            $student->academicTutor?->name ?? 'Not Assigned',
            $student->industryCoach?->name ?? 'Not Assigned',
        ];
    }

    public function title(): string
    {
        return 'Recruitment Pool';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15, // Matric No
            'B' => 25, // Student Name
            'C' => 15, // Programme
            'D' => 15, // Group
            'E' => 10, // CGPA
            'F' => 30, // Skills
            'G' => 30, // Interests
            'H' => 20, // Preferred Industry
            'I' => 20, // Preferred Location
            'J' => 25, // Company
            'K' => 20, // Resume Status
            'L' => 20, // Placement Status
            'M' => 15, // Mobile Phone
            'N' => 25, // Email
            'O' => 20, // Academic Tutor
            'P' => 20, // Industry Coach
        ];
    }

    /**
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Header row styling
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '003A6C'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }
}
