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
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('module_name'); // e.g., 'ppe', 'fyp', 'ip', 'osh', 'li', 'students', 'companies', 'assessments'
            $table->string('action'); // 'view', 'create', 'update', 'delete', 'export', 'finalise', 'moderate'
            $table->string('display_name'); // Human-readable name
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['module_name', 'action']);
            $table->index('module_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
