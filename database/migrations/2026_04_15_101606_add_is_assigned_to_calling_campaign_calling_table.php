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

        Schema::table('calling_campaign_calling', function (Blueprint $table) {
            $table->boolean('is_assigned')->default(0)->after('calling_type_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (\Illuminate\Support\Facades\Schema::getConnection()->getName() === 'mysql') { return; }

        Schema::table('calling_campaign_calling', function (Blueprint $table) {
            $table->dropColumn('is_assigned');
        });
    }
};
