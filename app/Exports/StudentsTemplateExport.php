<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;

class StudentsTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    /**
     * Return sample data for the template
     */
    public function array(): array
    {
        return [
            [
                'Ahmad bin Ali',
                'CD12345678',
                'Bachelor of Software Engineering',
                'Group 1',
                'Tech Solutions Sdn Bhd',
                '3.75',
                '990101-01-1234',
                '+60123456789',
                'Ali bin Rahman',
                '+60198765432',
                'Fatimah binti Ali',
                '+60123456788',
                'No. 123, Jalan ABC, Taman DEF, 26000 Kuantan, Pahang',
                'A dedicated student with strong programming background',
                'PHP, Laravel, MySQL, JavaScript',
                'Web Development, Mobile Apps',
                'Technology',
                'Kuala Lumpur',
            ],
            [
                'Siti Nurhaliza',
                'CD87654321',
                'Bachelor of Computer Science',
                'Group 1',
                'Digital Innovation Inc',
                '3.50',
                '000202-02-5678',
                '+60129876543',
                'Haliza binti Abdullah',
                '+60197654321',
                'Nurul binti Haliza',
                '+60129876542',
                'No. 456, Jalan XYZ, Bandar Indera Mahkota, 25200 Kuantan',
                'Passionate about data science and AI',
                'Python, Data Analysis, Machine Learning',
                'Data Science, AI Research',
                'Technology',
                'Penang',
            ],
            [
                'Muhammad Hakim',
                'CD11223344',
                'Bachelor of Information Technology',
                'Group 1',
                '',
                '3.25',
                '010303-03-9012',
                '+60123334444',
                'Hakim bin Yusof',
                '+60198887777',
                'Aminah binti Hakim',
                '+60123334443',
                'No. 789, Jalan PQR, Taman Gelora, 26300 Gambang, Pahang',
                '',
                'Java, Spring Boot, React',
                'Full Stack Development',
                'Finance',
                'Remote',
            ],
        ];
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
        ];
    }

    /**
     * Apply styles to the worksheet
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the header row
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '003A6C'], // UMPSA primary color
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
        ];
    }
}
