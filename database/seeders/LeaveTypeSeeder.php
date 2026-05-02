<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LeaveType;

class LeaveTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            [
                'name' => 'Casual Leave (CL)',
                'is_paid' => true,
                'is_deductible' => true,
                'full_day_weight' => 1.0,
                'half_day_weight' => 0.5,
                'color_code' => '#4e73df',
                'description' => 'General purpose leave.'
            ],
            [
                'name' => 'Sick Leave (SL)',
                'is_paid' => true,
                'is_deductible' => true,
                'full_day_weight' => 1.0,
                'half_day_weight' => 0.5,
                'color_code' => '#e74a3b',
                'description' => 'Leave for medical reasons.'
            ],
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
                'name' => 'Maternity Leave',
                'is_paid' => true,
                'is_deductible' => true,
                'full_day_weight' => 1.0,
                'half_day_weight' => 1.0,
                'allow_half_day' => false,
                'color_code' => '#f6823e',
                'description' => 'Maternity leave for female employees.'
            ],
            [
                'name' => 'LWP (Leave Without Pay)',
                'is_paid' => false,
                'is_deductible' => false,
                'full_day_weight' => 1.0,
                'half_day_weight' => 0.5,
                'color_code' => '#858796',
                'description' => 'Unpaid leave.'
            ]
        ];

        foreach ($types as $type) {
            LeaveType::updateOrCreate(['name' => $type['name']], $type);
        }
    }
}
