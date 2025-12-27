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
        Schema::table('company_agreements', function (Blueprint $table) {
            $table->string('staff_pic_name')->nullable()->after('remarks');
            $table->string('staff_pic_phone')->nullable()->after('staff_pic_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_agreements', function (Blueprint $table) {
            $table->dropColumn(['staff_pic_name', 'staff_pic_phone']);
        });
    }
};
