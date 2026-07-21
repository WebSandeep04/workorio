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

        Schema::create('monthly_attendance_summaries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->integer('month');
            $table->integer('year');
            $table->boolean('is_locked')->default(false);
            
            // Core Days
            $table->integer('total_working_days')->default(0);
            $table->integer('days_worked')->default(0);
            $table->integer('days_absent')->default(0);
            $table->decimal('attendance_percentage', 5, 2)->default(0);
            
            // Presents
            $table->integer('total_present_combined')->default(0);
            $table->integer('total_present')->default(0);
            $table->integer('total_halfday')->default(0);
            $table->integer('total_weekly_offs_worked')->default(0);
            $table->integer('total_holidays_worked')->default(0);
            
            // Leaves/Holidays
            $table->integer('days_on_leave')->default(0);
            $table->integer('total_unpaid_leaves')->default(0);
            $table->integer('total_short_leaves')->default(0);
            $table->integer('total_weekly_offs')->default(0);
            $table->integer('total_holidays')->default(0);
            
            // Hours
            $table->string('total_hours')->nullable();
            $table->string('total_office_hours')->nullable();
            $table->string('total_field_hours')->nullable();
            $table->string('total_break_time')->nullable();
            $table->string('avg_hours_per_day')->nullable();
            
            // Late/Exceptions
            $table->integer('total_less_8_30')->default(0);
            $table->integer('total_more_8_30')->default(0);
            $table->integer('late_count')->default(0);
            $table->integer('total_late_minutes')->default(0);
            
            // JSON Data
            $table->json('total_cycles')->nullable();
            $table->json('late_logs')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_attendance_summaries');
    }
};
