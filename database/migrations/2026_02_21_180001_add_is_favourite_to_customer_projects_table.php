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
        // Skip this migration if running on master database
        if (Schema::getConnection()->getName() === 'mysql') {
            return;
        }

        Schema::table('customer_projects', function (Blueprint $table) {
            if (!Schema::hasColumn('customer_projects', 'is_favourite')) {
                $table->boolean('is_favourite')->default(0)->after('completed_percentage');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Skip this migration if running on master database
        if (Schema::getConnection()->getName() === 'mysql') {
            return;
        }

        Schema::table('customer_projects', function (Blueprint $table) {
            if (Schema::hasColumn('customer_projects', 'is_favourite')) {
                $table->dropColumn('is_favourite');
            }
        });
    }
};
