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
        Schema::table('shifts', function (Blueprint $table) {
            $table->dropColumn([
                'min_per_month_late_allow',
                'is_grace_punish',
                'grace_bounce_day',
                'exempt_grace_on_overtime'
            ]);
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
        Schema::table('shifts', function (Blueprint $table) {
            $table->integer('min_per_month_late_allow')->default(0)->nullable();
            $table->boolean('is_grace_punish')->default(0);
            $table->integer('grace_bounce_day')->default(0)->nullable();
            $table->boolean('exempt_grace_on_overtime')->default(1);
        });
    }
};
