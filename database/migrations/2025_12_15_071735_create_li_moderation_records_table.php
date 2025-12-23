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
        Schema::create('li_moderation_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->decimal('original_supervisor_score', 8, 2)->nullable();
            $table->decimal('original_ic_score', 8, 2)->nullable();
            $table->decimal('original_final_score', 8, 2)->nullable();
            $table->decimal('adjusted_supervisor_score', 8, 2)->nullable();
            $table->decimal('adjusted_ic_score', 8, 2)->nullable();
            $table->decimal('adjusted_final_score', 8, 2)->nullable();
            $table->decimal('adjustment_percentage', 8, 2)->default(0);
            $table->string('adjustment_type')->default('percentage');
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
        Schema::dropIfExists('li_moderation_records');
    }
};
