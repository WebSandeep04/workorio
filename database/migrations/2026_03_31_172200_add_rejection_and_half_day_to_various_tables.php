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

        /* 1. Add rejection reason to Attendance table */
        Schema::table('attendance', function (Blueprint $table) {
            if (!Schema::hasColumn('attendance', 'reject_reason')) {
                $table->text('reject_reason')->nullable()->after('is_approved');
            }
        });

        /* 2. Add rejection reason and half-day fields to Leave Requests table */
        Schema::table('leave_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('leave_requests', 'reject_reason')) {
                $table->text('reject_reason')->nullable()->after('status');
            }
            if (!Schema::hasColumn('leave_requests', 'is_half_day')) {
                $table->boolean('is_half_day')->default(false)->after('total_days');
            }
            if (!Schema::hasColumn('leave_requests', 'half_day_period')) {
                $table->string('half_day_period', 20)->nullable()->after('is_half_day');
            }
        });

        /* 3. Add half-day allowance to Employment Types table */
        Schema::table('employment_types', function (Blueprint $table) {
            if (!Schema::hasColumn('employment_types', 'no_of_half_days')) {
                // User specified AFTER sl_allowed
                $table->integer('no_of_half_days')->default(0)->after('sl_allowed');
            }
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

        Schema::table('attendance', function (Blueprint $table) {
            if (Schema::hasColumn('attendance', 'reject_reason')) {
                $table->dropColumn('reject_reason');
            }
        });

        Schema::table('leave_requests', function (Blueprint $table) {
            $columnsToDrop = [];
            if (Schema::hasColumn('leave_requests', 'reject_reason')) {
                $columnsToDrop[] = 'reject_reason';
            }
            if (Schema::hasColumn('leave_requests', 'is_half_day')) {
                $columnsToDrop[] = 'is_half_day';
            }
            if (Schema::hasColumn('leave_requests', 'half_day_period')) {
                $columnsToDrop[] = 'half_day_period';
            }
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });

        Schema::table('employment_types', function (Blueprint $table) {
            if (Schema::hasColumn('employment_types', 'no_of_half_days')) {
                $table->dropColumn('no_of_half_days');
            }
        });
    }
};
