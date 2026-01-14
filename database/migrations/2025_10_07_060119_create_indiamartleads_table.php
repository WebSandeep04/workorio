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
        
        Schema::create('indiamartleads', function (Blueprint $table) {
            $table->id();
            
            // Unique identifier from IndiaMART
            $table->string('unique_query_id')->unique();
            
            // Query details
            $table->string('query_type')->nullable(); // W, P, B, etc.
            $table->timestamp('query_time')->nullable();
            $table->text('query_product_name')->nullable();
            $table->text('query_message')->nullable();
            $table->string('query_mcat_name')->nullable();
            
            // Sender details
            $table->string('sender_name')->nullable();
            $table->string('sender_mobile')->nullable();
            $table->string('sender_mobile_alt')->nullable();
            $table->string('sender_email')->nullable();
            $table->string('sender_email_alt')->nullable();
            $table->string('sender_company')->nullable();
            $table->text('sender_address')->nullable();
            $table->string('sender_city')->nullable();
            $table->string('sender_state')->nullable();
            $table->string('sender_pincode')->nullable();
            $table->string('sender_country_iso')->nullable();
            $table->string('sender_other_mobile')->nullable();
            $table->string('sender_phone')->nullable();
            $table->string('sender_phone_alt')->nullable();
            
            // Receiver details
            $table->string('receiver_mobile')->nullable();
            
            // Call details
            $table->string('call_duration')->nullable();
            
            // Additional fields
            $table->string('subject')->nullable();
            $table->string('company_name')->nullable();
            $table->string('product_name')->nullable();
            $table->string('district')->nullable();
            $table->string('org_sender_glusr_usr_id')->nullable();
            $table->string('enrichment_id')->nullable();
            $table->string('im_member_since')->nullable();
            
            // ENQ fields
            $table->string('enq_id')->nullable();
            $table->string('enq_receiver_name')->nullable();
            $table->string('enq_receiver_email')->nullable();
            $table->string('enq_receiver_mobile')->nullable();
            
            // Processing status
            $table->enum('status', ['new', 'processing', 'converted', 'rejected', 'duplicate'])->default('new');
            $table->boolean('is_processed')->default(false);
            
            // Link to sales_records if converted
            $table->unsignedBigInteger('sales_record_id')->nullable();
            $table->foreign('sales_record_id')->references('id')->on('sales_records')->onDelete('set null');
            
            // Raw JSON data for reference
            $table->json('raw_data')->nullable();
            
            // Additional metadata
            $table->timestamp('fetched_at')->nullable();
            $table->text('notes')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('query_type');
            $table->index('sender_mobile');
            $table->index('sender_email');
            $table->index('status');
            $table->index('query_time');
            $table->index('is_processed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('indiamartleads');
    }
};
