<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use App\Models\User as UserModel;
use App\Models\Role as RoleModel;

class TenantAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (!Schema::hasTable('users')) {
            $this->command->warn('users table not found, skipping admin user seeding.');
            return;
        }

        $connectionName = DB::getDefaultConnection();
        $tenantId = $this->extractTenantIdFromConnection($connectionName);
        
        if (!$tenantId) {
            $this->command->warn('Could not determine tenant ID from connection name, using 1 as default.');
            $tenantId = 1;
        }

        $adminRole = RoleModel::where('role_name', 'admin')->first();
        
        if (!$adminRole) {
            $this->command->error('Admin role not found. Please ensure roles are seeded first.');
            return;
        }
        
        $adminUser = UserModel::firstOrCreate(
            ['email' => 'admin@tenant' . $tenantId . '.com'],
            [
                'name' => 'Tenant Admin ' . $tenantId,
                'email' => 'admin@tenant' . $tenantId . '.com',
                'password' => Hash::make('admin123'),
                'role_id' => $adminRole->id,
                'is_worklog' => true,
                'is_manager' => null,
                'salary_per_month' => 50000,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        
        if ($adminUser->wasRecentlyCreated) {
            $this->command->info("✅ Created admin user: {$adminUser->name} ({$adminUser->email})");
        } else {
            $this->command->info("ℹ️ Admin user already exists: {$adminUser->name} ({$adminUser->email})");
        }
    }

    private function extractTenantIdFromConnection(string $connectionName): ?int
    {
        if (preg_match('/^tenant_(\d+)$/', $connectionName, $matches)) {
            return (int) $matches[1];
        }
        return null;
    }
}
