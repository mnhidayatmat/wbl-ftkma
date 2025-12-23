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
        Schema::create('fyp_rubric_level_descriptors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rubric_element_id')->constrained('fyp_rubric_elements')->onDelete('cascade');
            $table->integer('level'); // 1, 2, 3, 4, 5
            $table->string('label', 50); // AWARE, LIMITED, FAIR, GOOD, EXCELLENT
            $table->text('descriptor'); // Detailed text describing performance at this level
            $table->decimal('score_value', 5, 2); // Numeric score for this level (e.g., 1.0, 2.0, 3.0, 4.0, 5.0)
            $table->timestamps();

            // Indexes
            $table->index('rubric_element_id');
            $table->unique(['rubric_element_id', 'level']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fyp_rubric_level_descriptors');
    }
};
