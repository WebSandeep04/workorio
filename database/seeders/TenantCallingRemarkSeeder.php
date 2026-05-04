<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TenantCallingRemarkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (!Schema::hasTable('calling_remarks') || !Schema::hasTable('calling_campaign_calling')) {
            $this->command->warn('calling_remarks or calling_campaign_calling table not found, skipping calling remarks seeding.');
            return;
        }

        $assignments = DB::table('calling_campaign_calling')->get();
        if ($assignments->isEmpty()) {
            return;
        }

        $phrases = [
            'Left voicemail, awaiting response',
            'Spoke to client; requested callback next week',
            'Interested in demo; send details via email',
            'Number unreachable; try again tomorrow',
            'Requested quotation; follow up in 2 days',
        ];

        $rows = [];
        foreach ($assignments as $asgn) {
            $n = rand(1, 2);
            for ($i = 0; $i < $n; $i++) {
                $rows[] = [
                    'calling_id' => $asgn->calling_id,
                    'calling_campaign_id' => $asgn->calling_campaign_id,
                    'user_id' => $asgn->user_id,
                    'remark' => $phrases[array_rand($phrases)],
                    'created_at' => now()->subDays(rand(0, 5)),
                    'updated_at' => now()->subDays(rand(0, 5)),
                ];
            }
        }

        if (!empty($rows)) {
            DB::table('calling_remarks')->insert($rows);
        }
        $this->command->info('✅ Seeded campaign-specific remarks.');
    }
}
