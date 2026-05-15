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
         // Skip this migration if running on master database (multi-tenant check)
        if (Schema::getConnection()->getName() === 'mysql') {
            return;
        }

        Schema::table('employees', function (Blueprint $table) {
            // Large text column to safely store serialized 128/512 JSON arrays of floats.
            $table->longText('face_embeddings')->nullable()->after('profile_picture');
            
            // Boolean to allow rapid querying/indexing of enrolled kiosks
            $table->boolean('is_face_enrolled')->default(0)->after('face_embeddings');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::getConnection()->getName() === 'mysql') {
            return;
        }
        
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['face_embeddings', 'is_face_enrolled']);
        });
    }
};
