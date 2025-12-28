<?php

namespace App\Exports;

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

class CloAssessmentExport implements FromCollection, WithColumnWidths, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $students;

    protected $cloCodes;

    protected $courseCode;

    public function __construct(Collection $students, array $cloCodes, string $courseCode = 'FYP')
    {
        $this->students = $students;
        $this->cloCodes = $cloCodes;
        $this->courseCode = $courseCode;
    }

    public function collection()
    {
        return $this->students;
    }

    public function headings(): array
    {
        $headings = [
            'No.',
            'Student Name',
            'Matric No',
            'Group',
            'Company',
        ];

        // Add CLO columns
        foreach ($this->cloCodes as $clo) {
            $headings[] = $clo;
        }

        $headings[] = 'Total Score';
        $headings[] = 'Status';

        return $headings;
    }

    public function map($student): array
    {
        static $rowNumber = 0;
        $rowNumber++;

        $row = [
            $rowNumber,
            $student->name ?? '',
            $student->matric_no ?? '',
            $student->group->name ?? 'N/A',
            $student->company->company_name ?? 'N/A',
        ];

        // Add CLO scores
        $totalScore = 0;
        foreach ($this->cloCodes as $clo) {
            $score = $student->clo_scores[$clo] ?? 0;
            $row[] = number_format($score, 2);
            $totalScore += $score;
        }

        $row[] = number_format($totalScore, 2);
        $row[] = $student->overall_status_label ?? 'Not Started';

        return $row;
    }

    public function title(): string
    {
        return 'CLO Assessment - '.$this->courseCode;
    }

    public function columnWidths(): array
    {
        $widths = [
            'A' => 6,  // No.
            'B' => 25, // Student Name
            'C' => 15, // Matric No
            'D' => 15, // Group
            'E' => 25, // Company
        ];

        // Dynamic widths for CLO columns
        $col = 'F';
        foreach ($this->cloCodes as $clo) {
            $widths[$col] = 12;
            $col++;
        }

        // Total and Status columns
        $widths[$col] = 12;
        $col++;
        $widths[$col] = 15;

        return $widths;
    }

    public function styles(Worksheet $sheet)
    {
        $lastCol = chr(ord('E') + count($this->cloCodes) + 2);

        return [
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
                    'wrapText' => true,
                ],
            ],
        ];
    }
}
