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

        Schema::table('employee_salaries', function (Blueprint $table) {
            $table->json('component_breakdown')->nullable()->after('gross_salary');
            $table->boolean('is_pf_applicable')->default(true)->after('component_breakdown');
            $table->boolean('is_esi_applicable')->default(true)->after('is_pf_applicable');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::getConnection()->getName() === 'mysql') { return; }

        Schema::table('employee_salaries', function (Blueprint $table) {
            $table->dropColumn(['component_breakdown', 'is_pf_applicable', 'is_esi_applicable']);
        });
    }
};
