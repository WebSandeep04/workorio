<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use App\Models\TenantDatabase;
use App\Services\TenantDatabaseManager;
use App\Services\TenantDatabaseService;

class ListTenantDatabases extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'tenant:list';

    /**
     * The console command description.
     */
    protected $description = 'List all tenant databases and their status';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tenants = Tenant::all();
        $manager = new TenantDatabaseManager();
        
        if ($tenants->isEmpty()) {
            $this->info("No tenants found.");
            return 0;
        }
        
        $this->info("Tenant Databases Status:");
        $this->line("");
        
        $headers = ['ID', 'Tenant Name', 'Tenant Code', 'Database Exists', 'Tracked', 'Size (MB)', 'Status', 'Last Accessed'];
        $rows = [];
        
        foreach ($tenants as $tenant) {
            $dbExists = $manager->tenantDatabaseExists($tenant);
            $size = $dbExists ? $manager->getTenantDatabaseSize($tenant) : 'N/A';
            $status = $dbExists ? '✅ Active' : '❌ Not Created';
            
            // Check if tracked in tenant_databases table
            $tracked = TenantDatabase::where('tenant_id', $tenant->id)->exists();
            $lastAccessed = 'N/A';
            
            if ($tracked) {
                $tenantDb = TenantDatabase::where('tenant_id', $tenant->id)->first();
                $lastAccessed = $tenantDb->last_accessed_at ? $tenantDb->last_accessed_at->format('Y-m-d H:i') : 'Never';
                $status = $tenantDb->is_active ? '✅ Active' : '⚠️ Inactive';
            }
            
            $rows[] = [
                $tenant->id,
                $tenant->tenant_name,
                $tenant->tenant_code,
                $dbExists ? 'Yes' : 'No',
                $tracked ? 'Yes' : 'No',
                $size,
                $status,
                $lastAccessed
            ];
        }
        
        $this->table($headers, $rows);
        
        // Show statistics
        $stats = TenantDatabaseService::getDatabaseStats();
        $this->line("");
        $this->info("Database Statistics:");
        $this->line("Total Tracked: {$stats['total']}");
        $this->line("Active: {$stats['active']}");
        $this->line("Inactive: {$stats['inactive']}");
        
        return 0;
    }
}
