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
        Schema::create('signal_whatsapp_msg', function (Blueprint $table) {
            $table->id();
            $table->string('sender', 255)->nullable()->default(null);
            $table->string('sender_phone', 50)->nullable()->default(null);
            $table->string('chat', 255)->nullable()->default(null);
            $table->text('message_text')->nullable()->default(null);
            $table->string('created_at', 100)->nullable()->default(null);
            $table->timestamp('inserted_at')->useCurrent();
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
        Schema::dropIfExists('signal_whatsapp_msg');
    }
};
