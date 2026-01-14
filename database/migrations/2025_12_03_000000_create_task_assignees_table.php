<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::getConnection()->getName() === 'mysql') {
            return;
        }

        if (!Schema::hasTable('tasks')) {
            return;
        }

        Schema::create('task_assignees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('task_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('assigned_by')->nullable();
            $table->timestamps();

            $table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('assigned_by')->references('id')->on('users')->nullOnDelete();
            $table->unique(['task_id', 'user_id']);
            $table->index('user_id');
        });

        // Backfill existing single assignments into the pivot table
        $tasks = DB::table('tasks')
            ->whereNotNull('user_id')
            ->select('id', 'user_id', 'created_by')
            ->get();

        foreach ($tasks as $task) {
            DB::table('task_assignees')->updateOrInsert(
                ['task_id' => $task->id, 'user_id' => $task->user_id],
                ['assigned_by' => $task->created_by, 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }

    public function down(): void
    {
        if (Schema::getConnection()->getName() === 'mysql') {
            return;
        }

        Schema::dropIfExists('task_assignees');
    }
};

