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
        
        Schema::create('document_permissions', function (Blueprint $table) {
            $table->id();
            $table->morphs('permissible'); // polymorphic relation (category, subcategory, document)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->boolean('can_view')->default(true);
            $table->boolean('can_add')->default(false);
            $table->boolean('can_edit')->default(false);
            $table->boolean('can_delete')->default(false);
            $table->timestamps();
            
            // Prevent duplicate permissions
            $table->unique(['permissible_type', 'permissible_id', 'user_id'], 'unique_permission');
            $table->index(['user_id', 'permissible_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_permissions');
    }
};

