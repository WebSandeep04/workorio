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
        // Skip this migration if running on master database
        if (Schema::getConnection()->getName() === 'mysql') {
            return;
        }

        Schema::table('tasks', function (Blueprint $table) {
            $table->unsignedBigInteger('task_status_id')->nullable()->after('user_id');
            $table->foreign('task_status_id')->references('id')->on('task_statuses')->onDelete('set null');
        });

        // Set default status (Pending) for existing tasks
        DB::table('tasks')->whereNull('task_status_id')->update(['task_status_id' => 1]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['task_status_id']);
            $table->dropColumn('task_status_id');
        });
    }
};

