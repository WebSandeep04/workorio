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
        // Skip if running on master database
       if (Schema::getConnection()->getName() === 'mysql') {
            return;
        }

        Schema::table('subscription_histories', function (Blueprint $table) {
            if (!Schema::hasColumn('subscription_histories', 'status')) {
                $table->string('status')->nullable()->after('end_date');
            }
            // Make end_date nullable (modifying existing column)
            // Note: SQLite doesn't support basic column modification well, providing raw logic for typical MySQL/Postgres
            // For safety in Laravel we usually use change() but require doctrine/dbal.
            // As this is a recent table and likely MySQL, we use Modify or explicit raw statement if change() is risky without deps.
            // But let's try standard change() assuming dependencies are met, or use raw if safe.
            // Given the agent constraint, I'll attempt standard schema builder.
            $table->date('end_date')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::getConnection()->getName() === 'mysql') {
            return;
        }

        Schema::table('subscription_histories', function (Blueprint $table) {
             if (Schema::hasColumn('subscription_histories', 'status')) {
                 $table->dropColumn('status');
             }
             // Reverting nullable is hard if nulls exist, so we skip it or set logic to avoid error
             // $table->date('end_date')->nullable(false)->change(); 
        });
    }
};
