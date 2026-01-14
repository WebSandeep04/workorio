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

        if (!Schema::hasTable('calendar_date_client_statuses')) {
            Schema::create('calendar_date_client_statuses', function (Blueprint $table) {
                $table->id();
                $table->date('event_date');
                $table->unsignedBigInteger('client_id');
                $table->unsignedBigInteger('status_id')->nullable();
                $table->timestamps();

                $table->index(['event_date', 'client_id']);
                $table->unique(['event_date', 'client_id'], 'uniq_date_client_status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calendar_date_client_statuses');
    }
};

