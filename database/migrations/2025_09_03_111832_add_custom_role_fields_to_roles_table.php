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
        
        Schema::table('roles', function (Blueprint $table) {
            $table->text('description')->nullable()->after('role_name');
            $table->boolean('is_custom')->default(false)->after('description');
            $table->unsignedBigInteger('created_by')->nullable()->after('is_custom');
            $table->json('permissions_data')->nullable()->after('created_by');
            
            // Add foreign key constraints
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn(['description', 'is_custom', 'created_by', 'permissions_data']);
        });
    }
};
