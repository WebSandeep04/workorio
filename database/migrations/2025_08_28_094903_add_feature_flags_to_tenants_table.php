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
        // Skip this migration if running on tenant database
        if (Schema::getConnection()->getName() !== 'mysql') {
            return;
        }
        
        Schema::table('tenants', function (Blueprint $table) {
            $table->boolean('is_setup_enabled')->default(true)->after('tenant_code');
            $table->boolean('is_sales_enabled')->default(true)->after('is_setup_enabled');
            $table->boolean('is_worklog_enabled')->default(true)->after('is_sales_enabled');
            $table->boolean('is_attendance_enabled')->default(true)->after('is_worklog_enabled');
            $table->boolean('is_subscription_enabled')->default(true)->after('is_attendance_enabled');
            $table->boolean('is_document_management_enabled')->default(true)->after('is_subscription_enabled');
            $table->boolean('is_user_setup_enabled')->default(true)->after('is_document_management_enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn([
                'is_setup_enabled',
                'is_sales_enabled',
                'is_worklog_enabled',
                'is_attendance_enabled',
                'is_subscription_enabled',
                'is_document_management_enabled',
                'is_user_setup_enabled',
            ]);
        });
    }
};

