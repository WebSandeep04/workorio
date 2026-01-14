<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TaskPriority;

class TaskPrioritiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
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
            TaskPriority::updateOrCreate(
                ['name' => $priority['name']],
                $priority
            );
        }
    }
}
