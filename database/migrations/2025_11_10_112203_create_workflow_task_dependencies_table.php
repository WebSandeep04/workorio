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

        Schema::create('workflow_task_dependencies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_template_id')->constrained('workflow_templates')->cascadeOnDelete();
            $table->foreignId('predecessor_task_id')->constrained('workflow_tasks')->cascadeOnDelete();
            $table->foreignId('successor_task_id')->constrained('workflow_tasks')->cascadeOnDelete();
            $table->foreignId('dependency_type_id')->constrained('workflow_dependency_types')->cascadeOnDelete();
            $table->integer('lag_days')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['workflow_template_id', 'predecessor_task_id', 'successor_task_id', 'dependency_type_id'], 'workflow_task_dependencies_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_task_dependencies');
    }
};

