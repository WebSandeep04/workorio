<?php

namespace App\Services;

use App\Models\Tenant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Exception;

class TenantDatabaseManager
{
    /**
     * Create a complete database for a tenant
     */
    public function createTenantDatabase(Tenant $tenant): bool
    {
        try {
            // 1. Create the database
            $this->createDatabase($tenant);
            
            // 2. Create connection
            TenantDatabaseService::createConnection($tenant);
            
            // 3. Run migrations on tenant database
            $this->runMigrationsOnTenant($tenant);
            
            // 4. Seed tenant-specific data (includes admin users)
            $this->seedTenantData($tenant);
            
            Log::info("Successfully created database for tenant: {$tenant->tenant_name} (ID: {$tenant->id})");
            return true;
            
        } catch (Exception $e) {
            Log::error("Failed to create tenant database for {$tenant->tenant_name}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Create the physical database
     */
    private function createDatabase(Tenant $tenant): void
    {
        $databaseName = TenantDatabaseService::getDatabaseName($tenant);
        
        // Use the master connection to create the database
        DB::connection('mysql')->statement("CREATE DATABASE IF NOT EXISTS `{$databaseName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    }

    /**
     * Run all migrations on the tenant database
     */
    private function runMigrationsOnTenant(Tenant $tenant): void
    {
        $connectionName = TenantDatabaseService::getConnectionName($tenant->id);
        
        Artisan::call('migrate', [
            '--database' => $connectionName,
            '--force' => true,
            '--path' => 'database/migrations'
        ]);
    }

    /**
     * Seed tenant-specific data
     */
    private function seedTenantData(Tenant $tenant): void
    {
        $connectionName = TenantDatabaseService::getConnectionName($tenant->id);
        
        // Set the connection for seeding
        DB::setDefaultConnection($connectionName);
        
        // Run seeders
        Artisan::call('db:seed', [
            '--database' => $connectionName,
            '--force' => true,
            '--class' => 'TenantDataSeeder'
        ]);
        
        // Reset to default connection
        DB::setDefaultConnection('mysql');
    }

    /**
     * Migrate data from master database to tenant database
     */
    public function migrateTenantData(Tenant $tenant): bool
    {
        try {
            $connectionName = TenantDatabaseService::getConnectionName($tenant->id);
            
            // Ensure connection exists
            if (!TenantDatabaseService::connectionExists($tenant->id)) {
                TenantDatabaseService::createConnection($tenant);
            }
            
            // Test connection
            if (!TenantDatabaseService::testConnection($tenant->id)) {
                throw new Exception("Cannot connect to tenant database");
            }
            
            // Tables to migrate (excluding tenants table)
            $tables = [
                'users', 'roles', 'sales_records', 'sales_status', 'sales_lead_sources',
                'sales_products', 'sales_business_types', 'states', 'cities',
                'prospectuses', 'remarks', 'worklogs', 'worklog_approvals',
                'attendance', 'movements', 'leave_requests', 'leave_ledgers', 'leave_types', 'holidays',
                'customers', 'services', 'modules', 'customer_projects',
                'customer_project_modules', 'customer_project_users',
                'subscription_types', 'customer_subscriptions',
                'entry_types', 'password_reset_otps'
            ];
            
            $migratedTables = 0;
            foreach ($tables as $table) {
                try {
                    $this->migrateTableData($tenant, $table);
                    $migratedTables++;
                } catch (Exception $e) {
                    Log::warning("Failed to migrate table {$table} for tenant {$tenant->tenant_name}: " . $e->getMessage());
                }
            }
            
            Log::info("Successfully migrated data for tenant: {$tenant->tenant_name} ({$migratedTables} tables)");
            return true;
            
        } catch (Exception $e) {
            Log::error("Failed to migrate data for tenant {$tenant->tenant_name}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Migrate data for a specific table
     */
    private function migrateTableData(Tenant $tenant, string $tableName): void
    {
        $connectionName = TenantDatabaseService::getConnectionName($tenant->id);
        
        // Check if table exists in master database
        if (!Schema::connection('mysql')->hasTable($tableName)) {
            return;
        }
        
        // Check if table exists in tenant database
        if (!Schema::connection($connectionName)->hasTable($tableName)) {
            return;
        }
        
        // Get data from master database for this tenant
        $data = DB::connection('mysql')
            ->table($tableName)
            ->where('tenant_id', $tenant->id)
            ->get()
            ->toArray();
        
        if (empty($data)) {
            return;
        }
        
        // Insert data into tenant database
        foreach ($data as $row) {
            $rowArray = (array) $row;
            
            // Remove tenant_id from the data
            unset($rowArray['tenant_id']);
            
            // Insert into tenant database
            DB::connection($connectionName)
                ->table($tableName)
                ->insert($rowArray);
        }
    }

    /**
     * Drop a tenant database
     */
    public function dropTenantDatabase(Tenant $tenant): bool
    {
        try {
            $databaseName = TenantDatabaseService::getDatabaseName($tenant);
            
            DB::connection('mysql')->statement("DROP DATABASE IF EXISTS `{$databaseName}`");
            
            Log::info("Successfully dropped database for tenant: {$tenant->tenant_name}");
            return true;
            
        } catch (Exception $e) {
            Log::error("Failed to drop database for tenant {$tenant->tenant_name}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if tenant database exists
     */
    public function tenantDatabaseExists(Tenant $tenant): bool
    {
        $databaseName = TenantDatabaseService::getDatabaseName($tenant);
        
        $result = DB::connection('mysql')
            ->select("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?", [$databaseName]);
        
        return !empty($result);
    }

    /**
     * Get tenant database size
     */
    public function getTenantDatabaseSize(Tenant $tenant): string
    {
        $databaseName = TenantDatabaseService::getDatabaseName($tenant);
        
        $result = DB::connection('mysql')
            ->select("
                SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'DB Size in MB'
                FROM information_schema.tables 
                WHERE table_schema = ?
            ", [$databaseName]);
        
        return $result[0]->{'DB Size in MB'} ?? '0.00';
    }
}
