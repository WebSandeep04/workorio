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
        // Category 1: Tenant-Only Migration (Skip on Master DB)
        if (Schema::getConnection()->getName() === 'mysql') {
            return;
        }

        if (!Schema::hasTable('ai_settings')) {
            Schema::create('ai_settings', function (Blueprint $table) {
                $table->id();
                // Gemini Settings
                $table->boolean('is_gemini_byo_enabled')->default(false);
                $table->text('gemini_api_key')->nullable();

                // OpenAI Settings
                $table->boolean('is_openai_byo_enabled')->default(false);
                $table->text('openai_api_key')->nullable();

                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::getConnection()->getName() === 'mysql') {
            return;
        }

        Schema::dropIfExists('ai_settings');
    }
};
