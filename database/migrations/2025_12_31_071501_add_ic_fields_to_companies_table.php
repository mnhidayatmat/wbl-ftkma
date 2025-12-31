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
        Schema::table('companies', function (Blueprint $table) {
            $table->string('ic_name')->nullable()->after('address');
            $table->string('ic_phone')->nullable()->after('ic_name');
            $table->string('ic_position')->nullable()->after('ic_phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['ic_name', 'ic_phone', 'ic_position']);
        });
    }
};
