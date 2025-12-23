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
        Schema::create('assessment_evaluators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained('assessments')->onDelete('cascade');
            $table->string('evaluator_role', 20); // lecturer, at, ic, supervisor_li
            $table->decimal('total_score', 5, 2); // Total score percentage for this evaluator
            $table->integer('order')->default(0); // For ordering evaluators
            $table->timestamps();
            
            // Indexes
            $table->index('assessment_id');
            $table->index('evaluator_role');
            $table->index('order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_evaluators');
    }
};
