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
        // Skip if running on master database
        if (Schema::getConnection()->getName() === 'mysql') {
            return;
        }

        Schema::table('subscriptions', function (Blueprint $table) {
            if (!Schema::hasColumn('subscriptions', 'billing_type')) {
                // adding billing_type column, default to 'Prepaid' or nullable as per preference. 
                // Let's make it nullable initially to avoid issues with existing data, 
                // but user request implies a choice. I'll make it nullable for safety.
                $table->string('billing_type')->nullable()->after('amount'); 
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::getConnection()->getName() === 'mysql') {
            return;
        }
        
        Schema::table('subscriptions', function (Blueprint $table) {
            if (Schema::hasColumn('subscriptions', 'billing_type')) {
                $table->dropColumn('billing_type');
            }
        });
    }
};
