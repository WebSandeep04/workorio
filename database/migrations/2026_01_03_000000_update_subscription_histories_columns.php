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
        Schema::table('subscription_histories', function (Blueprint $table) {
            $table->renameColumn('start_date', 'period_start');
            $table->renameColumn('end_date', 'due_date');
        });

        Schema::table('subscription_histories', function (Blueprint $table) {
            $table->date('period_end')->nullable()->after('period_start');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscription_histories', function (Blueprint $table) {
            $table->renameColumn('period_start', 'start_date');
            $table->renameColumn('due_date', 'end_date');
            $table->dropColumn('period_end');
        });
    }
};
