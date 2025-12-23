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
        Schema::create('moas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->string('moa_type'); // Programme-based, Student-based
            $table->string('course_code')->nullable(); // PPE, IP, OSH, FYP, LI
            $table->string('status'); // Draft, In Progress, Signed, Expired
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->date('signed_date')->nullable();
            $table->string('file_path')->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index('company_id');
            $table->index('status');
            $table->index('course_code');
        });

        // Pivot table for MoA-Student relationship
        Schema::create('moa_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('moa_id')->constrained('moas')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['moa_id', 'student_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('moas');
    }
};
