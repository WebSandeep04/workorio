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

        if (!Schema::hasTable('calendar_client_social')) {
            Schema::create('calendar_client_social', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('client_id');
                $table->unsignedBigInteger('social_handle_id');
                $table->timestamps();
                $table->foreign('client_id')->references('id')->on('calendar_clients')->onDelete('cascade');
                $table->foreign('social_handle_id')->references('id')->on('calendar_social_handles')->onDelete('cascade');
                $table->unique(['client_id', 'social_handle_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calendar_client_social');
    }
};

