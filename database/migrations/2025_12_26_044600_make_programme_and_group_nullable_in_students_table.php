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
            // Drop foreign key constraint first
            $table->dropForeign(['group_id']);

            // Make programme nullable
            $table->string('programme')->nullable()->change();

            // Make group_id nullable and re-add foreign key
            $table->foreignId('group_id')->nullable()->change();
            $table->foreign('group_id')->references('id')->on('wbl_groups')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // Drop the nullable foreign key
            $table->dropForeign(['group_id']);

            // Revert programme to not nullable
            $table->string('programme')->nullable(false)->change();

            // Revert group_id to not nullable and re-add foreign key with cascade
            $table->foreignId('group_id')->nullable(false)->change();
            $table->foreign('group_id')->references('id')->on('wbl_groups')->onDelete('cascade');
        });
    }
};
