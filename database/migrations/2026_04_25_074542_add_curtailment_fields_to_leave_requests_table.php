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
         if (Schema::getConnection()->getName() === 'mysql') {
            return;
        }
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->boolean('has_attendance_overlap')->default(false)->after('status');
            $table->date('resumed_at')->nullable()->after('has_attendance_overlap');
            $table->boolean('is_early_return')->default(false)->after('resumed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->dropColumn(['has_attendance_overlap', 'resumed_at', 'is_early_return']);
        });
    }
};
