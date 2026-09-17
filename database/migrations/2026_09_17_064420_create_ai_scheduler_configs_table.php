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
        Schema::create('ai_scheduler_configs', function (Blueprint $table) {
            $table->id();
            $table->time('office_start_time')->default('10:00:00');
            $table->time('office_end_time')->default('19:00:00');
            
            $table->integer('office_day_frequency')->default(60); // minutes
            $table->integer('office_day_lookback')->default(180); // minutes
            
            $table->integer('off_day_frequency')->default(240); // minutes
            $table->integer('off_day_lookback')->default(360); // minutes
            
            $table->json('week_offs')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_scheduler_configs');
    }
};
