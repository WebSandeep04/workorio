<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\CallingCampaign;

class TenantCallingCampaignSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (!Schema::hasTable('calling_campaigns')) {
            $this->command->warn('calling_campaigns table not found, skipping calling campaigns seeding.');
            return;
        }

        $campaigns = ['General Sales', 'Diwali Offer 2024', 'Website Inquiries', 'Product Launch'];
        foreach ($campaigns as $name) {
            CallingCampaign::firstOrCreate(['name' => $name]);
        }
        $this->command->info('✅ Seeded calling campaigns.');
    }
}
