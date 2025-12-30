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
        Schema::create('document_templates', function (Blueprint $table) {
            $table->id();
            $table->string('type')->unique(); // SAL, SCL, MOU
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->text('salutation')->nullable();
            $table->longText('body_content');
            $table->text('closing_text')->nullable();
            $table->string('signatory_name')->nullable();
            $table->string('signatory_title')->nullable();
            $table->string('signatory_department')->nullable();
            $table->string('logo_path')->nullable();
            $table->json('settings')->nullable(); // Additional settings like margins, font size, etc.
            $table->boolean('is_active')->default(true);
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_templates');
    }
};
