<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TenantBranchDepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (!Schema::hasTable('branches') || !Schema::hasTable('departments')) {
            $this->command->warn('branches or departments table not found, skipping branch/department seeding.');
            return;
        }

        DB::table('branches')->updateOrInsert(
            ['code' => 'GN'],
            [
                'code' => 'GN',
                'name' => 'General',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $this->command->info('✅ Seeded default branch: General');

        $branchId = DB::table('branches')->where('code', 'GN')->value('id');

        if (!$branchId) {
            $this->command->error('General branch not found. Skipping department seeding.');
            return;
        }

        DB::table('departments')->updateOrInsert(
            ['code' => 'GEN', 'branch_id' => $branchId],
            [
                'branch_id' => $branchId,
                'code' => 'GEN',
                'name' => 'General',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $this->command->info('✅ Seeded default department: General');
    }
}
