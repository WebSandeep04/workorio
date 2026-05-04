<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TenantStateCitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (!Schema::hasTable('states') || !Schema::hasTable('cities')) {
            $this->command->warn('states or cities table not found, skipping states/cities seeding.');
            return;
        }

        $states = [
            ['state_name' => 'Maharashtra'],
            ['state_name' => 'Karnataka'],
            ['state_name' => 'Tamil Nadu'],
            ['state_name' => 'Gujarat'],
            ['state_name' => 'Rajasthan'],
            ['state_name' => 'Uttar Pradesh'],
            ['state_name' => 'West Bengal'],
            ['state_name' => 'Delhi'],
            ['state_name' => 'Punjab'],
            ['state_name' => 'Haryana']
        ];
        
        foreach ($states as $state) {
            $stateId = DB::table('states')->insertGetId(
                array_merge($state, ['created_at' => now(), 'updated_at' => now()])
            );
            
            $this->seedIndianCitiesForState($stateId, $state['state_name']);
        }
        
        $this->command->info('✅ Seeded Indian states and cities');
    }

    /**
     * Seed Indian cities for a specific state
     */
    private function seedIndianCitiesForState(int $stateId, string $stateName): void
    {
        $citiesByState = [
            'Maharashtra' => ['Mumbai', 'Pune', 'Nagpur', 'Nashik', 'Aurangabad'],
            'Karnataka' => ['Bangalore', 'Mysore', 'Hubli', 'Mangalore', 'Belgaum'],
            'Tamil Nadu' => ['Chennai', 'Coimbatore', 'Madurai', 'Tiruchirappalli', 'Salem'],
            'Gujarat' => ['Ahmedabad', 'Surat', 'Vadodara', 'Rajkot', 'Bhavnagar'],
            'Rajasthan' => ['Jaipur', 'Jodhpur', 'Udaipur', 'Kota', 'Ajmer'],
            'Uttar Pradesh' => ['Lucknow', 'Kanpur', 'Agra', 'Varanasi', 'Meerut'],
            'West Bengal' => ['Kolkata', 'Howrah', 'Durgapur', 'Asansol', 'Siliguri'],
            'Delhi' => ['New Delhi', 'Central Delhi', 'East Delhi', 'West Delhi', 'North Delhi'],
            'Punjab' => ['Chandigarh', 'Ludhiana', 'Amritsar', 'Jalandhar', 'Patiala'],
            'Haryana' => ['Gurgaon', 'Faridabad', 'Panipat', 'Karnal', 'Hisar']
        ];
        
        $cities = $citiesByState[$stateName] ?? [];
        
        foreach ($cities as $cityName) {
            DB::table('cities')->updateOrInsert(
                ['city_name' => $cityName, 'state_id' => $stateId],
                [
                    'city_name' => $cityName,
                    'state_id' => $stateId,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
        }
    }
}
