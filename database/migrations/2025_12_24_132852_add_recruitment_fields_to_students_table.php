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
            $table->json('skills')->nullable()->after('background');
            $table->text('interests')->nullable()->after('skills');
            $table->string('preferred_industry')->nullable()->after('interests');
            $table->string('preferred_location')->nullable()->after('preferred_industry');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['skills', 'interests', 'preferred_industry', 'preferred_location']);
        });
    }
};
