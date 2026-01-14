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
        
        Schema::create('remarks', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->date('remark_date');
            $table->text('remark');
            $table->unsignedBigInteger('sales_remark_id')->nullable();
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('sales_remark_id')
                ->references('id')->on('sales_records')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('remarks');
    }
};
