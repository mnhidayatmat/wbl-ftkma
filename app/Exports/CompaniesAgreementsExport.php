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
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CompaniesAgreementsExport implements FromCollection, WithColumnWidths, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $companies;

    public function __construct(Collection $companies)
    {
        $this->companies = $companies;
    }

    /**
     * @return Collection
     */
    public function collection()
    {
        return $this->companies;
    }

    public function headings(): array
    {
        return [
            'No',
            'Company Name',
            'Industry Type',
            'Category',
            'PIC Name',
            'Position',
            'Email',
            'Phone',
            'Address',
            'Website',
            'Agreement Type',
            'Agreement Title',
            'Reference No',
            'Start Date',
            'End Date',
            'Agreement Status',
            'Faculty',
            'Programme',
            'Remarks',
            'Students Count',
        ];
    }

    /**
     * @param  mixed  $company
     */
    public function map($company): array
    {
        static $rowNumber = 0;
        $rowNumber++;

        // Get the primary/active agreement for this company
        $primaryAgreement = $company->agreements
            ->sortByDesc(function ($a) {
                // Priority: Active > Pending > Draft > Not Started > Expired > Terminated
                $priority = match ($a->status) {
                    'Active' => 6,
                    'Pending' => 5,
                    'Draft' => 4,
                    'Not Started' => 3,
                    'Expired' => 2,
                    'Terminated' => 1,
                    default => 0,
                };

                return $priority;
            })
            ->first();

        return [
            $rowNumber,
            $company->company_name ?? '',
            $company->industry_type ?? '',
            $company->category ?? '',
            $company->pic_name ?? '',
            $company->position ?? '',
            $company->email ?? '',
            $company->phone ?? '',
            $company->address ?? '',
            $company->website ?? '',
            $primaryAgreement->agreement_type ?? '',
            $primaryAgreement->agreement_title ?? '',
            $primaryAgreement->reference_no ?? '',
            $primaryAgreement->start_date ? $primaryAgreement->start_date->format('Y-m-d') : '',
            $primaryAgreement->end_date ? $primaryAgreement->end_date->format('Y-m-d') : '',
            $primaryAgreement->status ?? '',
            $primaryAgreement->faculty ?? '',
            $primaryAgreement->programme ?? '',
            $primaryAgreement->remarks ?? '',
            $company->students_count ?? 0,
        ];
    }

    public function title(): string
    {
        return 'Companies & Agreements';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,   // No
            'B' => 30,  // Company Name
            'C' => 18,  // Industry Type
            'D' => 18,  // Category
            'E' => 20,  // PIC Name
            'F' => 15,  // Position
            'G' => 25,  // Email
            'H' => 15,  // Phone
            'I' => 35,  // Address
            'J' => 25,  // Website
            'K' => 15,  // Agreement Type
            'L' => 25,  // Agreement Title
            'M' => 15,  // Reference No
            'N' => 12,  // Start Date
            'O' => 12,  // End Date
            'P' => 15,  // Agreement Status
            'Q' => 15,  // Faculty
            'R' => 15,  // Programme
            'S' => 30,  // Remarks
            'T' => 12,  // Students Count
        ];
    }

    /**
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        $lastRow = $this->companies->count() + 1;

        return [
            // Header row styling
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 11,
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
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ],
            // Data rows styling
            'A2:T'.$lastRow => [
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC'],
                    ],
                ],
            ],
            // Center the No column
            'A2:A'.$lastRow => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ],
            // Center the Students Count column
            'T2:T'.$lastRow => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ],
        ];
    }
}
