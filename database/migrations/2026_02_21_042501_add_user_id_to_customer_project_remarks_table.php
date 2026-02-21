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

        Schema::table('customer_project_remarks', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('customer_project_id');
            
            // Foreign key to users table (tenant DB has its own users table)
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Only run on tenant databases
        if (Schema::getConnection()->getName() === 'mysql') {
            return;
        }

        Schema::table('customer_project_remarks', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
