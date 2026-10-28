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
        // Skip migration if running on the master/central database
        if (Schema::getConnection()->getName() === 'mysql') {
            return;
        }

        // 1. Table for storing uploaded WhatsApp template documents
        Schema::create('whatsapp_templates_documents_list', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('filename');
            $table->string('original_filename');
            $table->string('file_path');
            $table->string('file_extension', 20)->nullable();
            $table->bigInteger('file_size')->default(0);
            $table->string('mime_type')->nullable();
            $table->timestamps();
        });

        // 2. Drop existing pivot table if it was referencing general documents, and recreate with reference to whatsapp_templates_documents_list
        Schema::dropIfExists('whatsapp_template_documents');

        Schema::create('whatsapp_template_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('whatsapp_template_id')->constrained('whatsapp_templates', 'id', 'fk_wtd_whatsapp_template_id')->onDelete('cascade');
            $table->foreignId('document_id')->constrained('whatsapp_templates_documents_list', 'id', 'fk_wtd_doc_list_id')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['whatsapp_template_id', 'document_id'], 'wtd_unique_template_doc');
            $table->index('document_id', 'wtd_document_id_idx');
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

        Schema::dropIfExists('whatsapp_template_documents');
        Schema::dropIfExists('whatsapp_templates_documents_list');
    }
};
