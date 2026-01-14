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

        if (!Schema::hasTable('calendar_date_client_posts')) {
            Schema::create('calendar_date_client_posts', function (Blueprint $table) {
                $table->id();
                $table->date('event_date');
                $table->unsignedBigInteger('client_id');
                $table->unsignedBigInteger('social_handle_id');
                $table->timestamps();

                $table->index(['event_date', 'client_id']);
                $table->unique(['event_date', 'client_id', 'social_handle_id'], 'uniq_date_client_handle');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calendar_date_client_posts');
    }
};

