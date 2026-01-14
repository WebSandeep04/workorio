<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Keep behavior consistent with initial common_events migration:
        // skip on master "mysql" connection.
        if (Schema::getConnection()->getName() === 'mysql') {
            return;
        }

        if (Schema::hasTable('common_events') && !Schema::hasColumn('common_events', 'alert_before_days')) {
            Schema::table('common_events', function (Blueprint $table) {
                $table->unsignedSmallInteger('alert_before_days')
                      ->default(0)
                      ->after('is_active')
                      ->comment('Number of days before event to trigger alert');
            });
        }
    }

    public function down(): void
    {
        if (Schema::getConnection()->getName() === 'mysql') {
            return;
        }

        if (Schema::hasTable('common_events') && Schema::hasColumn('common_events', 'alert_before_days')) {
            Schema::table('common_events', function (Blueprint $table) {
                $table->dropColumn('alert_before_days');
            });
        }
    }
};


