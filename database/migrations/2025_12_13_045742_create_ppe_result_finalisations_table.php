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
        Schema::create('ppe_result_finalisations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->nullable()->constrained('students')->onDelete('cascade');
            $table->foreignId('group_id')->nullable()->constrained('wbl_groups')->onDelete('cascade');
            $table->enum('finalisation_scope', ['student', 'group', 'course'])->default('student');
            $table->boolean('is_finalised')->default(false);
            $table->text('notes')->nullable();
            $table->foreignId('finalised_by')->constrained('users')->onDelete('cascade');
            $table->timestamp('finalised_at')->nullable();
            $table->timestamps();

            $table->index('student_id');
            $table->index('group_id');
            $table->index('is_finalised');
            $table->unique(['student_id', 'is_finalised'], 'unique_student_finalisation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ppe_result_finalisations');
    }
};
