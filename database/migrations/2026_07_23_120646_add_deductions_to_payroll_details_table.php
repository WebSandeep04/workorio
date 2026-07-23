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
        Schema::table('payroll_details', function (Blueprint $table) {
            $table->decimal('total_deductions', 12, 2)->default(0)->after('net_salary');
            $table->decimal('lop_deduction_amount', 12, 2)->default(0)->after('total_deductions');
            $table->decimal('statutory_deduction_amount', 12, 2)->default(0)->after('lop_deduction_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payroll_details', function (Blueprint $table) {
            $table->dropColumn(['total_deductions', 'lop_deduction_amount', 'statutory_deduction_amount']);
        });
    }
};
