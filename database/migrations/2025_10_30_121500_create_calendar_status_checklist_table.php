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

        if (!Schema::hasTable('calendar_status_checklist')) {
            Schema::create('calendar_status_checklist', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('status_id');
                $table->unsignedBigInteger('checklist_id');
                $table->timestamps();

                $table->index(['status_id']);
                $table->index(['checklist_id']);
                $table->unique(['status_id', 'checklist_id'], 'uniq_status_checklist');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('calendar_status_checklist');
    }
};



