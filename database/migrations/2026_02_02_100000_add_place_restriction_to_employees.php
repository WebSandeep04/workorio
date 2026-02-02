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

        Schema::table('employees', function (Blueprint $table) {
            $table->boolean('is_place_allowed')->default(false)->after('shift_id');
        });

        Schema::create('employee_attendance_places', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('place_id');
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('place_id')->references('id')->on('places')->onDelete('cascade');
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

        Schema::dropIfExists('employee_attendance_places');
        
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('is_place_allowed');
        });
    }
};
