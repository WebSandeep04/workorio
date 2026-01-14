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
        
        Schema::create('prospectuses', function (Blueprint $table) {
            $table->id();
            $table->string('prospectus_name');
            $table->string('contact_person', 100)->nullable();
            $table->string('contact_number', 50)->nullable();
            $table->text('address')->nullable();
            $table->unsignedBigInteger('state_id')->nullable();
            $table->unsignedBigInteger('city_id')->nullable();
            $table->string('email', 100)->nullable();
            $table->unsignedBigInteger('business_type_id')->nullable();
            $table->string('website_link', 100)->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('state_id')->references('id')->on('states')->onDelete('set null');
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('set null');
            $table->foreign('business_type_id')->references('id')->on('sales_business_types')->onDelete('set null');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prospectuses');
    }
};


