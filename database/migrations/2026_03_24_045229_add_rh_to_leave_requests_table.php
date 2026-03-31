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
            $table->unsignedBigInteger('leave_type_id')->nullable()->change();
            $table->boolean('is_rh')->default(false)->after('leave_type_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->dropColumn('is_rh');
            // Reverting change of nullable requires dropping foreign, changing, and adding foreign back, but usually not strictly needed for this task's scale down method.
            $table->unsignedBigInteger('leave_type_id')->nullable(false)->change();
        });
    }
};
