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
        // Only create in tenant DBs
        if (Schema::getConnection()->getName() === 'mysql') {
            return;
        }

        Schema::create('calling_remarks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('calling_id')->index();
            $table->unsignedBigInteger('calling_campaign_id')->nullable()->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->text('remark')->nullable();

            // Foreign Keys
            $table->foreign('calling_id')->references('id')->on('callings')->onDelete('cascade');
            $table->foreign('calling_campaign_id')->references('id')->on('calling_campaigns')->onDelete('cascade');

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
        Schema::dropIfExists('calling_remarks');
    }
};


