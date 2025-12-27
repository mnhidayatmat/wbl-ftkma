<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,  // Must run first to create roles
            UserSeeder::class,
            GroupSeeder::class,
            CompanySeeder::class,
            StudentSeeder::class,
            // PpeIcQuestionSeeder::class, // Commented out - seeder doesn't exist
        ]);
    }
}
