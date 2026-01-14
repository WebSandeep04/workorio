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
        
        Schema::create('worklogs', function (Blueprint $table) {
            $table->id();
            $table->date('work_date');
            $table->unsignedBigInteger('entry_type_id');
            $table->string('entry_type_name')->nullable();
            $table->unsignedBigInteger('customer_id');
            $table->string('customer_name')->nullable();
            $table->unsignedBigInteger('service_id');
            $table->string('service_name')->nullable();
            $table->unsignedBigInteger('customer_project_id')->nullable();
            $table->string('customer_project_name')->nullable();
            $table->unsignedBigInteger('module_id');
            $table->string('module_name')->nullable();
            $table->integer('hours');
            $table->integer('minutes');
            $table->text('description');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
            
            $table->foreign('entry_type_id')->references('id')->on('entry_types')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
            $table->foreign('customer_project_id')->references('id')->on('customer_projects')->onDelete('set null');
            $table->foreign('module_id')->references('id')->on('modules')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            // Prevent duplicate entries
            $table->unique(['work_date', 'entry_type_id', 'customer_id', 'service_id', 'module_id', 'user_id', 'description'], 'unique_worklog_entry');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('worklogs');
    }
};
