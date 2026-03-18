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

        Schema::create('leave_ledgers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('leave_type_id');
            $table->enum('transaction_type', ['credit', 'debit', 'lapsed', 'encashed']);
            $table->decimal('amount', 8, 2);
            $table->decimal('balance_after', 8, 2);
            $table->nullableMorphs('reference'); // For tracking what triggered it (e.g. LeaveRequest ID, DailyJob)
            $table->text('remarks')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('leave_type_id')->references('id')->on('leave_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_ledgers');
    }
};
