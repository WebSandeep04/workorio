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
        Schema::table('whatsapp_campaign_members', function (Blueprint $table) {
            $table->dropColumn('error_message');
        });

        Schema::table('whatsapp_campaigns', function (Blueprint $table) {
            $table->dropColumn('request_id');
        });
    }
};
