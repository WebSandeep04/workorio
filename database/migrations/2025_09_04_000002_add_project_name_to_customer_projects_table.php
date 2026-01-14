<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Skip this migration if running on master database
        if (Schema::getConnection()->getName() === 'mysql') {
        return;
        }
        Schema::table('customer_projects', function (Blueprint $table) {
        // After refactor, customer_projects uses service_id column
        if (Schema::hasColumn('customer_projects', 'service_id')) {
            $table->string('project_name')->nullable()->after('service_id');
        } else {
            // Fallback for legacy databases
            $table->string('project_name')->nullable()->after('project_id');
        }
        });
    }

    public function down(): void
    {
        Schema::table('customer_projects', function (Blueprint $table) {
        if (Schema::hasColumn('customer_projects', 'project_name')) {
            $table->dropColumn('project_name');
        }
        });
    }
};



