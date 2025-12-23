<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\RolePermission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Student Management
            ['module_name' => 'students', 'action' => 'view', 'display_name' => 'View Students', 'description' => 'View student list and profiles', 'sort_order' => 10],
            ['module_name' => 'students', 'action' => 'create', 'display_name' => 'Create Students', 'description' => 'Create new student records', 'sort_order' => 11],
            ['module_name' => 'students', 'action' => 'update', 'display_name' => 'Update Students', 'description' => 'Edit student information', 'sort_order' => 12],
            ['module_name' => 'students', 'action' => 'delete', 'display_name' => 'Delete Students', 'description' => 'Delete student records', 'sort_order' => 13],
            ['module_name' => 'students', 'action' => 'assign', 'display_name' => 'Assign Students', 'description' => 'Assign students to courses, lecturers, ICs', 'sort_order' => 14],

            // Company Management
            ['module_name' => 'companies', 'action' => 'view', 'display_name' => 'View Companies', 'description' => 'View company list and details', 'sort_order' => 20],
            ['module_name' => 'companies', 'action' => 'create', 'display_name' => 'Create Companies', 'description' => 'Create new company records', 'sort_order' => 21],
            ['module_name' => 'companies', 'action' => 'update', 'display_name' => 'Update Companies', 'description' => 'Edit company information', 'sort_order' => 22],
            ['module_name' => 'companies', 'action' => 'delete', 'display_name' => 'Delete Companies', 'description' => 'Delete company records', 'sort_order' => 23],

            // Assessment Management
            ['module_name' => 'assessments', 'action' => 'view', 'display_name' => 'View Assessments', 'description' => 'View assessment configurations', 'sort_order' => 30],
            ['module_name' => 'assessments', 'action' => 'create', 'display_name' => 'Create Assessments', 'description' => 'Create new assessments', 'sort_order' => 31],
            ['module_name' => 'assessments', 'action' => 'update', 'display_name' => 'Update Assessments', 'description' => 'Edit assessment settings', 'sort_order' => 32],
            ['module_name' => 'assessments', 'action' => 'delete', 'display_name' => 'Delete Assessments', 'description' => 'Delete assessments', 'sort_order' => 33],

            // PPE Module
            ['module_name' => 'ppe', 'action' => 'view', 'display_name' => 'View PPE', 'description' => 'View PPE module data', 'sort_order' => 40],
            ['module_name' => 'ppe', 'action' => 'evaluate', 'display_name' => 'Evaluate PPE', 'description' => 'Evaluate students in PPE', 'sort_order' => 41],
            ['module_name' => 'ppe', 'action' => 'moderate', 'display_name' => 'Moderate PPE', 'description' => 'Moderate PPE results', 'sort_order' => 42],
            ['module_name' => 'ppe', 'action' => 'finalise', 'display_name' => 'Finalise PPE', 'description' => 'Finalise PPE results', 'sort_order' => 43],
            ['module_name' => 'ppe', 'action' => 'export', 'display_name' => 'Export PPE', 'description' => 'Export PPE reports', 'sort_order' => 44],
            ['module_name' => 'ppe', 'action' => 'schedule', 'display_name' => 'Manage PPE Schedule', 'description' => 'Manage PPE assessment windows', 'sort_order' => 45],

            // IP Module
            ['module_name' => 'ip', 'action' => 'view', 'display_name' => 'View IP', 'description' => 'View IP module data', 'sort_order' => 50],
            ['module_name' => 'ip', 'action' => 'evaluate', 'display_name' => 'Evaluate IP', 'description' => 'Evaluate students in IP', 'sort_order' => 51],
            ['module_name' => 'ip', 'action' => 'moderate', 'display_name' => 'Moderate IP', 'description' => 'Moderate IP results', 'sort_order' => 52],
            ['module_name' => 'ip', 'action' => 'finalise', 'display_name' => 'Finalise IP', 'description' => 'Finalise IP results', 'sort_order' => 53],
            ['module_name' => 'ip', 'action' => 'export', 'display_name' => 'Export IP', 'description' => 'Export IP reports', 'sort_order' => 54],
            ['module_name' => 'ip', 'action' => 'schedule', 'display_name' => 'Manage IP Schedule', 'description' => 'Manage IP assessment windows', 'sort_order' => 55],

            // OSH Module
            ['module_name' => 'osh', 'action' => 'view', 'display_name' => 'View OSH', 'description' => 'View OSH module data', 'sort_order' => 60],
            ['module_name' => 'osh', 'action' => 'evaluate', 'display_name' => 'Evaluate OSH', 'description' => 'Evaluate students in OSH', 'sort_order' => 61],
            ['module_name' => 'osh', 'action' => 'moderate', 'display_name' => 'Moderate OSH', 'description' => 'Moderate OSH results', 'sort_order' => 62],
            ['module_name' => 'osh', 'action' => 'finalise', 'display_name' => 'Finalise OSH', 'description' => 'Finalise OSH results', 'sort_order' => 63],
            ['module_name' => 'osh', 'action' => 'export', 'display_name' => 'Export OSH', 'description' => 'Export OSH reports', 'sort_order' => 64],
            ['module_name' => 'osh', 'action' => 'schedule', 'display_name' => 'Manage OSH Schedule', 'description' => 'Manage OSH assessment windows', 'sort_order' => 65],

            // FYP Module
            ['module_name' => 'fyp', 'action' => 'view', 'display_name' => 'View FYP', 'description' => 'View FYP module data', 'sort_order' => 70],
            ['module_name' => 'fyp', 'action' => 'evaluate', 'display_name' => 'Evaluate FYP', 'description' => 'Evaluate students in FYP', 'sort_order' => 71],
            ['module_name' => 'fyp', 'action' => 'moderate', 'display_name' => 'Moderate FYP', 'description' => 'Moderate FYP results', 'sort_order' => 72],
            ['module_name' => 'fyp', 'action' => 'finalise', 'display_name' => 'Finalise FYP', 'description' => 'Finalise FYP results', 'sort_order' => 73],
            ['module_name' => 'fyp', 'action' => 'export', 'display_name' => 'Export FYP', 'description' => 'Export FYP reports', 'sort_order' => 74],

            // LI Module
            ['module_name' => 'li', 'action' => 'view', 'display_name' => 'View LI', 'description' => 'View LI module data', 'sort_order' => 80],
            ['module_name' => 'li', 'action' => 'evaluate', 'display_name' => 'Evaluate LI', 'description' => 'Evaluate students in LI', 'sort_order' => 81],
            ['module_name' => 'li', 'action' => 'moderate', 'display_name' => 'Moderate LI', 'description' => 'Moderate LI results', 'sort_order' => 82],
            ['module_name' => 'li', 'action' => 'finalise', 'display_name' => 'Finalise LI', 'description' => 'Finalise LI results', 'sort_order' => 83],
            ['module_name' => 'li', 'action' => 'export', 'display_name' => 'Export LI', 'description' => 'Export LI reports', 'sort_order' => 84],

            // Reports
            ['module_name' => 'reports', 'action' => 'view', 'display_name' => 'View Reports', 'description' => 'View all reports', 'sort_order' => 90],
            ['module_name' => 'reports', 'action' => 'export', 'display_name' => 'Export Reports', 'description' => 'Export reports in various formats', 'sort_order' => 91],

            // Audit Logs
            ['module_name' => 'audit', 'action' => 'view', 'display_name' => 'View Audit Logs', 'description' => 'View system audit logs', 'sort_order' => 100],
            ['module_name' => 'audit', 'action' => 'export', 'display_name' => 'Export Audit Logs', 'description' => 'Export audit log data', 'sort_order' => 101],

            // User Management
            ['module_name' => 'users', 'action' => 'view', 'display_name' => 'View Users', 'description' => 'View user list', 'sort_order' => 110],
            ['module_name' => 'users', 'action' => 'manage_roles', 'display_name' => 'Manage User Roles', 'description' => 'Assign and modify user roles', 'sort_order' => 111],
            ['module_name' => 'users', 'action' => 'manage_permissions', 'display_name' => 'Manage Permissions', 'description' => 'Configure role permissions', 'sort_order' => 112],

            // Groups
            ['module_name' => 'groups', 'action' => 'view', 'display_name' => 'View Groups', 'description' => 'View student groups', 'sort_order' => 120],
            ['module_name' => 'groups', 'action' => 'create', 'display_name' => 'Create Groups', 'description' => 'Create new groups', 'sort_order' => 121],
            ['module_name' => 'groups', 'action' => 'update', 'display_name' => 'Update Groups', 'description' => 'Edit group information', 'sort_order' => 122],
            ['module_name' => 'groups', 'action' => 'delete', 'display_name' => 'Delete Groups', 'description' => 'Delete groups', 'sort_order' => 123],
        ];

        foreach ($permissions as $permissionData) {
            Permission::updateOrCreate(
                [
                    'module_name' => $permissionData['module_name'],
                    'action' => $permissionData['action'],
                ],
                $permissionData
            );
        }

        $this->command->info('Permissions seeded successfully!');
    }
}
