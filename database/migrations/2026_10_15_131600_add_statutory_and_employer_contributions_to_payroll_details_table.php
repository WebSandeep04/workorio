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
        // Skip this migration if running on master database
        if (Schema::getConnection()->getName() === 'mysql') {
            return;
        }

        Schema::table('payroll_details', function (Blueprint $table) {
            $table->decimal('pf_deduction_amount', 12, 2)->default(0)->after('statutory_deduction_amount');
            $table->decimal('esi_deduction_amount', 12, 2)->default(0)->after('pf_deduction_amount');
            $table->decimal('employer_pf_amount', 12, 2)->default(0)->after('esi_deduction_amount');
            $table->decimal('employer_esi_amount', 12, 2)->default(0)->after('employer_pf_amount');
            $table->decimal('employer_contribution_amount', 12, 2)->default(0)->after('employer_esi_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Skip this migration if running on master database
        if (Schema::getConnection()->getName() === 'mysql') {
            return;
        }

        Schema::table('payroll_details', function (Blueprint $table) {
            $table->dropColumn([
                'pf_deduction_amount',
                'esi_deduction_amount',
                'employer_pf_amount',
                'employer_esi_amount',
                'employer_contribution_amount',
            ]);
        });
    }
};
