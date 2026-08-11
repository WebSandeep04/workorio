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
        Schema::create('msg91_settings', function (Blueprint $table) {
            $table->id();
            $table->string('auth_key')->nullable();
            $table->string('whatsapp_number')->nullable();
            $table->string('whatsapp_namespace')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('msg91_settings');
    }
};
