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

        if (!Schema::hasTable('payment_terms') || !Schema::hasColumn('payment_terms', 'order')) {
            return;
        }

        Schema::table('payment_terms', function (Blueprint $table) {
            $table->dropColumn('order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('payment_terms') || Schema::hasColumn('payment_terms', 'order')) {
            return;
        }

        Schema::table('payment_terms', function (Blueprint $table) {
            $table->integer('order')->default(0);
        });
    }
};
