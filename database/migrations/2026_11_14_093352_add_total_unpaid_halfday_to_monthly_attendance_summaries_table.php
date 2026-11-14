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
        if (Schema::getConnection()->getName() === 'mysql') { return; }

        Schema::table('monthly_attendance_summaries', function (Blueprint $table) {
            if (!Schema::hasColumn('monthly_attendance_summaries', 'total_unpaid_halfday')) {
                $table->decimal('total_unpaid_halfday', 5, 1)->default(0)->after('total_halfday');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::getConnection()->getName() === 'mysql') { return; }

        Schema::table('monthly_attendance_summaries', function (Blueprint $table) {
            if (Schema::hasColumn('monthly_attendance_summaries', 'total_unpaid_halfday')) {
                $table->dropColumn('total_unpaid_halfday');
            }
        });
    }
};
