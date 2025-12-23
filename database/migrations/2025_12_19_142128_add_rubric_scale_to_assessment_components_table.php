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
        Schema::table('assessment_components', function (Blueprint $table) {
            // Rubric scale for Logbook assessment type (1-10 scoring)
            $table->unsignedTinyInteger('rubric_scale_min')->nullable()->default(1)->after('max_score');
            $table->unsignedTinyInteger('rubric_scale_max')->nullable()->default(10)->after('rubric_scale_min');
            // Duration label for Logbook (e.g., "Month 1", "M1", "Week 1-4")
            $table->string('duration_label', 50)->nullable()->after('rubric_scale_max');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assessment_components', function (Blueprint $table) {
            $table->dropColumn(['rubric_scale_min', 'rubric_scale_max', 'duration_label']);
        });
    }
};
