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
        Schema::table('payroll_penalties', function (Blueprint $table) {
            $table->integer('total_late_count')->default(0)->after('employee_id');
            $table->integer('exempted_late_count')->default(0)->after('total_late_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payroll_penalties', function (Blueprint $table) {
            $table->dropColumn(['total_late_count', 'exempted_late_count']);
        });
    }
};
