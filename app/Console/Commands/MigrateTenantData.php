<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use App\Services\TenantDatabaseManager;

class MigrateTenantData extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'tenant:migrate-data {tenant_id : The ID of the tenant}';

    /**
     * The console command description.
     */
    protected $description = 'Migrate data from master database to tenant database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tenantId = $this->argument('tenant_id');
        
        $tenant = Tenant::find($tenantId);
        
        if (!$tenant) {
            $this->error("Tenant with ID {$tenantId} not found.");
            return 1;
        }
        
        $this->info("Migrating data for tenant: {$tenant->tenant_name} (Code: {$tenant->tenant_code})");
        
        $manager = new TenantDatabaseManager();
        
        // Check if tenant database exists
        if (!$manager->tenantDatabaseExists($tenant)) {
            $this->error("Tenant database does not exist. Please create it first using: php artisan tenant:create-db {$tenantId}");
            return 1;
        }
        
        // Migrate data
        $this->info("Starting data migration...");
        
        if ($manager->migrateTenantData($tenant)) {
            $this->info("✅ Data migration completed successfully for tenant: {$tenant->tenant_name}");
            
            // Show database info
            $size = $manager->getTenantDatabaseSize($tenant);
            $this->info("Database size after migration: {$size} MB");
            
        } else {
            $this->error("❌ Data migration failed for tenant: {$tenant->tenant_name}");
            return 1;
        }
        
        return 0;
    }
}
