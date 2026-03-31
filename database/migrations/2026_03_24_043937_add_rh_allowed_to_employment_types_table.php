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
        Schema::table('employment_types', function (Blueprint $table) {
            $table->integer('rh_allowed')->default(0)->after('name')->comment('How many Restricted Holidays allowed');
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
        Schema::table('employment_types', function (Blueprint $table) {
            $table->dropColumn('rh_allowed');
        });
    }
};
