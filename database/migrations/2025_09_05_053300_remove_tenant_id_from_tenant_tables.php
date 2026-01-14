<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Skip this migration if running on master database
        if (Schema::getConnection()->getName() === 'mysql') {
            return;
        }
        
        // This migration is for tenant databases only
        // It removes tenant_id columns from all tables since each tenant has their own database
        
        $tables = [
            'users',
            'roles',
            'sales_records',
            'customer_projects',
            'customer_project_modules',
            'customer_project_users',
            'worklogs',
            'worklog_approvals',
            'attendance',
            'leaves',
            'holidays',
            'entry_types',
            'modules',
            'services',
            'customers',
            'prospectuses',
            'followups',
            'remarks',
            'sales_status',
            'sales_lead_source',
            'sales_business_type',
            'sales_product',
            'states',
            'cities',
            'subscription_types',
            'customer_subscriptions',
            'movements',
            'password_reset_otps'
        ];
        
        foreach ($tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'tenant_id')) {
                // Get foreign key constraints for tenant_id
                $foreignKeys = DB::select(""
                    . "SELECT CONSTRAINT_NAME "
                    . "FROM information_schema.KEY_COLUMN_USAGE "
                    . "WHERE TABLE_SCHEMA = DATABASE() "
                    . "AND TABLE_NAME = ? "
                    . "AND COLUMN_NAME = 'tenant_id' "
                    . "AND REFERENCED_TABLE_NAME IS NOT NULL"
                    . "", [$table]);
                
                // Drop foreign key constraints first
                foreach ($foreignKeys as $fk) {
                    try {
                        DB::statement("ALTER TABLE `{$table}` DROP FOREIGN KEY `{$fk->CONSTRAINT_NAME}`");
                    } catch (\Exception $e) {
                        // Ignore if foreign key doesn't exist
                    }
                }
                
                // Then drop the column
                try {
                    Schema::table($table, function (Blueprint $table) {
                        $table->dropColumn('tenant_id');
                    });
                } catch (\Exception $e) {
                    // Ignore if column doesn't exist
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration cannot be reversed as it removes tenant_id columns
        // If you need to reverse this, you would need to recreate the entire database structure
    }
};
