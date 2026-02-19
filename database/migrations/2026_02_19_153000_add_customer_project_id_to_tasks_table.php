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

        Schema::table('tasks', function (Blueprint $table) {
            $table->unsignedBigInteger('customer_project_id')->nullable()->after('customer_id');
            $table->foreign('customer_project_id')->references('id')->on('customer_projects')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['customer_project_id']);
            $table->dropColumn('customer_project_id');
        });
    }
};
