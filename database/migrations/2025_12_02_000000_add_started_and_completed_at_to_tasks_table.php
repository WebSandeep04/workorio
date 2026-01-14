<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Skip for master database; these columns are tenant-specific
        if (Schema::getConnection()->getName() === 'mysql') {
            return;
        }

        if (!Schema::hasTable('tasks')) {
            return;
        }

        Schema::table('tasks', function (Blueprint $table) {
            if (!Schema::hasColumn('tasks', 'started_at')) {
                $table->timestamp('started_at')->nullable()->after('due_date');
            }

            if (!Schema::hasColumn('tasks', 'completed_at')) {
                $table->timestamp('completed_at')->nullable()->after('started_at');
            }
        });
    }

    public function down(): void
    {
        if (Schema::getConnection()->getName() === 'mysql' || !Schema::hasTable('tasks')) {
            return;
        }

        Schema::table('tasks', function (Blueprint $table) {
            $columns = [];
            if (Schema::hasColumn('tasks', 'completed_at')) {
                $columns[] = 'completed_at';
            }
            if (Schema::hasColumn('tasks', 'started_at')) {
                $columns[] = 'started_at';
            }

            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};

