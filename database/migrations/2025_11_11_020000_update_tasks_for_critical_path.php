<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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

        if (!Schema::hasTable('tasks')) {
            return;
        }

        Schema::table('tasks', function (Blueprint $table) {
            if (!Schema::hasColumn('tasks', 'customer_project_id')) {
                $table->foreignId('customer_project_id')
                    ->nullable()
                    ->after('customer_id')
                    ->constrained('customer_projects')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('tasks', 'workflow_task_id')) {
                $table->foreignId('workflow_task_id')
                    ->nullable()
                    ->after('customer_project_id')
                    ->constrained('workflow_tasks')
                    ->nullOnDelete();
            }
        });

        // Expand task_type enum to include critical path (cp)
        if (Schema::hasTable('tasks') && Schema::hasColumn('tasks', 'task_type')) {
            $driver = Schema::getConnection()->getDriverName();
            if ($driver === 'mysql') {
                DB::statement("ALTER TABLE tasks MODIFY COLUMN task_type ENUM('task','qc','cp') DEFAULT 'task'");
            } else {
                Schema::table('tasks', function (Blueprint $table) {
                    $table->string('task_type', 20)->default('task')->change();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert enum change
        if (Schema::hasTable('tasks') && Schema::hasColumn('tasks', 'task_type')) {
            $driver = Schema::getConnection()->getDriverName();
            if ($driver === 'mysql') {
                DB::statement("ALTER TABLE tasks MODIFY COLUMN task_type ENUM('task','qc') DEFAULT 'task'");
            } else {
                Schema::table('tasks', function (Blueprint $table) {
                    $table->string('task_type', 20)->default('task')->change();
                });
            }
        }

        Schema::table('tasks', function (Blueprint $table) {
            if (Schema::hasColumn('tasks', 'workflow_task_id')) {
                $table->dropForeign(['workflow_task_id']);
                $table->dropColumn('workflow_task_id');
            }

            if (Schema::hasColumn('tasks', 'customer_project_id')) {
                $table->dropForeign(['customer_project_id']);
                $table->dropColumn('customer_project_id');
            }
        });
    }
};



