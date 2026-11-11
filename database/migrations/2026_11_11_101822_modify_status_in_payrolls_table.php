<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::getConnection()->getName() === 'mysql') { return; }

        if (Schema::hasTable('payrolls')) {
            // Change enum to support Draft, Approved, Rejected, Finalized or string column
            DB::statement("ALTER TABLE payrolls MODIFY COLUMN status VARCHAR(50) NOT NULL DEFAULT 'Draft'");
            
            Schema::table('payrolls', function (Blueprint $table) {
                if (!Schema::hasColumn('payrolls', 'approved_by')) {
                    $table->unsignedBigInteger('approved_by')->nullable()->after('status');
                }
                if (!Schema::hasColumn('payrolls', 'approved_at')) {
                    $table->timestamp('approved_at')->nullable()->after('approved_by');
                }
                if (!Schema::hasColumn('payrolls', 'rejection_reason')) {
                    $table->text('rejection_reason')->nullable()->after('approved_at');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::getConnection()->getName() === 'mysql') { return; }

        if (Schema::hasTable('payrolls')) {
            Schema::table('payrolls', function (Blueprint $table) {
                if (Schema::hasColumn('payrolls', 'rejection_reason')) {
                    $table->dropColumn('rejection_reason');
                }
                if (Schema::hasColumn('payrolls', 'approved_at')) {
                    $table->dropColumn('approved_at');
                }
                if (Schema::hasColumn('payrolls', 'approved_by')) {
                    $table->dropColumn('approved_by');
                }
            });
        }
    }
};
