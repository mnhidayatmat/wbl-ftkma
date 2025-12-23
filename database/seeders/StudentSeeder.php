<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Student;
use App\Models\WblGroup;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $groups = WblGroup::all();
        $companies = Company::all();

        Student::factory()->count(10)->create([
            'group_id' => fn () => $groups->random()->id,
            'company_id' => fn () => $companies->random()->id,
        ]);
    }
}
