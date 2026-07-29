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
        if (\Illuminate\Support\Facades\Schema::getConnection()->getName() === 'mysql') { return; }

        Schema::table('shifts', function (Blueprint $table) {
            $table->boolean('grant_comp_off_for_overtime')->default(true)->after('enforce_time_restriction_on_overtime');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (\Illuminate\Support\Facades\Schema::getConnection()->getName() === 'mysql') { return; }

        Schema::table('shifts', function (Blueprint $table) {
            $table->dropColumn('grant_comp_off_for_overtime');
        });
    }
};
