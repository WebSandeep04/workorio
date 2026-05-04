<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TenantRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (!Schema::hasTable('roles')) {
            $this->command->warn('roles table not found, skipping roles seeding.');
            return;
        }

        $roles = [
            [
                'role_name' => 'admin',
                'description' => 'Administrator with full access',
                'is_custom' => false,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];
        
        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['role_name' => $role['role_name']],
                $role
            );
        }
        
        $this->command->info('✅ Seeded roles: admin');
    }
}
