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
        Schema::create('fyp_rubric_elements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rubric_template_id')->constrained('fyp_rubric_templates')->onDelete('cascade');
            $table->string('element_code', 20); // e.g., "E1", "E2", "PS", "OBJ"
            $table->string('name'); // e.g., "Problem Statement", "Project Objectives", "Slide Design"
            $table->text('description')->nullable(); // Detailed description of what's being assessed
            $table->string('clo_code', 10); // CLO1, CLO2, CLO3, etc.
            $table->decimal('weight_percentage', 5, 2); // Weight of this element in the rubric
            $table->integer('order')->default(0); // Display order
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Indexes
            $table->index('rubric_template_id');
            $table->index('clo_code');
            $table->index('order');
            $table->unique(['rubric_template_id', 'element_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fyp_rubric_elements');
    }
};
