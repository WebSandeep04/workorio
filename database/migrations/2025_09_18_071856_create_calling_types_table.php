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

        Schema::create('calling_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        // Add foreign key constraint to callings table after calling_types table is created
        if (Schema::hasTable('callings') && Schema::hasColumn('callings', 'calling_type_id')) {
            Schema::table('callings', function (Blueprint $table) {
                $table->foreign('calling_type_id')->references('id')->on('calling_types')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign key constraint first if it exists
        if (Schema::hasTable('callings')) {
            Schema::table('callings', function (Blueprint $table) {
                $table->dropForeign(['calling_type_id']);
            });
        }
        
        Schema::dropIfExists('calling_types');
    }
};

