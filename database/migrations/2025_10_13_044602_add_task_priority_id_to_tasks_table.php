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
            $table->unsignedBigInteger('task_priority_id')->nullable()->after('task_status_id');
            $table->foreign('task_priority_id')->references('id')->on('task_priorities')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['task_priority_id']);
            $table->dropColumn('task_priority_id');
        });
    }
};

