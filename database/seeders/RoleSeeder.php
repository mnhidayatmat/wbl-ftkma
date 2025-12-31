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
            ['name' => 'fyp_coordinator', 'display_name' => 'FYP Coordinator', 'description' => 'Monitors and manages Final Year Project module'],
            ['name' => 'ip_coordinator', 'display_name' => 'IP Coordinator', 'description' => 'Monitors and manages Industrial Project module'],
            ['name' => 'osh_coordinator', 'display_name' => 'OSH Coordinator', 'description' => 'Monitors and manages Occupational Safety & Health module'],
            ['name' => 'ppe_coordinator', 'display_name' => 'PPE Coordinator', 'description' => 'Monitors and manages Professional Practice Evaluation module'],
            ['name' => 'li_coordinator', 'display_name' => 'Industrial Training Coordinator', 'description' => 'Monitors and manages Learning Integration module'],
            ['name' => 'lecturer', 'display_name' => 'Lecturer', 'description' => 'Can evaluate IP, OSH, PPE'],
            ['name' => 'at', 'display_name' => 'Academic Tutor (AT)', 'description' => 'FYP evaluation only'],
            ['name' => 'ic', 'display_name' => 'Industry Coach (IC)', 'description' => 'PPE/IC/OSH/FYP/WBL evaluation'],
            ['name' => 'supervisor_li', 'display_name' => 'Supervisor LI', 'description' => 'Industrial Training assessments'],
            ['name' => 'student', 'display_name' => 'Student', 'description' => 'Student role for WBL system'],
            ['name' => 'bta_wbl_coordinator', 'display_name' => 'BTA WBL Coordinator', 'description' => 'Work-Based Learning Coordinator for BTA program'],
            ['name' => 'btd_wbl_coordinator', 'display_name' => 'BTD WBL Coordinator', 'description' => 'Work-Based Learning Coordinator for BTD program'],
            ['name' => 'btg_wbl_coordinator', 'display_name' => 'BTG WBL Coordinator', 'description' => 'Work-Based Learning Coordinator for BTG program'],
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
