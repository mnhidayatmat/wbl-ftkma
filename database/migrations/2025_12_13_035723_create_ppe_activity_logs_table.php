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
        Schema::create('ppe_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->string('action'); // 'window_opened', 'window_closed', 'reminder_sent', etc.
            $table->string('evaluator_role')->nullable(); // 'lecturer', 'ic', or null
            $table->text('description');
            $table->foreignId('admin_user_id')->constrained('users')->onDelete('cascade');
            $table->json('metadata')->nullable(); // Store additional data like student count, etc.
            $table->timestamps();

            $table->index(['action', 'created_at']);
            $table->index('evaluator_role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ppe_activity_logs');
    }
};
