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
        
        Schema::table('invoices', function (Blueprint $table) {
            $table->unsignedBigInteger('customer_id')->nullable()->after('sales_record_id');
            $table->unsignedBigInteger('product_id')->nullable()->after('customer_id');
            
            // Foreign Key Constraints
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
            $table->foreign('product_id')->references('id')->on('sales_products')->onDelete('set null');
            
            // Indexes for better performance
            $table->index('customer_id');
            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropForeign(['product_id']);
            $table->dropIndex(['customer_id']);
            $table->dropIndex(['product_id']);
            $table->dropColumn(['customer_id', 'product_id']);
        });
    }
};

