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

        if (!Schema::hasTable('calendar_date_client_checklist_options')) {
            Schema::create('calendar_date_client_checklist_options', function (Blueprint $table) {
                $table->id();
                $table->date('event_date');
                $table->unsignedBigInteger('client_id');
                $table->unsignedBigInteger('checklist_id');
                $table->unsignedBigInteger('option_id');
                $table->boolean('is_done')->default(false);
                $table->timestamps();

                $table->index(['event_date', 'client_id'], 'idx_date_client');
                $table->unique(['event_date','client_id','checklist_id','option_id'], 'uniq_date_client_checklist_option');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calendar_date_client_checklist_options');
    }
};

