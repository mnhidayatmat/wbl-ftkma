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
            $table->longText('canvas_elements')->nullable()->after('settings');
            $table->integer('canvas_width')->default(595)->after('canvas_elements'); // A4 width in points
            $table->integer('canvas_height')->default(842)->after('canvas_width'); // A4 height in points
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_templates', function (Blueprint $table) {
            $table->dropColumn(['canvas_elements', 'canvas_width', 'canvas_height']);
        });
    }
};
