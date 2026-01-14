<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Skip this migration if running on master database
        if (Schema::getConnection()->getName() === 'mysql') {
            return;
        }

        if (!Schema::hasTable('calendar_client_common_events')) {
            Schema::create('calendar_client_common_events', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('client_id');
                $table->unsignedBigInteger('common_event_id');
                $table->date('event_date');
                $table->timestamps();

                $table->index(['client_id']);
                $table->unique(['client_id','common_event_id','event_date'], 'uniq_client_common_event_date');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('calendar_client_common_events');
    }
};



