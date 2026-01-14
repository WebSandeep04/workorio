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
        // Only run on master database
        if (Schema::getConnection()->getName() !== 'mysql') {
            return;
        }

        Schema::table('tenants', function (Blueprint $table) {
            if (!Schema::hasColumn('tenants', 'is_subscription_enabled')) {
                $table->boolean('is_subscription_enabled')->default(true)->after('is_attendance_enabled');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Only run on master database
        if (Schema::getConnection()->getName() !== 'mysql') {
            return;
        }

        Schema::table('tenants', function (Blueprint $table) {
            if (Schema::hasColumn('tenants', 'is_subscription_enabled')) {
                $table->dropColumn('is_subscription_enabled');
            }
        });
    }
};
