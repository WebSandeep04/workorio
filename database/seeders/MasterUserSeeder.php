<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class MasterUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Only run on master database, skip for tenant databases
        if (DB::getDefaultConnection() !== 'mysql') {
            $this->command->info('Skipping MasterUserSeeder for tenant database');
            return;
        }

        // Create super admin user for master database
        DB::table('users')->updateOrInsert(
            ['email' => 'superadmin@master.com'],
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@master.com',
                'password' => Hash::make('admin123'),
                'role_id' => 3, // Super admin role
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $this->command->info('Super admin user created: superadmin@master.com / admin123');
    }
}
