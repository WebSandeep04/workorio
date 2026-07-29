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

        Schema::table('salary_structures', function (Blueprint $table) {
            $table->dropColumn('salary_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (\Illuminate\Support\Facades\Schema::getConnection()->getName() === 'mysql') { return; }

        Schema::table('salary_structures', function (Blueprint $table) {
            $table->enum('salary_type', ['fixed', 'structured'])->default('structured');
        });
    }
};
