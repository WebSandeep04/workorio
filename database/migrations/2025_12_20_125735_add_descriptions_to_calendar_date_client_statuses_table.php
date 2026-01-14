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

        if (Schema::hasTable('calendar_date_client_statuses') && !Schema::hasColumn('calendar_date_client_statuses', 'descriptions')) {
            Schema::table('calendar_date_client_statuses', function (Blueprint $table) {
                $table->text('descriptions')->nullable()->after('missed_reason_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('calendar_date_client_statuses') && Schema::hasColumn('calendar_date_client_statuses', 'descriptions')) {
            Schema::table('calendar_date_client_statuses', function (Blueprint $table) {
                $table->dropColumn('descriptions');
            });
        }
    }
};
