<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TenantDatabase;
use App\Models\TaskPriority;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class SeedTaskPriorities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:seed-priorities';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed task priorities for all tenant databases';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to seed task priorities for all tenant databases...');

        $tenantDatabases = TenantDatabase::where('is_active', true)->get();

        foreach ($tenantDatabases as $tenantDb) {
            $this->info("Seeding priorities for database: {$tenantDb->database_name} (ID: {$tenantDb->id})");

            // Get tenant database connection
            $dbName = $tenantDb->database_name;
            
            // Configure tenant database connection
            Config::set('database.connections.tenant', [
                'driver' => 'mysql',
                'host' => env('DB_HOST', '127.0.0.1'),
                'port' => env('DB_PORT', '3306'),
                'database' => $dbName,
                'username' => env('DB_USERNAME', 'root'),
                'password' => env('DB_PASSWORD', ''),
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
                'strict' => true,
            ]);

            // Reconnect to tenant database
            DB::purge('tenant');
            DB::reconnect('tenant');

            // Set the default connection temporarily
            Config::set('database.default', 'tenant');

            try {
                $priorities = [
                    [
                        'name' => 'High',
                        'color' => '#dc3545', // Red
                        'order' => 1
                    ],
                    [
                        'name' => 'Medium',
                        'color' => '#ffc107', // Yellow
                        'order' => 2
                    ],
                    [
                        'name' => 'Low',
                        'color' => '#28a745', // Green
                        'order' => 3
                    ]
                ];

                foreach ($priorities as $priority) {
                    TaskPriority::updateOrCreate(
                        ['name' => $priority['name']],
                        $priority
                    );
                }

                $this->info("✓ Successfully seeded priorities for {$tenantDb->database_name}");
            } catch (\Exception $e) {
                $this->error("✗ Error seeding priorities for {$tenantDb->database_name}: " . $e->getMessage());
            }

            // Reset to master database
            Config::set('database.default', 'mysql');
            DB::reconnect('mysql');
        }

        $this->info('Completed seeding task priorities for all tenants!');
        return 0;
    }
}
