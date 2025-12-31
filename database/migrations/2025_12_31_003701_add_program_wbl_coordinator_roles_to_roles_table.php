<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $roles = [
            [
                'name' => 'bta_wbl_coordinator',
                'display_name' => 'BTA WBL Coordinator',
                'description' => 'Work-Based Learning Coordinator for BTA program',
            ],
            [
                'name' => 'btd_wbl_coordinator',
                'display_name' => 'BTD WBL Coordinator',
                'description' => 'Work-Based Learning Coordinator for BTD program',
            ],
            [
                'name' => 'btg_wbl_coordinator',
                'display_name' => 'BTG WBL Coordinator',
                'description' => 'Work-Based Learning Coordinator for BTG program',
            ],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->insertOrIgnore([
                'name' => $role['name'],
                'display_name' => $role['display_name'],
                'description' => $role['description'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('roles')->whereIn('name', [
            'bta_wbl_coordinator',
            'btd_wbl_coordinator',
            'btg_wbl_coordinator',
        ])->delete();
    }
};
