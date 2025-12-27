<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User with ALL roles
        $adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin@wbl.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Assign ALL available roles to admin user
        $allRoles = Role::all()->pluck('id');
        $adminUser->roles()->attach($allRoles);

        // Create Lecturer User
        $lecturerUser = User::create([
            'name' => 'Lecturer User',
            'email' => 'lecturer@wbl.com',
            'password' => Hash::make('password'),
            'role' => 'lecturer',
        ]);
        $lecturerUser->roles()->attach(Role::where('name', 'lecturer')->first()->id);

        // Create Industry Coach User
        $industryUser = User::create([
            'name' => 'Industry User',
            'email' => 'industry@wbl.com',
            'password' => Hash::make('password'),
            'role' => 'industry',
        ]);
        $industryUser->roles()->attach(Role::where('name', 'ic')->first()->id);

        // Create Student User
        $studentUser = User::create([
            'name' => 'Student User',
            'email' => 'student@wbl.com',
            'password' => Hash::make('password'),
            'role' => 'student',
        ]);
        $studentUser->roles()->attach(Role::where('name', 'student')->first()->id);

        // Create Supervisor LI User
        $supervisorLiUser = User::create([
            'name' => 'Supervisor LI User',
            'email' => 'supervisor_li@wbl.com',
            'password' => Hash::make('password'),
            'role' => 'supervisor_li',
        ]);
        $supervisorLiUser->roles()->attach(Role::where('name', 'supervisor_li')->first()->id);

        // Create Academic Tutor (AT) User
        $atUser = User::create([
            'name' => 'Academic Tutor (AT) User',
            'email' => 'at@wbl.com',
            'password' => Hash::make('password'),
            'role' => 'at',
        ]);
        $atUser->roles()->attach(Role::where('name', 'at')->first()->id);

        // Create Coordinator User
        $coordinatorUser = User::create([
            'name' => 'Coordinator User',
            'email' => 'coordinator@wbl.com',
            'password' => Hash::make('password'),
            'role' => 'coordinator',
        ]);
        $coordinatorUser->roles()->attach(Role::where('name', 'coordinator')->first()->id);
    }
}
