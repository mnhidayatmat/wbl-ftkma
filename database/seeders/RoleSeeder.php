<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['name' => 'admin', 'display_name' => 'Admin', 'description' => 'Full system access'],
            ['name' => 'coordinator', 'display_name' => 'Coordinator', 'description' => 'Academic coordination and overview'],
            ['name' => 'lecturer', 'display_name' => 'Lecturer', 'description' => 'Can evaluate IP, OSH, PPE'],
            ['name' => 'at', 'display_name' => 'Academic Tutor (AT)', 'description' => 'FYP evaluation only'],
            ['name' => 'ic', 'display_name' => 'Industry Coach (IC)', 'description' => 'PPE/IC/OSH/FYP/WBL evaluation'],
            ['name' => 'supervisor_li', 'display_name' => 'Supervisor LI', 'description' => 'Industrial Training assessments'],
            ['name' => 'student', 'display_name' => 'Student', 'description' => 'Student role for WBL system'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['name' => $role['name']],
                [
                    'display_name' => $role['display_name'],
                    'description' => $role['description'],
                ]
            );
        }
    }
}
