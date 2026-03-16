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
        Schema::table('tenants', function (Blueprint $table) {
            if (!Schema::hasColumn('tenants', 'is_tally_calling_enabled')) {
                $table->boolean('is_tally_calling_enabled')->default(0);
            }
            if (!Schema::hasColumn('tenants', 'is_projects_enabled')) {
                $table->boolean('is_projects_enabled')->default(0);
            }
            if (!Schema::hasColumn('tenants', 'is_tracking_enabled')) {
                $table->boolean('is_tracking_enabled')->default(0);
            }
            if (!Schema::hasColumn('tenants', 'is_workflow_enabled')) {
                $table->boolean('is_workflow_enabled')->default(0);
            }
            if (!Schema::hasColumn('tenants', 'is_social_media_calendar_enabled')) {
                $table->boolean('is_social_media_calendar_enabled')->default(0);
            }
            if (!Schema::hasColumn('tenants', 'is_master_enabled')) {
                $table->boolean('is_master_enabled')->default(0);
            }
            if (!Schema::hasColumn('tenants', 'is_task_reminders_enabled')) {
                $table->boolean('is_task_reminders_enabled')->default(0);
            }
            if (!Schema::hasColumn('tenants', 'is_reports_enabled')) {
                $table->boolean('is_reports_enabled')->default(0);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $columns = [
                'is_tally_calling_enabled',
                'is_projects_enabled',
                'is_tracking_enabled',
                'is_workflow_enabled',
                'is_social_media_calendar_enabled',
                'is_master_enabled',
                'is_task_reminders_enabled',
                'is_reports_enabled'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('tenants', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
