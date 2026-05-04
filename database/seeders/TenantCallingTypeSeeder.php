<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TenantCallingTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (!Schema::hasTable('calling_types')) {
            $this->command->warn('calling_types table not found, skipping calling types seeding.');
            return;
        }

        $callingTypes = [
            ['name' => 'Cold'],
            ['name' => 'Call Not Picked/Dissconnect'],
            ['name' => "Don't Call Again"],
            ['name' => 'Not Interested'],
            ['name' => 'Interested'],
            ['name' => 'Sent Details'],
            ['name' => 'Junk'],
        ];
        
        foreach ($callingTypes as $type) {
            DB::table('calling_types')->updateOrInsert(
                ['name' => $type['name']],
                array_merge($type, ['created_at' => now(), 'updated_at' => now()])
            );
        }
        
        $this->command->info('✅ Seeded calling types.');        
    }
}
