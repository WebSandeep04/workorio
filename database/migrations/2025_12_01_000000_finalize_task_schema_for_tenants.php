<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Only run for tenant databases
        if (Schema::getConnection()->getName() === 'mysql') {
            return;
        }

        if (Schema::hasTable('tasks')) {
            Schema::table('tasks', function (Blueprint $table) {
                if (!Schema::hasColumn('tasks', 'task_type')) {
                    $table->enum('task_type', ['task', 'qc', 'cp'])->default('task')->after('task');
                }

                if (!Schema::hasColumn('tasks', 'is_recurring')) {
                    $table->boolean('is_recurring')->default(false)->after('task_type');
                }

                if (!Schema::hasColumn('tasks', 'recurrence_type')) {
                    $table->enum('recurrence_type', ['daily', 'weekly', 'monthly', 'yearly'])
                        ->nullable()
                        ->after('is_recurring');
                }

                if (!Schema::hasColumn('tasks', 'recurrence_interval')) {
                    $table->unsignedInteger('recurrence_interval')->nullable()->after('recurrence_type');
                } else {
                    // Ensure recurrence_type column uses enum when it already exists
                    if (Schema::hasColumn('tasks', 'recurrence_type')) {
                        $driver = Schema::getConnection()->getDriverName();
                        if ($driver === 'mysql') {
                            DB::statement("ALTER TABLE tasks MODIFY COLUMN recurrence_type ENUM('daily','weekly','monthly','yearly') NULL");
                        } else {
                            Schema::table('tasks', function (Blueprint $table) {
                                $table->string('recurrence_type')->nullable()->change();
                            });
                        }
                    }
                }

                if (!Schema::hasColumn('tasks', 'recurrence_days_of_week')) {
                    $table->json('recurrence_days_of_week')->nullable()->after('recurrence_interval');
                }

                if (!Schema::hasColumn('tasks', 'recurrence_day_of_month')) {
                    $table->unsignedTinyInteger('recurrence_day_of_month')->nullable()->after('recurrence_days_of_week');
                }

                if (!Schema::hasColumn('tasks', 'recurrence_months')) {
                    $table->json('recurrence_months')->nullable()->after('recurrence_day_of_month');
                }

                if (!Schema::hasColumn('tasks', 'recurrence_end_date')) {
                    $table->date('recurrence_end_date')->nullable()->after('recurrence_months');
                }

                if (!Schema::hasColumn('tasks', 'due_date')) {
                    $table->date('due_date')->nullable()->after('recurrence_end_date');
                }
            });

            // Ensure task_type enum includes 'cp' even if column already existed
            if (Schema::hasColumn('tasks', 'task_type')) {
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

        if (Schema::hasTable('tasks') && !Schema::hasTable('task_images')) {
            Schema::create('task_images', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('task_id');
                $table->string('image_path');
                $table->string('original_name')->nullable();
                $table->integer('file_size')->nullable();
                $table->timestamps();

                $table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade');
                $table->index('task_id');
            });
        }
    }

    public function down(): void
    {
        // Intentionally left blank. These columns/tables are part of the final schema.
    }
};

