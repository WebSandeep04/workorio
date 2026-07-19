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
        if (Schema::getConnection()->getName() !== 'mysql') {
            Schema::table('employment_type_leave_rules', function (Blueprint $table) {
                $table->integer('eligibility_days')->default(0)->after('max_carry_forward');
                $table->decimal('halfday_count_value', 4, 2)->default(1.0)->after('eligibility_days');
            });
            Schema::table('leave_accrual_counters', function (Blueprint $table) {
                $table->decimal('valid_days_count', 8, 2)->default(0)->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::getConnection()->getName() !== 'mysql') {
            Schema::table('employment_type_leave_rules', function (Blueprint $table) {
                $table->dropColumn(['eligibility_days', 'halfday_count_value']);
            });
            Schema::table('leave_accrual_counters', function (Blueprint $table) {
                $table->integer('valid_days_count')->default(0)->change();
            });
        }
    }
};
