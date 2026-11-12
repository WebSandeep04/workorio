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
        // Skip migration on master db
        if (Schema::getConnection()->getName() === 'mysql') {
            return;
        }

        Schema::table('payroll_details', function (Blueprint $table) {
            if (!Schema::hasColumn('payroll_details', 'earned_gross_salary')) {
                $table->decimal('earned_gross_salary', 12, 2)->nullable()->after('gross_salary');
            }
        });

        Schema::table('payroll_component_details', function (Blueprint $table) {
            if (!Schema::hasColumn('payroll_component_details', 'earned_amount')) {
                $table->decimal('earned_amount', 12, 2)->nullable()->after('amount');
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
            if (Schema::hasColumn('payroll_details', 'earned_gross_salary')) {
                $table->dropColumn('earned_gross_salary');
            }
        });

        Schema::table('payroll_component_details', function (Blueprint $table) {
            if (Schema::hasColumn('payroll_component_details', 'earned_amount')) {
                $table->dropColumn('earned_amount');
            }
        });
    }
};

