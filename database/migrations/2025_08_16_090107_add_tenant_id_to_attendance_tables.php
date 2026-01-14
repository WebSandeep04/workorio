<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

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
        
        // This migration is no longer needed for tenant databases
        // as tenant_id columns have been removed from tenant tables
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('movements', function (Blueprint $table) {
        $table->dropForeign(['tenant_id']);
        $table->dropColumn('tenant_id');
        });

        Schema::table('attendance', function (Blueprint $table) {
        $table->dropForeign(['tenant_id']);
        $table->dropColumn('tenant_id');
        });
    }
};
