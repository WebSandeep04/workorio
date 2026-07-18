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
        // Skip this migration if running on master database (multi-tenant check)
        if (Schema::getConnection()->getName() === 'mysql') {
            return;
        }

        Schema::table('shifts', function (Blueprint $table) {
            $table->boolean('is_grace_punish')->default(0)->after('min_per_month_late_allow');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::getConnection()->getName() === 'mysql') {
            return;
        }

        Schema::table('shifts', function (Blueprint $table) {
            $table->dropColumn('is_grace_punish');
        });
    }
};
