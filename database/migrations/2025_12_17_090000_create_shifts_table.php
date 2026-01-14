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
        // Skip on master connection (mysql) – only run for tenant DBs
        if (Schema::getConnection()->getName() === 'mysql') {
            return;
        }

        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Shift name
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedInteger('late_min')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::getConnection()->getName() === 'mysql') {
            return;
        }

        Schema::dropIfExists('shifts');
    }
};


