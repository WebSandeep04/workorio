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
        if (Schema::getConnection()->getName() === 'mysql') {
            return;
        }

        /*
        Schema::table('users', function (Blueprint $table) {
            $flags = [
                'is_core_setup',
                'is_sales_setup',
                'is_work_setup',
                'is_user_setup',
                'is_tally_calling_setup',
                'is_projects_setup',
                'is_subscription_setup',
                'is_tracking_setup',
                'is_workflow_setup',
                'is_calendar_setup',
                'is_master_setup',
                'is_task_setup',
                'is_attendance_setup',
                'is_reports_setup',
                'is_document_setup',
                'is_petty_cash_setup',
                'is_contact_management_setup',
                'is_asset_management_setup',
                'is_email_marketing_setup',
            ];

            foreach ($flags as $flag) {
                if (!Schema::hasColumn('users', $flag)) {
                    $table->boolean($flag)->default(0);
                }
            }
        });
        */
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::getConnection()->getName() === 'mysql') {
            return;
        }

        /*
        Schema::table('users', function (Blueprint $table) {
            $flags = [
                'is_core_setup',
                'is_sales_setup',
                'is_work_setup',
                'is_user_setup',
                'is_tally_calling_setup',
                'is_projects_setup',
                'is_subscription_setup',
                'is_tracking_setup',
                'is_workflow_setup',
                'is_calendar_setup',
                'is_master_setup',
                'is_task_setup',
                'is_attendance_setup',
                'is_reports_setup',
                'is_document_setup',
                'is_petty_cash_setup',
                'is_contact_management_setup',
                'is_asset_management_setup',
                'is_email_marketing_setup',
            ];

            foreach ($flags as $flag) {
                if (Schema::hasColumn('users', $flag)) {
                    $table->dropColumn($flag);
                }
            }
        });
        */
    }
};
