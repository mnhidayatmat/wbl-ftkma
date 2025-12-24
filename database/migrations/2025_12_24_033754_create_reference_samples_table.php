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
        Schema::create('reference_samples', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // e.g., "Resume Structure", "Poster Layout"
            $table->enum('category', ['resume', 'poster', 'achievement', 'other'])->default('other');
            $table->string('file_path'); // Storage path
            $table->string('file_name'); // Original file name
            $table->unsignedBigInteger('file_size'); // File size in bytes
            $table->string('mime_type'); // File MIME type
            $table->text('description')->nullable(); // Optional description
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade'); // Who uploaded it
            $table->boolean('is_active')->default(true); // Active/inactive toggle
            $table->integer('display_order')->default(0); // For custom sorting
            $table->unsignedInteger('download_count')->default(0); // Track downloads
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('category');
            $table->index('is_active');
            $table->index('display_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reference_samples');
    }
};
