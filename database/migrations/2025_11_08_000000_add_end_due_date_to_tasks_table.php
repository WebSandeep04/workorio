<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Skip this migration if running on master database
        if (Schema::getConnection()->getName() === 'mysql') {
            return;
        }

        if (!Schema::hasTable('tasks') || !Schema::hasColumn('tasks', 'recurrence_end_date') || Schema::hasColumn('tasks', 'due_date')) {
            return;
        }

        Schema::table('tasks', function (Blueprint $table) {
            $table->date('due_date')->nullable()->after('recurrence_end_date');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('tasks') || !Schema::hasColumn('tasks', 'due_date')) {
            return;
        }

        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn('due_date');
        });
    }
};


