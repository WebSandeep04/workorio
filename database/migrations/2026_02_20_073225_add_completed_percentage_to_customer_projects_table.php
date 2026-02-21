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
        if (Schema::hasTable('customer_projects')) {
            Schema::table('customer_projects', function (Blueprint $table) {
                if (!Schema::hasColumn('customer_projects', 'completed_percentage')) {
                    $table->integer('completed_percentage')->default(0)->after('status');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_projects', function (Blueprint $table) {
            $table->dropColumn('completed_percentage');
        });
    }
};
