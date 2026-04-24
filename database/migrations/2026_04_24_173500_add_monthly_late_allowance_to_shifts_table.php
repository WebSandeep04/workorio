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

        Schema::table('shifts', function (Blueprint $table) {
            if (!Schema::hasColumn('shifts', 'min_per_month_late_allow')) {
                $table->integer('min_per_month_late_allow')->default(0)->after('late_min');
            }
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
            if (Schema::hasColumn('shifts', 'min_per_month_late_allow')) {
                $table->dropColumn('min_per_month_late_allow');
            }
        });
    }
};
