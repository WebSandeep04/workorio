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
        Schema::table('users', function (Blueprint $table) {
        $table->decimal('salary_per_month', 12, 2)->nullable()->after('is_manager');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
        $table->dropColumn('salary_per_month');
        });
    }
};



