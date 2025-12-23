<?php

namespace Database\Seeders;

use App\Models\WblGroup;
use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $groups = [
            [
                'name' => 'Group A - 2024',
                'start_date' => '2024-01-01',
                'end_date' => '2024-06-30',
            ],
            [
                'name' => 'Group B - 2024',
                'start_date' => '2024-02-01',
                'end_date' => '2024-07-31',
            ],
            [
                'name' => 'Group C - 2024',
                'start_date' => '2024-03-01',
                'end_date' => '2024-08-31',
            ],
            [
                'name' => 'Group D - 2024',
                'start_date' => '2024-04-01',
                'end_date' => '2024-09-30',
            ],
            [
                'name' => 'Group E - 2024',
                'start_date' => '2024-05-01',
                'end_date' => '2024-10-31',
            ],
        ];

        foreach ($groups as $group) {
            WblGroup::create($group);
        }
    }
}

