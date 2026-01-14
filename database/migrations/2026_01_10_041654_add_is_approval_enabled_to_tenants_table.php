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
        // Skip this migration if running on tenant database
        if (Schema::getConnection()->getName() !== 'mysql') {
            return;
        }

        Schema::table('tenants', function (Blueprint $table) {
            $table->boolean('is_approval_enabled')->default(true)->after('is_petty_cash_enable');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::getConnection()->getName() !== 'mysql') {
            return;
        }

        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn('is_approval_enabled');
        });
    }
};
