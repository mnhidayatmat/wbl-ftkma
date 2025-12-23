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
        Schema::create('ip_moderation_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->decimal('original_lecturer_score', 5, 2)->default(0);
            $table->decimal('original_ic_score', 5, 2)->default(0);
            $table->decimal('original_final_score', 5, 2)->default(0);
            $table->decimal('adjusted_lecturer_score', 5, 2)->default(0);
            $table->decimal('adjusted_ic_score', 5, 2)->default(0);
            $table->decimal('adjusted_final_score', 5, 2)->default(0);
            $table->decimal('adjustment_percentage', 5, 2)->default(0);
            $table->string('adjustment_type'); // 'percentage' or 'manual_override'
            $table->text('justification');
            $table->text('notes')->nullable();
            $table->foreignId('moderated_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->unique('student_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ip_moderation_records');
    }
};
