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
        if (Schema::hasTable('clo_plo_relationships')) {
            Schema::table('clo_plo_relationships', function (Blueprint $table) {
                if (!Schema::hasColumn('clo_plo_relationships', 'plo_description')) {
                    $table->text('plo_description')->nullable()->after('plo_code');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('clo_plo_relationships')) {
            Schema::table('clo_plo_relationships', function (Blueprint $table) {
                if (Schema::hasColumn('clo_plo_relationships', 'plo_description')) {
                    $table->dropColumn('plo_description');
                }
            });
        }
    }
};
