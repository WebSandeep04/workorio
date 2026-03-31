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
        Schema::table('employment_types', function (Blueprint $table) {
            $table->integer('sl_allowed')->default(0)->after('rh_allowed')->comment('How many Short Leaves allowed per month');
        });
        
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->boolean('is_sl')->default(false)->after('is_rh');
            $table->time('start_time')->nullable()->after('end_date');
            $table->time('end_time')->nullable()->after('start_time');
            $table->string('sl_period')->nullable()->after('end_time')->comment('morning or evening');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employment_types', function (Blueprint $table) {
            $table->dropColumn('sl_allowed');
        });
        
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->dropColumn(['is_sl', 'start_time', 'end_time']);
        });
    }
};
