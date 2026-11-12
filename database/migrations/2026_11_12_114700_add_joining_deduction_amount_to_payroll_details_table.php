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
            if (!Schema::hasColumn('payroll_details', 'joining_deduction_amount')) {
                $table->decimal('joining_deduction_amount', 12, 2)->default(0)->after('lop_deduction_amount');
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

        Schema::table('payroll_details', function (Blueprint $table) {
            if (Schema::hasColumn('payroll_details', 'joining_deduction_amount')) {
                $table->dropColumn('joining_deduction_amount');
            }
        });
    }
};

