<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Change interests, preferred_industry, and preferred_location to JSON
     * to support multiple selections.
     */
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->json('interests')->nullable()->change();
            $table->json('preferred_industry')->nullable()->change();
            $table->json('preferred_location')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->text('interests')->nullable()->change();
            $table->string('preferred_industry')->nullable()->change();
            $table->string('preferred_location')->nullable()->change();
        });
    }
};
