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
            UserSeeder::class,
            GroupSeeder::class,
            CompanySeeder::class,
            StudentSeeder::class,
            PpeIcQuestionSeeder::class,
        ]);
    }
}

