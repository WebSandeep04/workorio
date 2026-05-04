<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TenantLateReasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (!Schema::hasTable('late_reasons')) {
            $this->command->warn('late_reasons table not found, skipping late reasons seeding.');
            return;
        }

        $reasons = [
            'Other',
            'Traffic / transport delay',
            'Health-related',
            'Family emergency',
            'Work-related reason',
            'Personal planning issue',
        ];

        foreach ($reasons as $reason) {
            DB::table('late_reasons')->updateOrInsert(
                ['reason' => $reason],
                [
                    'reason' => $reason,
                    'active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        $this->command->info('✅ Seeded default late reasons.');
    }
}
