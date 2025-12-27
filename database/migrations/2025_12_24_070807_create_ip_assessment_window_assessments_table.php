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
        Schema::create('ip_assessment_window_assessments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ip_assessment_window_id');
            $table->unsignedBigInteger('assessment_id');
            $table->timestamps();

            // Foreign keys with short constraint names
            $table->foreign('ip_assessment_window_id', 'ip_aw_asmt_window_fk')
                ->references('id')->on('ip_assessment_windows')->onDelete('cascade');
            $table->foreign('assessment_id', 'ip_aw_asmt_fk')
                ->references('id')->on('assessments')->onDelete('cascade');

            // Unique constraint
            $table->unique(['ip_assessment_window_id', 'assessment_id'], 'ip_aw_asmt_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ip_assessment_window_assessments');
    }
};
