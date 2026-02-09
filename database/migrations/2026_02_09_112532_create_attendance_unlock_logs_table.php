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
        Schema::create('attendance_unlock_logs', function (Blueprint $table) {
            $table->id();
            $table->date('date'); // date on which attendance unlock
            $table->date('unlock_date'); // which attendance date unlcoked
            $table->text('reason')->nullable();
            $table->unsignedBigInteger('unlocked_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_unlock_logs');
    }
};
