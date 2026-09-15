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
        Schema::create('signal_ai_tasks', function (Blueprint $table) {
            $table->id();
            $table->integer('message_id')->nullable(); // referencing signal_whatsapp_msg.id which is INT
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('ai_assigned_to')->nullable();
            $table->string('ai_requested_by')->nullable();
            $table->string('status')->default('pending'); // pending, converted
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('signal_ai_tasks');
    }
};
