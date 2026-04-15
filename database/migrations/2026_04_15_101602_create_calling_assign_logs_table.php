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
        Schema::dropIfExists('calling_assign_logs');
    }
};
