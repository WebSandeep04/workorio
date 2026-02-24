<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Skip this migration if running on a tenant connection
        if (Schema::getConnection()->getName() !== 'mysql') {
            return;
        }

        Schema::table('tenants', function (Blueprint $table) {
            $table->boolean('is_petty_cash_enable')->default(false)->after('is_document_management_enabled');
        });
    }

    public function down()
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn('is_petty_cash_enable');
        });
    }
};
