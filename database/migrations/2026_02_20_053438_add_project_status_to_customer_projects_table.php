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
            $table->tinyInteger('project_status')->default(0)->comment('0=Pending, 1=In Progress, 2=Completed');
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
            $table->dropColumn('project_status');
        });
    }
};
