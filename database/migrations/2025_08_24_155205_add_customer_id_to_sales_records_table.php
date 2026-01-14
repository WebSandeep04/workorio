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
        Schema::table('sales_records', function (Blueprint $table) {
        $table->unsignedBigInteger('customer_id')->nullable()->after('prospectus_id');
        
        // Foreign key constraint
        $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
        
        // Index for performance
        $table->index('customer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_records', function (Blueprint $table) {
        $table->dropForeign(['customer_id']);
        $table->dropIndex(['customer_id']);
        $table->dropColumn('customer_id');
        });
    }
};

