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
        Schema::table('signal_whatsapp_msg', function (Blueprint $table) {
            $table->boolean('is_ai_processed')->default(false)->after('message_text');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('signal_whatsapp_msg', function (Blueprint $table) {
            $table->dropColumn('is_ai_processed');
        });
    }
};
