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
        Schema::create('assessment_rubrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained('assessments')->onDelete('cascade');
            $table->string('question_code', 10); // Q1, Q2, Q3, etc.
            $table->string('question_title');
            $table->text('question_description')->nullable();
            $table->string('clo_code', 10); // CLO1, CLO2, CLO3, CLO4
            $table->decimal('weight_percentage', 5, 2); // Weight within the assessment
            $table->integer('rubric_min')->default(1); // Minimum rubric score
            $table->integer('rubric_max')->default(5); // Maximum rubric score
            $table->text('example_answer')->nullable(); // Optional guidance
            $table->integer('order')->default(0); // For ordering questions
            $table->timestamps();
            
            // Indexes
            $table->index('assessment_id');
            $table->index('order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_rubrics');
    }
};
