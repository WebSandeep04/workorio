<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use App\Services\TenantDatabaseManager;

class CreateTenantDatabase extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'tenant:create-db {tenant_id : The ID of the tenant}';

    /**
     * The console command description.
     */
    protected $description = 'Create a separate database for a specific tenant';

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
        
        $this->info("Creating database for tenant: {$tenant->tenant_name} (Code: {$tenant->tenant_code})");
        
        $manager = new TenantDatabaseManager();
        
        // Check if database already exists
        if ($manager->tenantDatabaseExists($tenant)) {
            if (!$this->confirm("Database for tenant '{$tenant->tenant_name}' already exists. Do you want to recreate it?")) {
                $this->info("Operation cancelled.");
                return 0;
            }
            
            // Drop existing database
            $this->info("Dropping existing database...");
            $manager->dropTenantDatabase($tenant);
        }
        
        // Create new database
        $this->info("Creating new database...");
        if ($manager->createTenantDatabase($tenant)) {
            $this->info("✅ Database created successfully for tenant: {$tenant->tenant_name}");
            
            // Show database info
            $size = $manager->getTenantDatabaseSize($tenant);
            $this->info("Database size: {$size} MB");
            
        } else {
            $this->error("❌ Failed to create database for tenant: {$tenant->tenant_name}");
            return 1;
        }
        
        return 0;
    }
}
