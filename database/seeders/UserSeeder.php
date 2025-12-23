<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@wbl.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Lecturer User',
            'email' => 'lecturer@wbl.com',
            'password' => Hash::make('password'),
            'role' => 'lecturer',
        ]);

        User::create([
            'name' => 'Industry User',
            'email' => 'industry@wbl.com',
            'password' => Hash::make('password'),
            'role' => 'industry',
        ]);

        User::create([
            'name' => 'Student User',
            'email' => 'student@wbl.com',
            'password' => Hash::make('password'),
            'role' => 'student',
        ]);

        // New roles for WBL system
        User::create([
            'name' => 'Supervisor LI User',
            'email' => 'supervisor_li@wbl.com',
            'password' => Hash::make('password'),
            'role' => 'supervisor_li',
        ]);

        User::create([
            'name' => 'Academic Tutor (AT) User',
            'email' => 'at@wbl.com',
            'password' => Hash::make('password'),
            'role' => 'at',
        ]);
    }
}

