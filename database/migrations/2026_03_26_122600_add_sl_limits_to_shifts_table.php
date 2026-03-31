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
            $table->integer('sl_start_limit')->default(0)->after('end_time')->comment('Hours limit after shift start for morning SL');
            $table->integer('sl_end_limit')->default(0)->after('sl_start_limit')->comment('Hours limit before shift end for evening SL');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shifts', function (Blueprint $table) {
            $table->dropColumn(['sl_start_limit', 'sl_end_limit']);
        });
    }
};
