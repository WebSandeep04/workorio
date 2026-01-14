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
        // Skip if on master database, as this is for tenant users
        if (Schema::getConnection()->getName() === 'mysql') {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            // Add columns if they don't exist
            if (!Schema::hasColumn('users', 'is_sales')) {
                $table->boolean('is_sales')->default(0)->after('is_manager');
            }
            if (!Schema::hasColumn('users', 'is_task')) {
                $table->boolean('is_task')->default(0)->after('is_sales');
            }
            if (!Schema::hasColumn('users', 'is_indiaMart')) {
                $table->boolean('is_indiaMart')->default(0)->after('is_task');
            }
            if (!Schema::hasColumn('users', 'is_calander')) {
                $table->boolean('is_calander')->default(0)->after('is_indiaMart');
            }
            if (!Schema::hasColumn('users', 'is_login')) {
                $table->boolean('is_login')->default(1)->after('is_calander');
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

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'is_sales', 
                'is_task', 
                'is_indiaMart', 
                'is_calander', 
                'is_login'
            ]);
        });
    }
};
