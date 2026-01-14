<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use App\Services\TenantDatabaseService;
use App\Services\TenantDatabaseManager;
use Illuminate\Support\Facades\Artisan;

class SeedTenantAdmins extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'tenant:seed-admins {--tenant= : Specific tenant ID to seed} {--force : Force seeding even if users exist}';

    /**
     * The console command description.
     */
    protected $description = 'Seed admin users in all tenant databases or a specific tenant';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $specificTenantId = $this->option('tenant');
        $force = $this->option('force');
        
        if ($specificTenantId) {
            $this->seedTenantAdmin($specificTenantId, $force);
        } else {
            $this->seedAllTenantAdmins($force);
        }
        
        return 0;
    }
    
    /**
     * Seed admin users in all tenant databases
     */
    private function seedAllTenantAdmins(bool $force = false): void
    {
        $tenants = Tenant::all();
        $manager = new TenantDatabaseManager();
        
        if ($tenants->isEmpty()) {
            $this->info("No tenants found.");
            return;
        }
        
        $this->info("Seeding admin users in all tenant databases...");
        $this->line("");
        
        $successCount = 0;
        $errorCount = 0;
        
        foreach ($tenants as $tenant) {
            $this->line("Processing tenant: {$tenant->tenant_name} (ID: {$tenant->id})");
            
            try {
                // Check if tenant database exists
                if (!$manager->tenantDatabaseExists($tenant)) {
                    $this->warn("  ⚠️ Database does not exist. Creating...");
                    
                    if ($manager->createTenantDatabase($tenant)) {
                        $this->info("  ✅ Database created successfully");
                    } else {
                        $this->error("  ❌ Failed to create database");
                        $errorCount++;
                        continue;
                    }
                }
                
                // Seed admin users
                if ($this->seedTenantAdmin($tenant->id, $force)) {
                    $successCount++;
                    $this->info("  ✅ Admin users seeded successfully");
                } else {
                    $errorCount++;
                    $this->error("  ❌ Failed to seed admin users");
                }
                
            } catch (\Exception $e) {
                $errorCount++;
                $this->error("  ❌ Error: " . $e->getMessage());
            }
            
            $this->line("");
        }
        
        // Summary
        $this->info("Seeding completed!");
        $this->line("✅ Success: {$successCount}");
        $this->line("❌ Errors: {$errorCount}");
    }
    
    /**
     * Seed admin users in a specific tenant database
     */
    private function seedTenantAdmin(int $tenantId, bool $force = false): bool
    {
        $tenant = Tenant::find($tenantId);
        
        if (!$tenant) {
            $this->error("Tenant with ID {$tenantId} not found.");
            return false;
        }
        
        $this->line("Seeding admin users for tenant: {$tenant->tenant_name} (ID: {$tenantId})");
        
        try {
            // Create connection if it doesn't exist
            if (!TenantDatabaseService::connectionExists($tenantId)) {
                TenantDatabaseService::createConnection($tenant);
            }
            
            // Set the connection for seeding
            $connectionName = TenantDatabaseService::getConnectionName($tenantId);
            
            // Run the TenantDataSeeder on the tenant database
            $exitCode = Artisan::call('db:seed', [
                '--database' => $connectionName,
                '--class' => 'TenantDataSeeder',
                '--force' => true,
            ]);
            
            if ($exitCode === 0) {
                $this->info("  ✅ Admin users seeded successfully");
                return true;
            } else {
                $this->error("  ❌ Seeding failed");
                return false;
            }
            
        } catch (\Exception $e) {
            $this->error("  ❌ Error: " . $e->getMessage());
            return false;
        }
    }
}