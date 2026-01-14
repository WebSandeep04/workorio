<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Skip this migration if running on master database
        if (Schema::getConnection()->getName() === 'mysql') {
            return;
        }
        
        Schema::create('customer_project_users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_project_id');
            $table->unsignedBigInteger('user_id');
            $table->integer('days_allocated')->default(0); // number of days required for this user
            $table->timestamps();

            $table->foreign('customer_project_id')->references('id')->on('customer_projects')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_project_users');
    }
};


