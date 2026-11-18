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
        if (!Schema::hasTable('system_email_alerts')) {
            Schema::create('system_email_alerts', function (Blueprint $table) {
                $table->id();
                $table->string('mail_code', 60)->unique();
                $table->string('name', 150);
                $table->string('category', 50); // Attendance, Timesheet, Tasks, Finance, Sales, Tele-Calling, Calendar, WhatsApp
                $table->string('default_time', 20); // e.g. '10:30 AM'
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('system_email_alert_recipients')) {
            Schema::create('system_email_alert_recipients', function (Blueprint $table) {
                $table->id();
                $table->string('mail_code', 60);
                $table->unsignedBigInteger('user_id');
                $table->timestamps();

                $table->foreign('mail_code')
                    ->references('mail_code')
                    ->on('system_email_alerts')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');

                $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');

                $table->unique(['mail_code', 'user_id'], 'uk_mail_code_user');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_email_alert_recipients');
        Schema::dropIfExists('system_email_alerts');
    }
};
