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
        Schema::create('assessment_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained('assessments')->onDelete('cascade');
            $table->string('component_name'); // e.g., "Problem Statement"
            $table->text('criteria_keywords')->nullable(); // e.g., "Clarity and Conciseness, Relevance, Specificity..."
            $table->string('clo_code'); // e.g., "CLO1"
            $table->decimal('weight_percentage', 5, 2); // e.g., 1.70
            $table->integer('order')->default(0); // For ordering components
            $table->timestamps();

            $table->index(['assessment_id', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_components');
    }
};
