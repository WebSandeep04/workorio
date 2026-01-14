<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Skip this migration if running on master database
        if (Schema::getConnection()->getName() === 'mysql') {
            return;
        }

        Schema::create('form_builder_submissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('form_builder_id');
            $table->json('data');
            $table->timestamps();

            $table->index('form_builder_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('form_builder_submissions');
    }
};



