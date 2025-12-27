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
        Schema::create('ppe_assessment_window_assessments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ppe_assessment_window_id');
            $table->unsignedBigInteger('assessment_id');
            $table->timestamps();

            // Foreign keys with short constraint names
            $table->foreign('ppe_assessment_window_id', 'ppe_aw_asmt_window_fk')
                ->references('id')->on('ppe_assessment_windows')->onDelete('cascade');
            $table->foreign('assessment_id', 'ppe_aw_asmt_fk')
                ->references('id')->on('assessments')->onDelete('cascade');

            // Unique constraint
            $table->unique(['ppe_assessment_window_id', 'assessment_id'], 'ppe_aw_asmt_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ppe_assessment_window_assessments');
    }
};
