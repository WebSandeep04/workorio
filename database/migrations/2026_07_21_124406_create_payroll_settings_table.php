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
        if (\Illuminate\Support\Facades\Schema::getConnection()->getName() === 'mysql') { return; }

        Schema::create('payroll_settings', function (Blueprint $table) {
            $table->id();
            $table->integer('salary_cycle_start')->default(1);
            $table->integer('salary_cycle_end')->default(31);
            $table->boolean('attendance_based')->default(true);
            $table->boolean('pf_enabled')->default(false);
            $table->boolean('esi_enabled')->default(false);
            $table->boolean('pt_enabled')->default(false);
            $table->boolean('tds_enabled')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_settings');
    }
};
