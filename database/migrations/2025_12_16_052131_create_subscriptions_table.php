<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Skip if running on master database (multi-tenant pattern in this app)
        if (Schema::getConnection()->getName() === 'mysql') {
            return;
        }

        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();

            // Core links
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('sales_record_id')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();

            // Basic subscription info
            $table->string('subscription_name')->nullable(); // Optional title
            $table->text('description')->nullable();
            $table->decimal('amount', 10, 2)->default(0);

            // Recurrence settings
            $table->boolean('is_recurring')->default(false);
            $table->string('recurrence_type')->nullable(); // daily|weekly|monthly|yearly|quaterly|halfyearly
            $table->unsignedInteger('alert_before_days')->nullable(); // days before next billing to alert
            $table->unsignedInteger('recurrence_interval')->nullable(); // every N units
            $table->json('recurrence_days_of_week')->nullable(); // ["mon","tue",...]
            $table->unsignedTinyInteger('recurrence_day_of_month')->nullable(); // optional for monthly
            $table->json('recurrence_months')->nullable(); // [1..12] for yearly
            $table->date('recurrence_end_date')->nullable();

            // Date & status
            $table->date('start_date');
            $table->date('end_date')->nullable(); // Auto-updated based on recurrence
            $table->date('next_billing_date')->nullable();
            $table->date('last_billed_date')->nullable();
            // Free-text status populated from subscription_status master table
            $table->string('status', 255)->nullable();
            $table->boolean('is_active')->default(true); // Active/Inactive flag
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('user_id')->nullable(); // Assigned user / owner
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
            $table->foreign('sales_record_id')->references('id')->on('sales_records')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');

            // Indexes
            $table->index(['customer_id', 'status']);
            $table->index('start_date');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        if (Schema::getConnection()->getName() === 'mysql') {
            return;
        }
        Schema::dropIfExists('subscriptions');
    }
};
