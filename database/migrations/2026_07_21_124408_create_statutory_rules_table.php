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

        Schema::create('statutory_rules', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['PF', 'ESI', 'PT', 'TDS']);
            $table->decimal('employee_rate', 5, 2)->nullable();
            $table->decimal('employer_rate', 5, 2)->nullable();
            $table->decimal('salary_limit', 10, 2)->nullable();
            $table->string('calculate_on')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('statutory_rules');
    }
};
