<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TenantLeaveTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (!Schema::hasTable('leave_types')) {
            $this->command->warn('leave_types table not found, skipping leave type seeding.');
            return;
        }

        $types = [
            [
                'name' => 'Short Leave (Permission)',
                'is_paid' => true,
                'is_deductible' => true,
                'is_short_leave' => true,
                'full_day_weight' => 1.0,
                'half_day_weight' => 1.0,
                'allow_half_day' => false,
                'quota_type' => 'monthly',
                'color_code' => '#f6c23e',
                'description' => 'Late leave for personal errands (2 hours).'
            ],
            [
                'name' => 'Restricted Holiday (RH)',
                'is_paid' => true,
                'is_deductible' => false,
                'is_restricted' => true,
                'full_day_weight' => 1.0,
                'half_day_weight' => 1.0,
                'allow_half_day' => false,
                'color_code' => '#36b9cc',
                'description' => 'Selection from optional holiday list.'
            ],
            [
                'name' => 'LWP (Leave Without Pay)',
                'is_paid' => false,
                'is_deductible' => false,
                'full_day_weight' => 1.0,
                'half_day_weight' => 0.5,
                'color_code' => '#858796',
                'description' => 'Unpaid leave.'
            ],
            [
                'name' => 'Holiday Working',
                'is_paid' => true,
                'is_deductible' => true,
                'full_day_weight' => 1.0,
                'half_day_weight' => 1.0,
                'allow_half_day' => false,
                'quota_type' => 'yearly',
                'color_code' => '#1cc88a',
                'description' => 'Compensatory Off for working on a holiday or weekly off.'
            ]
        ];

        foreach ($types as $type) {
            DB::table('leave_types')->updateOrInsert(
                ['name' => $type['name']],
                array_merge($type, ['created_at' => now(), 'updated_at' => now()])
            );
        }

        $this->command->info('✅ Seeded default leave types.');
    }
}
