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
        Schema::create('workplace_issue_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('issue_report_id')->constrained('workplace_issue_reports')->onDelete('cascade');

            $table->string('file_path');
            $table->string('file_name', 255);
            $table->string('file_type', 50);
            $table->unsignedBigInteger('file_size');
            $table->string('mime_type', 100);

            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->index('issue_report_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workplace_issue_attachments');
    }
};
