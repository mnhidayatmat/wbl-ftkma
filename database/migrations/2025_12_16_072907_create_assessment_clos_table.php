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
        Schema::create('assessment_clos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained('assessments')->onDelete('cascade');
            $table->string('clo_code', 10); // CLO1, CLO2, CLO3, etc.
            $table->decimal('weight_percentage', 5, 2); // Weight percentage for this CLO within the assessment
            $table->integer('order')->default(0); // For ordering CLOs
            $table->timestamps();

            // Ensure unique CLO per assessment
            $table->unique(['assessment_id', 'clo_code']);

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
        Schema::dropIfExists('assessment_clos');
    }
};
