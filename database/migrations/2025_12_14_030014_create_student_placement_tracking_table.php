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
        Schema::create('student_placement_tracking', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('group_id')->constrained('wbl_groups')->onDelete('cascade');
            $table->enum('status', [
                'NOT_APPLIED',
                'SAL_RELEASED',
                'APPLIED',
                'INTERVIEWED',
                'OFFER_RECEIVED',
                'ACCEPTED',
                'CONFIRMED',
                'SCL_RELEASED'
            ])->default('NOT_APPLIED');
            $table->timestamp('sal_released_at')->nullable();
            $table->foreignId('sal_released_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('sal_file_path')->nullable();
            $table->timestamp('scl_released_at')->nullable();
            $table->foreignId('scl_released_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('scl_file_path')->nullable();
            $table->string('confirmation_proof_path')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->index('status');
            $table->index('group_id');
            $table->unique('student_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_placement_tracking');
    }
};
