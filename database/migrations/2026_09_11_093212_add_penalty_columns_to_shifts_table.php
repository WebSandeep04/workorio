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
        Schema::table('shifts', function (Blueprint $table) {
            $table->integer('penalty_eligible_days')->nullable()->after('extended_hr');
            $table->decimal('penalty_deduction_days', 8, 2)->nullable()->after('penalty_eligible_days');
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
        Schema::table('shifts', function (Blueprint $table) {
            $table->dropColumn(['penalty_eligible_days', 'penalty_deduction_days']);
        });
    }
};
