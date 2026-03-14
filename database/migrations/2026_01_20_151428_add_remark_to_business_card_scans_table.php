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
        Schema::table('business_card_scans', function (Blueprint $table) {
            $table->text('remark')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('business_card_scans', function (Blueprint $table) {
            $table->dropColumn('remark');
        });
    }
};
