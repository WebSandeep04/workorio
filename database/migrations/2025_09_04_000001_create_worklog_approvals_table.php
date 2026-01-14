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
        
        Schema::create('worklog_approvals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('worklog_id');
            $table->unsignedBigInteger('approved_by');
            $table->enum('status', ['approved', 'rejected']);
            $table->enum('rating', ['met', 'below', 'exceeded'])->nullable();
            $table->text('remark')->nullable();
            $table->timestamps();

            $table->foreign('worklog_id')->references('id')->on('worklogs')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('worklog_approvals');
    }
};


