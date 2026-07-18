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
            $table->boolean('exempt_grace_on_overtime')->default(1)->after('grace_bounce_day');
            $table->boolean('enforce_time_restriction_on_overtime')->default(0)->after('exempt_grace_on_overtime');
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
            $table->dropColumn([
                'exempt_grace_on_overtime',
                'enforce_time_restriction_on_overtime'
            ]);
        });
    }
};
