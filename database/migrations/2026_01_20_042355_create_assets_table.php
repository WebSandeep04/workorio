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
        // Skip this migration if running on master database
        if (Schema::getConnection()->getName() === 'mysql') {
            return;
        }

        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('asset_id')->unique(); // User entered custom ID
            $table->string('name');
            $table->foreignId('asset_category_id')->constrained()->onDelete('cascade');
            $table->json('custom_fields_data')->nullable(); // Stores key-value pairs of custom fields
            $table->string('status'); // e.g., Available, Assigned, Damaged, Lost
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
