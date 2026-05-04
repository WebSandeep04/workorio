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
        
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_name');
            $table->string('tenant_code')->unique();

            // Menu features and flags
            $table->boolean('is_setup_enabled')->default(true);
            $table->boolean('is_sales_enabled')->default(true);
            $table->boolean('is_worklog_enabled')->default(true);
            $table->boolean('is_attendance_enabled')->default(true);
            $table->boolean('is_subscription_enabled')->default(true);
            $table->boolean('is_document_management_enabled')->default(true);
            $table->boolean('is_user_setup_enabled')->default(true);
            $table->boolean('is_sales_setup_enabled')->default(true);
            $table->boolean('is_work_setup_enabled')->default(true);
            $table->boolean('is_subs_setup_enabled')->default(true);
            $table->boolean('is_petty_cash_enable')->default(false);
            $table->boolean('is_approval_enabled')->default(true);
            $table->boolean('is_contact_management')->default(true);
            $table->boolean('is_asset_management_enable')->default(true);
            $table->boolean('is_email_marketing_enable')->default(true);
            $table->boolean('is_tally_calling_enabled')->default(false);
            $table->boolean('is_leadgen_enabled')->default(true);
            $table->boolean('is_projects_enabled')->default(false);
            $table->boolean('is_tracking_enabled')->default(false);
            $table->boolean('is_workflow_enabled')->default(false);
            $table->boolean('is_social_media_calendar_enabled')->default(false);
            $table->boolean('is_master_enabled')->default(false);
            $table->boolean('is_task_reminders_enabled')->default(false);
            $table->boolean('is_reports_enabled')->default(false);

            // Module specific setup dimensions
            $table->boolean('is_core_setup_enabled')->default(false);
            $table->boolean('is_tally_calling_setup_enabled')->default(false);
            $table->boolean('is_leadgen_setup_enabled')->default(false);
            $table->boolean('is_projects_setup_enabled')->default(false);
            $table->boolean('is_subscription_setup_enabled')->default(false);
            $table->boolean('is_tracking_setup_enabled')->default(false);
            $table->boolean('is_workflow_setup_enabled')->default(false);
            $table->boolean('is_calendar_setup_enabled')->default(false);
            $table->boolean('is_master_setup_enabled')->default(false);
            $table->boolean('is_task_setup_enabled')->default(false);
            $table->boolean('is_attendance_setup_enabled')->default(false);
            $table->boolean('is_reports_setup_enabled')->default(false);
            $table->boolean('is_document_setup_enabled')->default(false);
            $table->boolean('is_petty_cash_setup_enabled')->default(false);
            $table->boolean('is_contact_management_setup_enabled')->default(false);
            $table->boolean('is_asset_management_setup_enabled')->default(false);
            $table->boolean('is_email_marketing_setup_enabled')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
