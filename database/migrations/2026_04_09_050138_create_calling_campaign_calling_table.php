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
        if (Schema::getConnection()->getName() === 'mysql') {
            return;
        }

        Schema::create('calling_campaign_calling', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('calling_campaign_id')->index();
            $table->unsignedBigInteger('calling_id')->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            
            $table->unsignedBigInteger('calling_type_id')->nullable()->index();
            $table->date('next_followup_date')->nullable();
            $table->boolean('is_locked')->default(0);

            $table->foreign('calling_type_id')
                  ->references('id')->on('calling_types')->onDelete('set null');

            $table->foreign('calling_campaign_id', 'fk_camp_id')
                  ->references('id')->on('calling_campaigns')->onDelete('cascade');
            $table->foreign('calling_id', 'fk_call_id')
                  ->references('id')->on('callings')->onDelete('cascade');
            $table->foreign('user_id', 'fk_agent_id')
                  ->references('id')->on('users')->onDelete('set null');

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
        Schema::dropIfExists('calling_campaign_calling');
    }
};
