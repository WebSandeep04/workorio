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

        Schema::table('payment_terms', function (Blueprint $table) {
            $table->text('terms_and_conditions')->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (\Illuminate\Support\Facades\Schema::getConnection()->getName() === 'mysql') { return; }

        Schema::table('payment_terms', function (Blueprint $table) {
            $table->dropColumn('terms_and_conditions');
        });
    }
};
