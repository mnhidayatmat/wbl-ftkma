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
        Schema::create('recruitment_handovers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->json('recruiter_emails'); // Array of email addresses
            $table->json('student_ids'); // Array of student IDs
            $table->integer('student_count')->default(0);
            $table->text('message')->nullable(); // Custom message from coordinator
            $table->foreignId('handed_over_by')->constrained('users')->onDelete('cascade');
            $table->json('filters_applied')->nullable(); // Record of filters used
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recruitment_handovers');
    }
};
