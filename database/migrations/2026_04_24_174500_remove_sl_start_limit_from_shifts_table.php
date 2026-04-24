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
            if (Schema::hasColumn('shifts', 'sl_start_limit')) {
                $table->dropColumn('sl_start_limit');
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
            if (!Schema::hasColumn('shifts', 'sl_start_limit')) {
                $table->unsignedInteger('sl_start_limit')->default(0)->after('late_min');
            }
        });
    }
};
