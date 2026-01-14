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

        Schema::table('workflow_tasks', function (Blueprint $table) {
            if (!Schema::hasColumn('workflow_tasks', 'duration_days')) {
                $table->unsignedInteger('duration_days')->nullable()->after('position');
            }
        });

        if (Schema::hasColumn('workflow_tasks', 'end_date')) {
            Schema::table('workflow_tasks', function (Blueprint $table) {
                $table->dropColumn('end_date');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('workflow_tasks', 'duration_days')) {
            Schema::table('workflow_tasks', function (Blueprint $table) {
                $table->dropColumn('duration_days');
            });
        }

        Schema::table('workflow_tasks', function (Blueprint $table) {
            if (!Schema::hasColumn('workflow_tasks', 'end_date')) {
                $table->date('end_date')->nullable()->after('position');
            }
        });
    }
};



