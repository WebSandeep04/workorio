<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use App\Services\TenantDatabaseService;

class MigrateTenantTasks extends Command
{
    protected $signature = 'tenant:migrate-tasks';
    protected $description = 'Run tasks table migration on all tenant databases';

    public function handle()
    {
        $tenants = Tenant::all();

        if ($tenants->isEmpty()) {
            $this->error('No tenants found.');
            return 1;
        }

        foreach ($tenants as $tenant) {
            $this->info("Migrating tasks table for tenant: {$tenant->tenant_name} (ID: {$tenant->id})");

            try {
                // Create connection if it doesn't exist
                if (!TenantDatabaseService::connectionExists($tenant->id)) {
                    TenantDatabaseService::createConnection($tenant);
                }

                $connectionName = TenantDatabaseService::getConnectionName($tenant->id);

                // Run the specific migration on tenant database
                Artisan::call('migrate', [
                    '--database' => $connectionName,
                    '--path' => 'database/migrations/2025_10_01_112522_create_tasks_table.php',
                    '--force' => true
                ]);

                $this->info("✓ Successfully migrated tasks table for {$tenant->tenant_name}");

            } catch (\Exception $e) {
                $this->error("✗ Failed to migrate for {$tenant->tenant_name}: " . $e->getMessage());
            }
        }

        $this->info('Migration complete!');
        return 0;
    }
}
