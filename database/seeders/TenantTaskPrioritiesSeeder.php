<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TenantTaskPrioritiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Guard against missing table
        if (!Schema::hasTable('task_priorities')) {
            $this->command->warn('task_priorities table not found, skipping task priorities seeding.');
            return;
        }

        $priorities = [
            [
                'name' => 'High',
                'color' => '#dc3545', // Red
                'order' => 1
            ],
            [
                'name' => 'Medium',
                'color' => '#ffc107', // Yellow
                'order' => 2
            ],
            [
                'name' => 'Low',
                'color' => '#28a745', // Green
                'order' => 3
            ]
        ];

        foreach ($priorities as $priority) {
            DB::table('task_priorities')->updateOrInsert(
                ['name' => $priority['name']],
                array_merge($priority, ['created_at' => now(), 'updated_at' => now()])
            );
        }

        $this->command->info('✅ Seeded task priorities: High, Medium, Low');
    }
}
