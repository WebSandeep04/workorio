<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * This table is created in tenant databases to log all dispatched emails.
     */
    public function up(): void
    {
        if (!Schema::hasTable('system_email_logs')) {
            Schema::create('system_email_logs', function (Blueprint $table) {
                $table->id();
                $table->string('mail_code', 60)->index();
                $table->string('recipient_email', 255)->index();
                $table->string('recipient_name', 255)->nullable();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->string('subject', 255)->nullable();
                $table->enum('status', ['sent', 'failed'])->default('sent')->index();
                $table->text('error_message')->nullable();
                $table->json('meta')->nullable();
                $table->timestamp('sent_at')->useCurrent()->index();
                $table->timestamps();

                $table->index(['mail_code', 'sent_at']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_email_logs');
    }
};
