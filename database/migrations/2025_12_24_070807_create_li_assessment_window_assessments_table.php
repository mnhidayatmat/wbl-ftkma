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
            $table->unsignedBigInteger('li_assessment_window_id');
            $table->unsignedBigInteger('assessment_id');
            $table->timestamps();

            // Foreign keys with short constraint names
            $table->foreign('li_assessment_window_id', 'li_aw_asmt_window_fk')
                ->references('id')->on('li_assessment_windows')->onDelete('cascade');
            $table->foreign('assessment_id', 'li_aw_asmt_fk')
                ->references('id')->on('assessments')->onDelete('cascade');

            // Unique constraint
            $table->unique(['li_assessment_window_id', 'assessment_id'], 'li_aw_asmt_unique');
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
