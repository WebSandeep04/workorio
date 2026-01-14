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

        if (Schema::hasTable('payment_terms')) {
            Schema::table('payment_terms', function (Blueprint $table) {
                if (Schema::hasColumn('payment_terms', 'payment_term_group_id')) {
                    try {
                        $table->dropForeign(['payment_term_group_id']);
                    } catch (\Throwable $e) {
                        // Foreign key might already be gone; ignore
                    }
                }
            });
            
            Schema::dropIfExists('payment_terms');
        }

        Schema::dropIfExists('payment_term_groups');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration only drops tables, so down() is empty
    }
};
