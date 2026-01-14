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
        // Only run this migration on master database
        if (Schema::getConnection()->getName() !== 'mysql') {
            return;
        }
        
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id')->default(3)->after('password');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Only run this migration on master database
        if (Schema::getConnection()->getName() !== 'mysql') {
            return;
        }
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role_id');
        });
    }
};
