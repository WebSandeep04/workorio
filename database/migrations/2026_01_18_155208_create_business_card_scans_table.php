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

        Schema::create('business_card_scans', function (Blueprint $table) {
            $table->id();

            // Contact Basics
            $table->string('name')->nullable();
            $table->string('designation')->nullable()->comment('Job Title');
            $table->string('company_name')->nullable();
            
            // Communication
            $table->string('email')->nullable();
            $table->string('phone_primary')->nullable();
            $table->string('phone_secondary')->nullable();
            $table->string('website')->nullable();
            
            // Location
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('pincode')->nullable();
            $table->string('country')->nullable();
            
            // Metadata & Storage
            $table->json('social_links')->nullable()->comment('LinkedIn, Twitter handles etc');
            $table->text('raw_text')->nullable()->comment('Full OCR extracted text');
            $table->string('card_image_url')->nullable();
            $table->json('raw_ai_response')->nullable()->comment('Full JSON response from Gemini');

            // System Status
            $table->boolean('is_converted')->default(false)->comment('Converted to Lead/Contact');
            $table->unsignedBigInteger('sales_record_id')->nullable()->comment('Linked Lead ID if converted');
            $table->unsignedBigInteger('created_by')->nullable(); // User who scanned it

            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for searching
            $table->index('name');
            $table->index('company_name');
            $table->index('email');
            $table->index('phone_primary');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_card_scans');
    }
};
