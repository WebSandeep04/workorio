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
        
        Schema::table('document_categories', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->onDelete('cascade');
            $table->foreignId('storage_id')->nullable()->after('user_id')->comment('Storage/tenant identifier');
            $table->index(['user_id', 'storage_id']);
        });
        
        Schema::table('document_subcategories', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->onDelete('cascade');
            $table->foreignId('storage_id')->nullable()->after('user_id')->comment('Storage/tenant identifier');
            $table->index(['user_id', 'storage_id']);
        });
        
        Schema::table('documents', function (Blueprint $table) {
            $table->foreignId('storage_id')->nullable()->after('id')->comment('Storage/tenant identifier');
            $table->index(['storage_id', 'uploaded_by']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropIndex(['storage_id', 'uploaded_by']);
            $table->dropColumn('storage_id');
        });
        
        Schema::table('document_subcategories', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'storage_id']);
            $table->dropColumn(['user_id', 'storage_id']);
        });
        
        Schema::table('document_categories', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'storage_id']);
            $table->dropColumn(['user_id', 'storage_id']);
        });
    }
};

