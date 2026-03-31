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
        if (Schema::getConnection()->getName() !== 'mysql') {
            return;
        }
        Schema::table('tenants', function (Blueprint $table) {
            $flags = [
                'is_core_setup_enabled',
                'is_tally_calling_setup_enabled',
                'is_projects_setup_enabled',
                'is_subscription_setup_enabled',
                'is_tracking_setup_enabled',
                'is_workflow_setup_enabled',
                'is_calendar_setup_enabled',
                'is_master_setup_enabled',
                'is_task_setup_enabled',
                'is_attendance_setup_enabled',
                'is_reports_setup_enabled',
                'is_document_setup_enabled',
                'is_petty_cash_setup_enabled',
                'is_contact_management_setup_enabled',
                'is_asset_management_setup_enabled',
                'is_email_marketing_setup_enabled',
            ];

            foreach ($flags as $flag) {
                if (!Schema::hasColumn('tenants', $flag)) {
                    $table->boolean($flag)->default(0);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $flags = [
                'is_core_setup_enabled',
                'is_tally_calling_setup_enabled',
                'is_projects_setup_enabled',
                'is_subscription_setup_enabled',
                'is_tracking_setup_enabled',
                'is_workflow_setup_enabled',
                'is_calendar_setup_enabled',
                'is_master_setup_enabled',
                'is_task_setup_enabled',
                'is_attendance_setup_enabled',
                'is_reports_setup_enabled',
                'is_document_setup_enabled',
                'is_petty_cash_setup_enabled',
                'is_contact_management_setup_enabled',
                'is_asset_management_setup_enabled',
                'is_email_marketing_setup_enabled',
            ];

            foreach ($flags as $flag) {
                if (Schema::hasColumn('tenants', $flag)) {
                    $table->dropColumn($flag);
                }
            }
        });
    }
};
