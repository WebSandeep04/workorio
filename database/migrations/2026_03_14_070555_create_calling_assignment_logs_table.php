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
        Schema::create('calling_assignment_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('calling_id')->index();
            $table->unsignedBigInteger('calling_campaign_id')->nullable()->index();
            $table->unsignedBigInteger('from_user_id')->nullable();
            $table->unsignedBigInteger('to_user_id');
            $table->unsignedBigInteger('assigned_by');
            $table->string('remark')->nullable();
            $table->timestamps();

            // Note: Since this application might handle relationships differently or use multiple databases,
            // we're avoiding strict foreign key constraints for flexibility, similar to other logs.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calling_assignment_logs');
    }
};
