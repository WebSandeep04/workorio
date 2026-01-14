<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::getConnection()->getName() === 'mysql') {
            return;
        }
        Schema::table('subscriptions', function (Blueprint $table) {
            if (Schema::hasColumn('subscriptions', 'next_billing_date')) {
                $table->dropColumn('next_billing_date');
            }
            if (Schema::hasColumn('subscriptions', 'last_billed_date')) {
                $table->dropColumn('last_billed_date');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            if (!Schema::hasColumn('subscriptions', 'next_billing_date')) {
                $table->date('next_billing_date')->nullable();
            }
            if (!Schema::hasColumn('subscriptions', 'last_billed_date')) {
                $table->date('last_billed_date')->nullable();
            }
        });
    }
};
