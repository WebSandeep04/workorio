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
        if (\Illuminate\Support\Facades\Schema::getConnection()->getName() === 'mysql') { return; }

        Schema::create('calling_assign_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('calling_id');
            $table->unsignedBigInteger('calling_campaign_id')->nullable();
            $table->unsignedBigInteger('sales_record_id')->nullable();
            $table->unsignedBigInteger('prospectus_id')->nullable();
            $table->unsignedBigInteger('assigned_by')->nullable();
            $table->unsignedBigInteger('assigned_to');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (\Illuminate\Support\Facades\Schema::getConnection()->getName() === 'mysql') { return; }

        Schema::dropIfExists('calling_assign_logs');
    }
};
