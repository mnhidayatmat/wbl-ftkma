<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // admin, coordinator, lecturer, at, ic, supervisor_li
            $table->string('display_name'); // Admin, Coordinator, Lecturer, AT, IC, Supervisor LI
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Seed default roles
        $roles = [
            ['name' => 'admin', 'display_name' => 'Admin', 'description' => 'Full system access'],
            ['name' => 'coordinator', 'display_name' => 'Coordinator', 'description' => 'Academic coordination and overview'],
            ['name' => 'lecturer', 'display_name' => 'Lecturer', 'description' => 'Can evaluate IP, OSH, PPE'],
            ['name' => 'at', 'display_name' => 'Academic Tutor (AT)', 'description' => 'FYP evaluation only'],
            ['name' => 'ic', 'display_name' => 'Industry Coach (IC)', 'description' => 'PPE/IC/OSH/FYP/WBL evaluation'],
            ['name' => 'supervisor_li', 'display_name' => 'Supervisor LI', 'description' => 'Industrial Training assessments'],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->insert([
                'name' => $role['name'],
                'display_name' => $role['display_name'],
                'description' => $role['description'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
