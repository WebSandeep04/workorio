<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TenantSalesStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (!Schema::hasTable('sales_status')) {
            $this->command->warn('sales_status table not found, skipping sales status seeding.');
            return;
        }

        $statuses = [
            ['status_name' => 'Close Won'],
            ['status_name' => 'Close Lost']
        ];
        
        foreach ($statuses as $status) {
            DB::table('sales_status')->updateOrInsert(
                ['status_name' => $status['status_name']],
                array_merge($status, ['created_at' => now(), 'updated_at' => now()])
            );
        }
        
        $this->command->info('✅ Seeded sales status: Close Won, Close Lost');
    }
}
