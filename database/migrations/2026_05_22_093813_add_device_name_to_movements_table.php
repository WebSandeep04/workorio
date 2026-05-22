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
         // Skip this migration if running on master database (multi-tenant check)
        if (Schema::getConnection()->getName() === 'mysql') {
            return;
        }
        Schema::table('movements', function (Blueprint $table) {
            $table->string('device_name')->nullable()->after('mode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('movements', function (Blueprint $table) {
            $table->dropColumn('device_name');
        });
    }
};
