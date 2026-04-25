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
        Schema::table('leave_types', function (Blueprint $table) {
            $table->boolean('is_deductible')->default(true)->after('is_paid');
            $table->boolean('is_short_leave')->default(false)->after('is_deductible');
            $table->boolean('is_restricted')->default(false)->after('is_short_leave');
            $table->decimal('full_day_weight', 8, 2)->default(1.00)->after('is_restricted');
            $table->decimal('half_day_weight', 8, 2)->default(0.50)->after('full_day_weight');
            $table->boolean('allow_half_day')->default(true)->after('half_day_weight');
            $table->string('quota_type')->default('yearly')->after('allow_half_day'); // yearly or monthly
            $table->text('description')->nullable()->after('quota_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leave_types', function (Blueprint $table) {
            $table->dropColumn([
                'is_deductible',
                'is_short_leave',
                'is_restricted',
                'full_day_weight',
                'half_day_weight',
                'allow_half_day',
                'quota_type',
                'description'
            ]);
        });
    }
};
