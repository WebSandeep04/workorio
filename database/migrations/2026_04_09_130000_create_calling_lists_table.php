<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::getConnection()->getName() === 'mysql') {
            return;
        }

        Schema::create('calling_lists', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('total_records')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        if (Schema::getConnection()->getName() === 'mysql') {
            return;
        }
        Schema::dropIfExists('calling_lists');
    }
};
