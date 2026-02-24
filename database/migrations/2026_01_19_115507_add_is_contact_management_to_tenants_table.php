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
        // Skip this migration if running on a tenant connection
        if (Schema::getConnection()->getName() !== 'mysql') {
            return;
        }

        Schema::table('tenants', function (Blueprint $table) {
            $table->boolean('is_contact_management')->default(1)->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn('is_contact_management');
        });
    }
};
