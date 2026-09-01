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
        Schema::table('monthly_attendance_summaries', function (Blueprint $table) {
            $table->decimal('total_weekly_offs_worked', 5, 2)->default(0)->change();
            $table->decimal('total_holidays_worked', 5, 2)->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('monthly_attendance_summaries', function (Blueprint $table) {
            $table->integer('total_weekly_offs_worked')->default(0)->change();
            $table->integer('total_holidays_worked')->default(0)->change();
        });
    }
};
