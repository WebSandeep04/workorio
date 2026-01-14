<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use App\Services\TenantDatabaseService;
use Illuminate\Support\Facades\Artisan;

class MigrateTenant extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'tenant:migrate {tenant_id : The ID of the tenant} {--fresh : Run fresh migrations}';

    /**
     * The console command description.
     */
    protected $description = 'Run migrations on a specific tenant database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tenantId = $this->argument('tenant_id');
        $fresh = $this->option('fresh');
        
        $tenant = Tenant::find($tenantId);
        
        if (!$tenant) {
            $this->error("Tenant with ID {$tenantId} not found.");
            return 1;
        }
        
        $this->info("Running migrations for tenant: {$tenant->tenant_name} (Code: {$tenant->tenant_code})");
        
        // Create connection if it doesn't exist
        if (!TenantDatabaseService::connectionExists($tenant->id)) {
            TenantDatabaseService::createConnection($tenant);
        }
        
        $connectionName = TenantDatabaseService::getConnectionName($tenant->id);
        
        // Run migrations
        $command = $fresh ? 'migrate:fresh' : 'migrate';
        $params = [
            '--database' => $connectionName,
            '--force' => true,
        ];
        
        if ($fresh) {
            $params['--seed'] = true;
        }
        
        $this->info("Executing: php artisan {$command} --database={$connectionName} --force");
        
        $exitCode = Artisan::call($command, $params);
        
        if ($exitCode === 0) {
            $this->info("✅ Migrations completed successfully for tenant: {$tenant->tenant_name}");
        } else {
            $this->error("❌ Migrations failed for tenant: {$tenant->tenant_name}");
            return 1;
        }
        
        return 0;
    }
}
