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

        if (!Schema::hasTable('calendar_event_client')) {
            Schema::create('calendar_event_client', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('event_id');
                $table->unsignedBigInteger('client_id');
                $table->timestamps();
                $table->foreign('event_id')->references('id')->on('calendar_events')->onDelete('cascade');
                $table->foreign('client_id')->references('id')->on('calendar_clients')->onDelete('cascade');
                $table->unique(['event_id', 'client_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calendar_event_client');
    }
};

