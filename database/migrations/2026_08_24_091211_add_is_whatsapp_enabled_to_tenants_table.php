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
        // Skip this migration if NOT running on master database
        if (Schema::getConnection()->getName() !== 'mysql') {
            return;
        }
        Schema::table('tenants', function (Blueprint $table) {
            $table->boolean('is_whatsapp_enabled')->default(true)->after('is_sales_enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Skip this migration if NOT running on master database
        if (Schema::getConnection()->getName() !== 'mysql') {
            return;
        }
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn('is_whatsapp_enabled');
        });
    }
};
