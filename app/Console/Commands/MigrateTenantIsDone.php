<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use Illuminate\Support\Facades\Artisan;
use App\Services\TenantDatabaseService;

class MigrateTenantIsDone extends Command
{
    protected $signature = 'tenant:migrate-is-done';
    protected $description = 'Add is_done column to tasks table in all tenant databases';

    public function handle()
    {
        $tenants = Tenant::all();

        if ($tenants->isEmpty()) {
            $this->error('No tenants found.');
            return 1;
        }

        foreach ($tenants as $tenant) {
            $this->info("Migrating is_done column for tenant: {$tenant->tenant_name} (ID: {$tenant->id})");

            try {
                if (!TenantDatabaseService::connectionExists($tenant->id)) {
                    TenantDatabaseService::createConnection($tenant);
                }

                $connectionName = TenantDatabaseService::getConnectionName($tenant->id);

                Artisan::call('migrate', [
                    '--database' => $connectionName,
                    '--path' => 'database/migrations/2025_10_01_123745_add_is_done_to_tasks_table.php',
                    '--force' => true
                ]);

                $this->info("✓ Successfully migrated is_done column for {$tenant->tenant_name}");

            } catch (\Exception $e) {
                $this->error("✗ Failed to migrate for {$tenant->tenant_name}: " . $e->getMessage());
            }
        }

        $this->info('Migration complete!');
        return 0;
    }
}
