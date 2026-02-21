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

        Schema::create('customer_project_remarks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_project_id')->index();
            $table->text('remark')->nullable();
            
            // Foreign key constraint
            $table->foreign('customer_project_id')
                  ->references('id')
                  ->on('customer_projects')
                  ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Only run on tenant databases
        if (Schema::getConnection()->getName() === 'mysql') {
            return;
        }

        Schema::dropIfExists('customer_project_remarks');
    }
};
