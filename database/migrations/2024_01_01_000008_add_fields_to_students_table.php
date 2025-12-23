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
            $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->onDelete('cascade');
            $table->string('image_path')->nullable()->after('company_id');
            $table->foreignId('ic_id')->nullable()->after('image_path')->constrained('users')->onDelete('set null');
            $table->text('background')->nullable()->after('ic_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['ic_id']);
            $table->dropColumn(['user_id', 'image_path', 'ic_id', 'background']);
        });
    }
};

