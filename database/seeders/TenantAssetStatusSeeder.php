<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TenantAssetStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (!Schema::hasTable('asset_statuses')) {
            $this->command->warn('asset_statuses table not found, skipping asset status seeding.');
            return;
        }

        $statuses = [
            'Available',
            'Assigned',
            'Damaged',
            'Lost',
            'Under Maintenance',
            'Broken'
        ];

        foreach ($statuses as $status) {
            DB::table('asset_statuses')->updateOrInsert(
                ['name' => $status],
                [
                    'name' => $status,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        $this->command->info('✅ Seeded asset statuses.');
    }
}
