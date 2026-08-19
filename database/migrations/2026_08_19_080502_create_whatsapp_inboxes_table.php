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
        Schema::create('whatsapp_inboxes', function (Blueprint $table) {
            $table->id();
            $table->string('sender_number');
            $table->string('receiver_number')->nullable();
            $table->text('message_text')->nullable();
            $table->string('media_url')->nullable();
            $table->string('message_type')->default('text'); // text, image, document
            $table->string('msg91_message_id')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('received_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_inboxes');
    }
};
