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
        Schema::table('students', function (Blueprint $table) {
            $table->string('ic_number', 20)->nullable()->after('mobile_phone');
            $table->string('parent_name')->nullable()->after('ic_number');
            $table->string('parent_phone_number', 20)->nullable()->after('parent_name');
            $table->string('next_of_kin')->nullable()->after('parent_phone_number');
            $table->text('home_address')->nullable()->after('next_of_kin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['ic_number', 'parent_name', 'parent_phone_number', 'next_of_kin', 'home_address']);
        });
    }
};
