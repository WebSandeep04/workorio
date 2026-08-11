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
        Schema::table('loans', function (Blueprint $table) {
            $table->decimal('applied_interest_rate', 5, 2)->default(0)->after('amount');
            $table->decimal('total_interest', 10, 2)->default(0)->after('applied_interest_rate');
            $table->decimal('total_payable', 10, 2)->default(0)->after('total_interest');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropColumn(['applied_interest_rate', 'total_interest', 'total_payable']);
        });
    }
};
