<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // This migration optimizes the master database by removing business data tables
        // that should only exist in tenant databases
        
        // Skip this migration if running on tenant database
        if (Schema::getConnection()->getName() !== 'mysql') {
            return;
        }
        // Tables to keep in master database (system tables only)
        $keepTables = [
        'tenants',
        'migrations',
        'password_reset_tokens',
        'personal_access_tokens',
        'cache',
        'cache_locks',
        'jobs',
        'job_batches',
        'failed_jobs',
        'sessions'
        ];
        
        // Tables to remove from master database (business data tables)
        $removeTables = [
        'users',
        'roles',
        'sales_records',
        'sales_status',
        'sales_lead_sources',
        'sales_products',
        'sales_business_types',
        'states',
        'cities',
        'prospectuses',
        'remarks',
        'customers',
        'services',
        'modules',
        'customer_projects',
        'customer_project_modules',
        'customer_project_users',
        'entry_types',
        'worklogs',
        'worklog_approvals',
        'attendance',
        'leaves',
        'holidays',
        'movements',
        'password_reset_otps',
        'subscription_types',
        'customer_subscriptions'
        ];
        
        // Get all current tables
        $currentTables = collect(DB::select('SHOW TABLES'))
        ->map(function ($table) {
        return array_values((array)$table)[0];
        })
        ->toArray();
        
        // Remove business data tables from master database
        foreach ($removeTables as $table) {
        if (in_array($table, $currentTables)) {
        try {
        Schema::dropIfExists($table);
        echo "Dropped table: {$table}\n";
        } catch (Exception $e) {
        echo "Error dropping table {$table}: " . $e->getMessage() . "\n";
        }
        }
        }
        
        // Verify only system tables remain
        $remainingTables = collect(DB::select('SHOW TABLES'))
        ->map(function ($table) {
        return array_values((array)$table)[0];
        })
        ->toArray();
        
        echo "\nMaster database optimization completed!\n";
        echo "Tables remaining in master database:\n";
        foreach ($remainingTables as $table) {
        if (in_array($table, $keepTables)) {
        echo "✅ {$table} (system table)\n";
        } else {
        echo "⚠️  {$table} (unexpected table)\n";
        }
        }
        
        echo "\nExpected system tables:\n";
        foreach ($keepTables as $table) {
        if (in_array($table, $remainingTables)) {
        echo "✅ {$table}\n";
        } else {
        echo "❌ {$table} (missing)\n";
        }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration cannot be easily reversed as it drops business data tables
        // If you need to reverse this, you would need to:
        // 1. Restore from backup
        // 2. Or recreate all business tables with proper structure
        
        echo "This migration cannot be reversed automatically.\n";
        echo "To reverse: Restore from backup or recreate business tables.\n";
    }
};
