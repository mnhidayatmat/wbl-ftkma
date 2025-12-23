<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Support\Collection;

class StudentPerformanceExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle
{
    protected $students;
    protected $courseCode;
    protected $lecturerWeight;
    protected $icWeight;

    public function __construct(Collection $students, string $courseCode = 'PPE', float $lecturerWeight = 40, float $icWeight = 60)
    {
        $this->students = $students;
        $this->courseCode = $courseCode;
        $this->lecturerWeight = $lecturerWeight;
        $this->icWeight = $icWeight;
    }

    /**
     * @return Collection
     */
    public function collection()
    {
        return $this->students;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Student Name',
            'Matric No',
            'Programme',
            'Group',
            'Lecturer Score (out of ' . number_format($this->lecturerWeight, 0) . '%)',
            'IC Score (out of ' . number_format($this->icWeight, 0) . '%)',
            'Final Score (out of 100%)',
            'Status',
            'Last Updated',
        ];
    }

    /**
     * @param mixed $student
     * @return array
     */
    public function map($student): array
    {
        return [
            $student->name ?? '',
            $student->matric_no ?? '',
            $student->programme ?? '',
            $student->group->name ?? '',
            number_format($student->lecturer_score ?? 0, 2) . '%',
            number_format($student->ic_score ?? 0, 2) . '%',
            number_format($student->final_score ?? 0, 2) . '%',
            $student->overall_status_label ?? 'Not Started',
            $student->last_updated ? $student->last_updated->format('Y-m-d H:i:s') : 'N/A',
        ];
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Student Performance – ' . $this->courseCode;
    }

    /**
     * @return array
     */
    public function columnWidths(): array
    {
        return [
            'A' => 25, // Student Name
            'B' => 15, // Matric No
            'C' => 15, // Programme
            'D' => 15, // Group
            'E' => 25, // Lecturer Score
            'F' => 20, // IC Score
            'G' => 20, // Final Score
            'H' => 15, // Status
            'I' => 20, // Last Updated
        ];
    }

    /**
     * @param Worksheet $sheet
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

