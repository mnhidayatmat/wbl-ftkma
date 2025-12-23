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
        // Create clo_plo_mappings table if it doesn't exist
        if (!Schema::hasTable('clo_plo_mappings')) {
            Schema::create('clo_plo_mappings', function (Blueprint $table) {
                $table->id();
                $table->string('course_code', 10); // PPE, IP, OSH, FYP, LI
                $table->string('clo_code', 10); // CLO1, CLO2, CLO3, etc.
                $table->text('clo_description')->nullable(); // Description of the CLO
                $table->boolean('is_active')->default(true); // Whether CLO is active
                $table->boolean('allow_for_assessment')->default(false); // Whether CLO can be used in assessments
                $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
                $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamps();
                
                // Unique constraint: one CLO per course
                $table->unique(['course_code', 'clo_code']);
                
                // Indexes for performance
                $table->index('course_code');
                $table->index('is_active');
                $table->index('allow_for_assessment');
                $table->index(['course_code', 'is_active', 'allow_for_assessment'], 'clo_plo_mappings_course_active_assessment_idx');
            });
        }

        // Create pivot table for CLO-PLO relationships (many-to-many) if it doesn't exist
        if (!Schema::hasTable('clo_plo_relationships')) {
            Schema::create('clo_plo_relationships', function (Blueprint $table) {
                $table->id();
                $table->foreignId('clo_plo_mapping_id')->constrained('clo_plo_mappings')->onDelete('cascade');
                $table->string('plo_code', 10); // PLO1, PLO2, PLO3, etc.
                $table->timestamps();
                
                // Unique constraint: one PLO per CLO mapping
                $table->unique(['clo_plo_mapping_id', 'plo_code']);
                
                // Index for performance
                $table->index('plo_code');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clo_plo_relationships');
        Schema::dropIfExists('clo_plo_mappings');
    }
};
