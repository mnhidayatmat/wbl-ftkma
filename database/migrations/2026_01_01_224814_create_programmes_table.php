<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('programmes', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('short_code', 10)->unique();
            $table->string('wbl_coordinator_role')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Seed existing programmes
        $programmes = [
            [
                'name' => 'Bachelor of Mechanical Engineering Technology (Automotive) with Honours',
                'short_code' => 'BTA',
                'wbl_coordinator_role' => 'bta_wbl_coordinator',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Bachelor of Mechanical Engineering Technology (Design and Analysis) with Honours',
                'short_code' => 'BTD',
                'wbl_coordinator_role' => 'btd_wbl_coordinator',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Bachelor of Mechanical Engineering Technology (Oil and Gas) with Honours',
                'short_code' => 'BTG',
                'wbl_coordinator_role' => 'btg_wbl_coordinator',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        \Illuminate\Support\Facades\DB::table('programmes')->insert($programmes);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programmes');
    }
};
