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
        Schema::table('quotation_settings', function (Blueprint $table) {
            $table->string('template_name')->default('modern')->after('logo_path');
            $table->string('primary_color')->default('#434AFA')->after('template_name');
            $table->string('secondary_color')->default('#FF8C00')->after('primary_color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotation_settings', function (Blueprint $table) {
            $table->dropColumn(['template_name', 'primary_color', 'secondary_color']);
        });
    }
};
