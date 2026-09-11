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
        Schema::table('shifts', function (Blueprint $table) {
            $table->dropColumn(['penalty_eligible_days', 'penalty_deduction_days']);
        });
    }
};
