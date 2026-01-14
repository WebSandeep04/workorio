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

        if (!Schema::hasTable('checklist_options')) {
            Schema::create('checklist_options', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('checklist_id');
                $table->string('name');
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index(['checklist_id']);
                $table->index(['is_active']);
                $table->unique(['checklist_id', 'name'], 'uniq_checklist_option_name');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('checklist_options');
    }
};



