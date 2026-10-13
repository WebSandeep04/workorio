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
        // Category 2: Master-Only Migration
        if (Schema::getConnection()->getName() !== 'mysql') {
            return;
        }

        if (!Schema::hasTable('map_api_usages')) {
            Schema::create('map_api_usages', function (Blueprint $table) {
                $table->id();
                $table->string('provider'); // 'google', 'ola', 'osm'
                $table->unsignedBigInteger('request_count')->default(0);
                $table->date('billing_cycle_start');
                $table->timestamps();

                $table->unique(['provider', 'billing_cycle_start']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::getConnection()->getName() !== 'mysql') {
            return;
        }

        Schema::dropIfExists('map_api_usages');
    }
};
