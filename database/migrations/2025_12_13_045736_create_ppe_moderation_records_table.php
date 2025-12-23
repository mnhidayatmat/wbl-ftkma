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
        Schema::create('ppe_moderation_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->decimal('original_lecturer_score', 5, 2)->nullable();
            $table->decimal('original_ic_score', 5, 2)->nullable();
            $table->decimal('original_final_score', 5, 2)->nullable();
            $table->decimal('adjusted_lecturer_score', 5, 2)->nullable();
            $table->decimal('adjusted_ic_score', 5, 2)->nullable();
            $table->decimal('adjusted_final_score', 5, 2)->nullable();
            $table->decimal('adjustment_percentage', 5, 2)->default(0); // +/- percentage
            $table->enum('adjustment_type', ['percentage', 'manual_override'])->default('percentage');
            $table->text('justification')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('moderated_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->index('student_id');
            $table->index('moderated_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ppe_moderation_records');
    }
};
