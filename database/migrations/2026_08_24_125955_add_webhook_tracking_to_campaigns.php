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
        Schema::table('whatsapp_campaigns', function (Blueprint $table) {
            $table->string('request_id')->nullable()->after('status');
        });

        Schema::table('whatsapp_campaign_members', function (Blueprint $table) {
            $table->text('error_message')->nullable()->after('status');
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
        Schema::table('whatsapp_campaign_members', function (Blueprint $table) {
            $table->dropColumn('error_message');
        });

        Schema::table('whatsapp_campaigns', function (Blueprint $table) {
            $table->dropColumn('request_id');
        });
    }
};
