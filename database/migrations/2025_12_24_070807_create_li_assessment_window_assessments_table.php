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
        Schema::create('li_assessment_window_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('li_assessment_window_id')
                ->constrained('li_assessment_windows')
                ->onDelete('cascade');
            $table->foreignId('assessment_id')
                ->constrained('assessments')
                ->onDelete('cascade');
            $table->timestamps();

            // Unique constraint: one assessment can't be assigned to same window multiple times
            $table->unique(['li_assessment_window_id', 'assessment_id'], 'li_aw_assessment_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('li_assessment_window_assessments');
    }
};
