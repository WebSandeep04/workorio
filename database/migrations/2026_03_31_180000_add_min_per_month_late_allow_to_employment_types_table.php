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
        Schema::table('employment_types', function (Blueprint $table) {
            $table->integer('min_per_month_late_allow')->default(0)->after('no_of_half_days')->comment('Maximum late minutes allowed per month');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employment_types', function (Blueprint $table) {
            $table->dropColumn('min_per_month_late_allow');
        });
    }
};
