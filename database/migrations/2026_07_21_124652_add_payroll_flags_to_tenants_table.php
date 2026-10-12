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
        if (\Illuminate\Support\Facades\Schema::getConnection()->getName() !== 'mysql') { return; }

        Schema::table('tenants', function (Blueprint $table) {
            if (!Schema::hasColumn('tenants', 'is_payroll_enabled')) {
                $table->boolean('is_payroll_enabled')->default(0);
            }
            if (!Schema::hasColumn('tenants', 'is_payroll_setup_enabled')) {
                $table->boolean('is_payroll_setup_enabled')->default(0);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (\Illuminate\Support\Facades\Schema::getConnection()->getName() !== 'mysql') { return; }

        Schema::table('tenants', function (Blueprint $table) {
            $dropCols = [];
            if (Schema::hasColumn('tenants', 'is_payroll_enabled')) {
                $dropCols[] = 'is_payroll_enabled';
            }
            if (Schema::hasColumn('tenants', 'is_payroll_setup_enabled')) {
                $dropCols[] = 'is_payroll_setup_enabled';
            }
            if (!empty($dropCols)) {
                $table->dropColumn($dropCols);
            }
        });
    }
};
