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
        Schema::table('document_templates', function (Blueprint $table) {
            $table->string('word_template_path')->nullable()->after('logo_path');
            $table->string('word_template_original_name')->nullable()->after('word_template_path');
            $table->enum('template_mode', ['canvas', 'word'])->default('canvas')->after('word_template_original_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_templates', function (Blueprint $table) {
            $table->dropColumn(['word_template_path', 'word_template_original_name', 'template_mode']);
        });
    }
};
