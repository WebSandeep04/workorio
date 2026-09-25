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
        Schema::table('loan_installments', function (Blueprint $table) {
            $table->decimal('principal_component', 10, 2)->default(0)->after('amount');
            $table->decimal('interest_component', 10, 2)->default(0)->after('principal_component');
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
        Schema::table('loan_installments', function (Blueprint $table) {
            $table->dropColumn(['principal_component', 'interest_component']);
        });
    }
};
