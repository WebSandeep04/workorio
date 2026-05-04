<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TenantSubscriptionStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (!Schema::hasTable('subscription_status')) {
            $this->command->warn('subscription_status table not found, skipping subscription status seeding.');
            return;
        }

        $statuses = [
            ['status_name' => 'Pending'],
            ['status_name' => 'Payment Received']
        ];

        foreach ($statuses as $status) {
            DB::table('subscription_status')->updateOrInsert(
                ['status_name' => $status['status_name']],
                array_merge($status, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        $this->command->info('✅ Seeded subscription statuses: pending, payment received.');
    }
}
