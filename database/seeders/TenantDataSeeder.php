<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TenantDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the current tenant from the connection name
        $connectionName = DB::getDefaultConnection();
        $tenantId = $this->extractTenantIdFromConnection($connectionName);
        
        if (!$tenantId) {
            $this->command->error('Could not determine tenant ID from connection name.');
            return;
        }
        
        $this->command->info("Seeding minimal data for tenant ID: {$tenantId}");
        
        // Call all individual tenant-specific seeders
        $this->call([
            TenantRoleSeeder::class,
            TenantSalesStatusSeeder::class,
            TenantLateReasonSeeder::class,
            TenantCountrySeeder::class,
            TenantStateCitySeeder::class,
            TenantAdminUserSeeder::class,
            TenantCallingTypeSeeder::class,
            TenantCallingCampaignSeeder::class,
            TenantCallingSeeder::class,
            TenantCallingRemarkSeeder::class,
            TenantSubscriptionStatusSeeder::class,
            TenantBranchDepartmentSeeder::class,
            TenantAssetStatusSeeder::class,
            TenantLeaveTypeSeeder::class,
            TenantTaskPrioritiesSeeder::class,
        ]);
        
        $this->command->info('Minimal tenant data seeded successfully.');
    }

    /**
     * Extract tenant ID from connection name (e.g., "tenant_1" -> 1)
     */
    private function extractTenantIdFromConnection(string $connectionName): ?int
    {
        if (preg_match('/^tenant_(\d+)$/', $connectionName, $matches)) {
            return (int) $matches[1];
        }
        return null;
    }
}
