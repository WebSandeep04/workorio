<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Skip if running on master database (multi-tenant pattern in this app)
        if (Schema::getConnection()->getName() === 'mysql') {
            return;
        }

        if (!Schema::hasTable('tasks') || !Schema::hasColumn('tasks', 'task_type')) {
            return;
        }

        Schema::table('tasks', function (Blueprint $table) {
            if (!Schema::hasColumn('tasks', 'is_recurring')) {
                $table->boolean('is_recurring')->default(false)->after('task_type');
            }
            if (!Schema::hasColumn('tasks', 'recurrence_type')) {
                $table->string('recurrence_type')->nullable()->after('is_recurring'); // daily|weekly|monthly|yearly
            }
            if (!Schema::hasColumn('tasks', 'recurrence_interval')) {
                $table->unsignedInteger('recurrence_interval')->nullable()->after('recurrence_type'); // every N units
            }
            if (!Schema::hasColumn('tasks', 'recurrence_days_of_week')) {
                $table->json('recurrence_days_of_week')->nullable()->after('recurrence_interval'); // ["mon","tue",...]
            }
            if (!Schema::hasColumn('tasks', 'recurrence_day_of_month')) {
                $table->unsignedTinyInteger('recurrence_day_of_month')->nullable()->after('recurrence_days_of_week');
            }
            if (!Schema::hasColumn('tasks', 'recurrence_months')) {
                $table->json('recurrence_months')->nullable()->after('recurrence_day_of_month'); // [1..12]
            }
            if (!Schema::hasColumn('tasks', 'recurrence_end_date')) {
                $table->date('recurrence_end_date')->nullable()->after('recurrence_months');
            }
        });
    }

    public function down(): void
    {
        if (Schema::getConnection()->getName() === 'mysql') {
            return;
        }
        if (!Schema::hasTable('tasks')) {
            return;
        }

        $columns = [
            'is_recurring',
            'recurrence_type',
            'recurrence_interval',
            'recurrence_days_of_week',
            'recurrence_day_of_month',
            'recurrence_months',
            'recurrence_end_date',
        ];

        $columnsToDrop = array_filter($columns, function ($column) {
            return Schema::hasColumn('tasks', $column);
        });

        if (empty($columnsToDrop)) {
            return;
        }

        Schema::table('tasks', function (Blueprint $table) use ($columnsToDrop) {
            $table->dropColumn($columnsToDrop);
        });
    }
};



