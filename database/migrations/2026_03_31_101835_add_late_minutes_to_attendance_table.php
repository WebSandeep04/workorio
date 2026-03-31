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
        if (Schema::getConnection()->getName() === 'mysql') {
            return;
        }
        Schema::table('attendance', function (Blueprint $table) {
            $table->integer('late_minutes')->default(0)->after('is_emergency')->comment('Total late minutes for the day if exceeded grace period');
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
        Schema::table('attendance', function (Blueprint $table) {
            $table->dropColumn('late_minutes');
        });
    }
};
