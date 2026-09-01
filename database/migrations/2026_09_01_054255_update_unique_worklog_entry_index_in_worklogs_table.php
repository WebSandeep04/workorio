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
        // Skip this migration if running on master database
        if (Schema::getConnection()->getName() === 'mysql') {
            return;
        }

        Schema::table('worklogs', function (Blueprint $table) {
            $table->dropUnique('unique_worklog_entry');
            $table->unique(['work_date', 'entry_type_id', 'customer_id', 'customer_project_name', 'service_id', 'module_id', 'user_id', 'description'], 'unique_worklog_entry');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Skip this migration if running on master database
        if (Schema::getConnection()->getName() === 'mysql') {
            return;
        }

        Schema::table('worklogs', function (Blueprint $table) {
            $table->dropUnique('unique_worklog_entry');
            $table->unique(['work_date', 'entry_type_id', 'customer_id', 'service_id', 'module_id', 'user_id', 'description'], 'unique_worklog_entry');
        });
    }
};
