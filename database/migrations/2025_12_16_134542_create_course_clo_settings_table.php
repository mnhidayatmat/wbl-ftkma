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
        // Create course_clo_settings table to store CLO count per course
        if (! Schema::hasTable('course_clo_settings')) {
            Schema::create('course_clo_settings', function (Blueprint $table) {
                $table->id();
                $table->string('course_code', 10)->unique(); // PPE, IP, OSH, FYP, LI
                $table->integer('clo_count')->default(4); // Number of CLOs for this course
                $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamps();

                $table->index('course_code');
            });
        }

        // Add plo_description column to clo_plo_relationships if it doesn't exist
        if (Schema::hasTable('clo_plo_relationships') && ! Schema::hasColumn('clo_plo_relationships', 'plo_description')) {
            Schema::table('clo_plo_relationships', function (Blueprint $table) {
                $table->text('plo_description')->nullable()->after('plo_code');
            });
        }

        // Seed default CLO counts for each course
        $defaultCounts = [
            ['course_code' => 'PPE', 'clo_count' => 4],
            ['course_code' => 'IP', 'clo_count' => 4],
            ['course_code' => 'OSH', 'clo_count' => 4],
            ['course_code' => 'FYP', 'clo_count' => 7],
            ['course_code' => 'LI', 'clo_count' => 4],
        ];

        foreach ($defaultCounts as $setting) {
            \DB::table('course_clo_settings')->updateOrInsert(
                ['course_code' => $setting['course_code']],
                ['clo_count' => $setting['clo_count'], 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_clo_settings');

        if (Schema::hasTable('clo_plo_relationships') && Schema::hasColumn('clo_plo_relationships', 'plo_description')) {
            Schema::table('clo_plo_relationships', function (Blueprint $table) {
                $table->dropColumn('plo_description');
            });
        }
    }
};
