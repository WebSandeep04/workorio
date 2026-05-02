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

        Schema::create('employment_type_leave_rules', function (Blueprint $table) {
            $table->id();
            
            // It references the main app employment_types and the newly created leave_types
            $table->unsignedBigInteger('employment_type_id');
            $table->unsignedBigInteger('leave_type_id');
            
            $table->enum('generation_type', ['accrual', 'prefill', 'unlimited']);
            $table->integer('value'); // dynamic based on generation type
            $table->integer('max_use_per_month')->nullable();
            
            $table->boolean('carry_forward_allowed')->default(false);
            $table->integer('max_carry_forward')->default(0);
            $table->string('lapse_type')->nullable(); // monthly, yearly
            
            $table->timestamps();

            $table->foreign('leave_type_id')->references('id')->on('leave_types')->onDelete('cascade');
            
            // Since employment_types is likely in the master table or tenant table, 
            // if it's in the master table, standard foreign keys might fail across DB connections.
            // If it's tenant, uncomment:
            // $table->foreign('employment_type_id')->references('id')->on('employment_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employment_type_leave_rules');
    }
};
