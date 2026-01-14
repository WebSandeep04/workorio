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
        // Skip this migration on master database; only create in tenant DBs
        if (Schema::getConnection()->getName() === 'mysql') {
            return;
        }

        Schema::create('callings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index();

            // Core details
            $table->unsignedBigInteger('calling_type_id')->nullable()->index();
            $table->unsignedBigInteger('status_id')->nullable()->index();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('address')->nullable();
            // Location
            $table->unsignedBigInteger('state_id')->nullable()->index();
            $table->unsignedBigInteger('city_id')->nullable()->index();
            // Dates
            $table->date('next_follow_up_date')->nullable();
            $table->unsignedBigInteger('is_locked')->default(0);
            // Foreign keys (if tables exist)
            // Note: calling_type_id foreign key will be added in a later migration after calling_types table is created
            $table->foreign('status_id')->references('id')->on('sales_status')->onDelete('set null');
            $table->foreign('state_id')->references('id')->on('states')->onDelete('set null');
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');

            $table->timestamps();
        });
    }   

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Only drop in tenant DBs
        if (Schema::getConnection()->getName() === 'mysql') {
            return;
        }

        Schema::dropIfExists('callings');
        
    }
};
