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
        $table->decimal('original_value', 12, 2)->nullable()->after('description');
        $table->decimal('estimated_value', 12, 2)->nullable()->after('original_value');
        $table->decimal('profit_value', 12, 2)->nullable()->after('estimated_value');
        });
    }

    public function down(): void
    {
        Schema::table('customer_projects', function (Blueprint $table) {
        $table->dropColumn(['original_value', 'estimated_value', 'profit_value']);
        });
    }
};



