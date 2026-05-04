<?php

namespace App\Services;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use App\Models\Tenant;
use App\Models\TenantDatabase;

class TenantDatabaseService
{
    // =========================================================================
    // 1. Utility & Identification Methods
    // =========================================================================

    /**
     * Get the connection name for a specific tenant
     */
    public static function getConnectionName(int $tenantId): string
    {
        return "tenant_{$tenantId}";
    }

    /**
     * Get the database name for a tenant
     */
    public static function getDatabaseName(Tenant $tenant): string
    {
        // 1) If a database has already been tracked for this tenant, prefer that
        $tracked = TenantDatabase::where('tenant_id', $tenant->id)->value('database_name');
        if (!empty($tracked)) {
            return $tracked;
        }

        // 2) Otherwise, build a safe default using tenant_code
        // Ensure database name is MySQL-safe: letters, numbers, and underscores only
        $sanitizedCode = preg_replace('/[^a-zA-Z0-9_]/', '_', (string) $tenant->tenant_code);
        $sanitizedCode = strtolower($sanitizedCode);
        $baseName = "tenant_{$sanitizedCode}";

        // 3) Hostinger (and many shared hosts) prepend username_ to DB names.
        // Allow an optional TENANT_DB_PREFIX env (e.g., 'u123456_') to be prepended.
        $prefix = env('TENANT_DB_PREFIX', '');
        return $prefix !== '' ? ($prefix . $baseName) : $baseName;
    }

    /**
     * Check if a tenant database connection exists
     */
    public static function connectionExists(int $tenantId): bool
    {
        $connectionName = self::getConnectionName($tenantId);
        return Config::has("database.connections.{$connectionName}");
    }

    // =========================================================================
    // 2. Core Connection Lifecycle Methods
    // =========================================================================

    /**
     * Create a database connection for a specific tenant
     */
    public static function createConnection(Tenant $tenant): void
    {
        $connectionName = self::getConnectionName($tenant->id);
        $databaseName = self::getDatabaseName($tenant);

        // Optional per-tenant credentials (from tenant_databases table)
        // Ensure we query master for tenant_databases always
        $dbRow = TenantDatabase::on('mysql')->where('tenant_id', $tenant->id)->first();
        $username = env('DB_USERNAME', 'root');
        $password = env('DB_PASSWORD', '');
        $host     = env('DB_HOST', '127.0.0.1');
        $port     = env('DB_PORT', '3306');
        if ($dbRow) {
            // If these nullable columns exist, prefer them
            $username = $dbRow->db_username ?? $username;
            $password = $dbRow->db_password ?? $password;
            $host     = $dbRow->db_host ?? $host;
            $port     = $dbRow->db_port ?? $port;
        }
        
        Config::set("database.connections.{$connectionName}", [
            'driver' => 'mysql',
            'host' => $host,
            'port' => $port,
            'database' => $databaseName,
            'username' => $username,
            'password' => $password,
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                \PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ]);

        // Track connection in tenant_databases table
        self::trackConnection($tenant, $connectionName, $databaseName);
    }

    /**
     * Track database connection in tenant_databases table
     */
    public static function trackConnection(Tenant $tenant, string $connectionName, string $databaseName): void
    {
        // Use master database connection for tracking
        DB::connection('mysql')->table('tenant_databases')->updateOrInsert(
            ['tenant_id' => $tenant->id],
            [
                'database_name' => $databaseName,
                'connection_name' => $connectionName,
                'is_active' => true,
                'last_accessed_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    /**
     * Set the default connection for the current request
     */
    public static function setDefaultConnection(int $tenantId): void
    {
        // Ensure the connection exists; create it if missing
        if (!self::connectionExists($tenantId)) {
            $tenant = Tenant::find($tenantId);
            if ($tenant) {
                self::createConnection($tenant);
            }
        }
        $connectionName = self::getConnectionName($tenantId);
        Config::set('database.default', $connectionName);
        DB::setDefaultConnection($connectionName);
    }

    /**
     * Test if a tenant database connection is working
     */
    public static function testConnection(int $tenantId): bool
    {
        try {
            $connectionName = self::getConnectionName($tenantId);
            DB::connection($connectionName)->getPdo();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    // =========================================================================
    // 3. Maintenance Methods
    // =========================================================================

    /**
     * Update last accessed time for tenant database
     */
    public static function updateLastAccessed(int $tenantId): void
    {
        $tenantDb = TenantDatabase::where('tenant_id', $tenantId)->first();
        if ($tenantDb) {
            $tenantDb->updateLastAccessed();
        }
    }

    /**
     * Mark tenant database as inactive
     */
    public static function markInactive(int $tenantId): void
    {
        $tenantDb = TenantDatabase::where('tenant_id', $tenantId)->first();
        if ($tenantDb) {
            $tenantDb->markAsInactive();
        }
    }

    // =========================================================================
    // 4. Information / Statistics Methods
    // =========================================================================

    /**
     * Get tenant database info
     */
    public static function getTenantDatabaseInfo(int $tenantId)
    {
        return TenantDatabase::where('tenant_id', $tenantId)->first();
    }

    /**
     * Get all tenant connection names
     */
    public static function getAllTenantConnections(): array
    {
        $connections = [];
        $tenants = Tenant::all();
        
        foreach ($tenants as $tenant) {
            $connections[] = self::getConnectionName($tenant->id);
        }
        
        return $connections;
    }

    /**
     * Get all tracked tenant databases
     */
    public static function getAllTrackedDatabases()
    {
        return TenantDatabase::with('tenant')->get();
    }

    /**
     * Get database statistics
     */
    public static function getDatabaseStats()
    {
        $totalDatabases = TenantDatabase::count();
        $activeDatabases = TenantDatabase::where('is_active', true)->count();
        $inactiveDatabases = TenantDatabase::where('is_active', false)->count();
        
        return [
            'total' => $totalDatabases,
            'active' => $activeDatabases,
            'inactive' => $inactiveDatabases,
        ];
    }
}
