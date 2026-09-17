<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AiSchedulerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\AiSchedulerConfig::firstOrCreate([], [
            'office_start_time' => '10:00:00',
            'office_end_time' => '19:00:00',
            'office_day_frequency' => 60,
            'office_day_lookback' => 180,
            'off_day_frequency' => 240,
            'off_day_lookback' => 360,
            'week_offs' => [0], // Sunday
        ]);

        $passes = [
            ['name' => 'Pre-office sweep', 'run_at' => '09:45:00', 'lookback_minutes' => 720],
            ['name' => 'Office-open sweep', 'run_at' => '10:00:00', 'lookback_minutes' => 720],
            ['name' => 'Post-office-open sweep', 'run_at' => '10:30:00', 'lookback_minutes' => 60],
            ['name' => 'Closing sweep', 'run_at' => '19:30:00', 'lookback_minutes' => 60],
            ['name' => 'Late-evening pass', 'run_at' => '22:00:00', 'lookback_minutes' => 180],
            ['name' => 'Overnight pass', 'run_at' => '02:00:00', 'lookback_minutes' => 360],
        ];

        foreach ($passes as $pass) {
            \App\Models\AiSchedulerFixedPass::firstOrCreate(['name' => $pass['name']], $pass);
        }
    }
}
