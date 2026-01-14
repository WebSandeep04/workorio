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
            if (!Schema::hasColumn('tenants', 'is_user_setup_enabled')) {
                $table->boolean('is_user_setup_enabled')->default(true)->after('is_subs_setup_enabled');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            if (Schema::hasColumn('tenants', 'is_user_setup_enabled')) {
                $table->dropColumn('is_user_setup_enabled');
            }
        });
    }
};

