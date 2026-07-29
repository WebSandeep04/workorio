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
        if (\Illuminate\Support\Facades\Schema::getConnection()->getName() === 'mysql') { return; }

        
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'is_attendance')) {
                $table->boolean('is_attendance')->default(0)->after('is_task');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (\Illuminate\Support\Facades\Schema::getConnection()->getName() === 'mysql') { return; }

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'is_attendance')) {
                $table->dropColumn('is_attendance');
            }
        });
    }
};
