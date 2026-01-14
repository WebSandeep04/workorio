<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Skip this migration if running on master database
        if (Schema::getConnection()->getName() === 'mysql') {
            return;
        }

        Schema::create('advances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sales_record_id');
            $table->decimal('amount', 15, 2);
            $table->date('payment_date');
            $table->string('transaction_id')->unique()->nullable();
            $table->text('notes')->nullable();
            $table->date('next_followup_date')->nullable();
            $table->timestamps();

            $table->foreign('sales_record_id')->references('id')->on('sales_records')->onDelete('cascade');

            $table->index('sales_record_id');
            $table->index('payment_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('advances');
    }
};

