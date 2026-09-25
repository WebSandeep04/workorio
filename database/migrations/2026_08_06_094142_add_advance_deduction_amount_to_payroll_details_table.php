<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Skip this migration if running on master database
        if (Schema::getConnection()->getName() === 'mysql') {
            return;
        }
        Schema::table('payroll_details', function (Blueprint $table) {
            $table->decimal('advance_deduction_amount', 15, 2)->default(0)->after('loan_deduction_amount');
        });
    }

    public function down(): void
    {
        // Skip this migration if running on master database
        if (Schema::getConnection()->getName() === 'mysql') {
            return;
        }
        Schema::table('payroll_details', function (Blueprint $table) {
            $table->dropColumn('advance_deduction_amount');
        });
    }
};
