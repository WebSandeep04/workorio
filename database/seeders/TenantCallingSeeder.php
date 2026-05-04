<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\CallingCampaign;

class TenantCallingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (!Schema::hasTable('callings') || !Schema::hasTable('calling_campaign_calling')) {
            $this->command->warn('callings or calling_campaign_calling table not found, skipping calling seeding.');
            return;
        }

        $names = ['Aman','Rohit','Priya','Neha','Karan','Simran','Vivek','Pooja','Arjun','Sneha'];
        $states = ['Maharashtra', 'Karnataka', 'Gujarat', 'Delhi', 'Rajasthan'];
        $cities = ['Mumbai', 'Bangalore', 'Ahmedabad', 'New Delhi', 'Jaipur'];

        $campaignIds = CallingCampaign::pluck('id')->toArray();
        $adminUserId = DB::table('users')->value('id');

        for ($i=0; $i<10; $i++) {
            $callingId = DB::table('callings')->insertGetId([
                'name' => $names[$i],
                'email' => strtolower($names[$i]).$i.'@example.com',
                'phone' => '99999'.str_pad((string)$i, 5, '0', STR_PAD_LEFT),
                'address' => 'Sample address '.$i,
                'city' => $cities[array_rand($cities)],
                'state' => $states[array_rand($states)],
            ]);

            if (!empty($campaignIds) && $adminUserId) {
                $campaignId = $campaignIds[array_rand($campaignIds)];
                DB::table('calling_campaign_calling')->insert([
                    'calling_campaign_id' => $campaignId,
                    'calling_id' => $callingId,
                    'user_id' => $adminUserId,
                    'status' => 'Pending',
                    'next_followup_date' => now()->addDays(rand(1, 10))->toDateString(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command->info('✅ Seeded 10 calling contacts and linked them to campaigns.');
    }
}
