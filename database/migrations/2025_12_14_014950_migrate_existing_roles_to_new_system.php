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
        // Map old role values to new role names
        $roleMapping = [
            'admin' => 'admin',
            'lecturer' => 'lecturer',
            'industry' => 'ic',
            'student' => null, // Students don't get roles in the new system
            'supervisor_li' => 'supervisor_li',
            'at' => 'at',
        ];

        // Migrate existing user roles
        $users = DB::table('users')->get();

        foreach ($users as $user) {
            $oldRole = $user->role;

            if (isset($roleMapping[$oldRole]) && $roleMapping[$oldRole] !== null) {
                $roleId = DB::table('roles')
                    ->where('name', $roleMapping[$oldRole])
                    ->value('id');

                if ($roleId) {
                    // Check if user already has this role
                    $exists = DB::table('user_roles')
                        ->where('user_id', $user->id)
                        ->where('role_id', $roleId)
                        ->exists();

                    if (! $exists) {
                        DB::table('user_roles')->insert([
                            'user_id' => $user->id,
                            'role_id' => $roleId,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Clear user_roles table
        DB::table('user_roles')->truncate();
    }
};
