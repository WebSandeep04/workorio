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
        Schema::create('asset_assignment_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_assignment_id')->constrained()->onDelete('cascade');
            $table->foreignId('previous_employee_id')->constrained('employees')->onDelete('cascade');
            $table->foreignId('new_employee_id')->constrained('employees')->onDelete('cascade');
            $table->unsignedBigInteger('updated_by')->nullable(); // User ID
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_assignment_logs');
    }
};
