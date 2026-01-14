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

        if (!Schema::hasTable('quotation_revisions')) {
            Schema::create('quotation_revisions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('quotation_id')->index();
                $table->unsignedInteger('version');
                $table->string('file_path');
                $table->json('data')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();

                $table->foreign('quotation_id')->references('id')->on('quotations')->onDelete('cascade');
                $table->unique(['quotation_id','version']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('quotation_revisions');
    }
};



