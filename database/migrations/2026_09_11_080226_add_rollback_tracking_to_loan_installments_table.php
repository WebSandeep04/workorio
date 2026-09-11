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
        Schema::table('loan_installments', function (Blueprint $table) {
            $table->unsignedBigInteger('payroll_id')->nullable()->after('status');
            $table->decimal('original_amount', 12, 2)->nullable()->after('amount');
            $table->boolean('is_system_generated')->default(false)->after('original_amount');

            $table->foreign('payroll_id')->references('id')->on('payrolls')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loan_installments', function (Blueprint $table) {
            $table->dropForeign(['payroll_id']);
            $table->dropColumn(['payroll_id', 'original_amount', 'is_system_generated']);
        });
    }
};
