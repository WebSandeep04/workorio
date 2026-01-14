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
        if (Schema::getConnection()->getName() === 'mysql') {
            return;
        }

        Schema::table('customer_projects', function (Blueprint $table) {
            $table->boolean('critical_path_enabled')
                ->default(false)
                ->after('profit_value');
            $table->foreignId('workflow_template_id')
                ->nullable()
                ->after('critical_path_enabled')
                ->constrained('workflow_templates')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_projects', function (Blueprint $table) {
            if (Schema::hasColumn('customer_projects', 'workflow_template_id')) {
                $table->dropForeign(['workflow_template_id']);
                $table->dropColumn('workflow_template_id');
            }
            if (Schema::hasColumn('customer_projects', 'critical_path_enabled')) {
                $table->dropColumn('critical_path_enabled');
            }
        });
    }
};



