<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;

class CompaniesTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    /**
     * Return empty array for template (just headers)
     */
    public function array(): array
    {
        // Return a few sample rows to guide users
        return [
            [
                'ABC Corporation',
                'IT',
                'John Doe',
                'john.doe@abc.com',
                '+60123456789',
                '123 Business Street',
                'Kuala Lumpur',
                'Selangor',
                '50000',
                'Malaysia',
                'https://www.abc.com',
                'Information Technology',
                '100-500',
                'This is a sample note about the company',
            ],
            [
                'XYZ Industries Sdn Bhd',
                'Manufacturing',
                'Jane Smith',
                'jane.smith@xyz.com',
                '+60198765432',
                '456 Industrial Park',
                'Johor Bahru',
                'Johor',
                '80000',
                'Malaysia',
                'https://www.xyz.com',
                'Manufacturing',
                '500+',
                'Sample manufacturing company',
            ],
        ];
    }

    /**
     * Define column headings
     */
    public function headings(): array
    {
        return [
            'company_name',      // Required
            'category',          // Oil and Gas, Design, Automotive, IT, Manufacturing, Construction, Other
            'pic_name',
            'email',
            'phone',
            'address',
            'city',
            'state',
            'postcode',
            'country',
            'website',
            'industry',
            'company_size',
            'notes',
        ];
    }

    /**
     * Style the worksheet
     */
    public function styles(Worksheet $sheet)
    {
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
            ],
        ];
    }

    /**
     * Define column widths
     */
    public function columnWidths(): array
    {
        return [
            'A' => 30,  // company_name
            'B' => 15,  // category
            'C' => 20,  // pic_name
            'D' => 25,  // email
            'E' => 15,  // phone
            'F' => 30,  // address
            'G' => 15,  // city
            'H' => 12,  // state
            'I' => 10,  // postcode
            'J' => 12,  // country
            'K' => 25,  // website
            'L' => 20,  // industry
            'M' => 12,  // company_size
            'N' => 30,  // notes
        ];
    }
}
