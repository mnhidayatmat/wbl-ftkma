<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;

class StudentsErrorsExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    protected $invalidRows;

    public function __construct(array $invalidRows)
    {
        $this->invalidRows = $invalidRows;
    }

    /**
     * Return invalid rows with their error messages
     */
    public function array(): array
    {
        $data = [];

        foreach ($this->invalidRows as $row) {
            $rowData = $row['data'];
            $errors = implode('; ', $row['errors']);

            $data[] = [
                $rowData['name'] ?? '',
                $rowData['matric_no'] ?? '',
                $rowData['programme'] ?? '',
                $rowData['group'] ?? '',
                $rowData['company'] ?? '',
                $rowData['cgpa'] ?? '',
                $rowData['ic_number'] ?? '',
                $rowData['mobile_phone'] ?? '',
                $rowData['parent_name'] ?? '',
                $rowData['parent_phone_number'] ?? '',
                $rowData['next_of_kin'] ?? '',
                $rowData['next_of_kin_phone_number'] ?? '',
                $rowData['home_address'] ?? '',
                $rowData['background'] ?? '',
                $rowData['skills'] ?? '',
                $rowData['interests'] ?? '',
                $rowData['preferred_industry'] ?? '',
                $rowData['preferred_location'] ?? '',
                $errors, // Error column
            ];
        }

        return $data;
    }

    /**
     * Define the column headings
     */
    public function headings(): array
    {
        return [
            'name',
            'matric_no',
            'programme',
            'group',
            'company',
            'cgpa',
            'ic_number',
            'mobile_phone',
            'parent_name',
            'parent_phone_number',
            'next_of_kin',
            'next_of_kin_phone_number',
            'home_address',
            'background',
            'skills',
            'interests',
            'preferred_industry',
            'preferred_location',
            'errors', // Error column
        ];
    }

    /**
     * Apply styles to the worksheet
     */
    public function styles(Worksheet $sheet)
    {
        // Style the header row
        $sheet->getStyle('1:1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '003A6C'], // UMPSA primary color
            ],
        ]);

        // Style the error column header in red
        $sheet->getStyle('S1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'DC2626'], // Red color
            ],
        ]);

        return [];
    }

    /**
     * Define column widths
     */
    public function columnWidths(): array
    {
        return [
            'A' => 25, // name
            'B' => 15, // matric_no
            'C' => 35, // programme
            'D' => 12, // group
            'E' => 25, // company
            'F' => 8,  // cgpa
            'G' => 18, // ic_number
            'H' => 18, // mobile_phone
            'I' => 25, // parent_name
            'J' => 18, // parent_phone_number
            'K' => 25, // next_of_kin
            'L' => 18, // next_of_kin_phone_number
            'M' => 50, // home_address
            'N' => 40, // background
            'O' => 30, // skills
            'P' => 30, // interests
            'Q' => 20, // preferred_industry
            'R' => 20, // preferred_location
            'S' => 60, // errors
        ];
    }
}
