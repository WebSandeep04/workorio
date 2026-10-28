<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class WhatsappTemplateDocument extends Model
{
    protected $table = 'whatsapp_templates_documents_list';

    protected $fillable = [
        'title',
        'filename',
        'original_filename',
        'file_path',
        'file_extension',
        'file_size',
        'mime_type',
    ];

    protected $appends = ['formatted_file_size', 'file_url', 'download_url'];

    /**
     * Templates using this document
     */
    public function templates()
    {
        return $this->belongsToMany(WhatsappTemplate::class, 'whatsapp_template_documents', 'document_id', 'whatsapp_template_id');
    }

    /**
     * Get the full file public direct download URL
     * This URL forces direct file download on client devices rather than in-browser viewing.
     */
    public function getDownloadUrlAttribute(): string
    {
        $tenantId = session('tenant_id', 1);
        return url("/public/whatsapp-doc/{$tenantId}/{$this->id}/download");
    }

    /**
     * Get direct download URL (same as download_url)
     */
    public function getFileUrlAttribute(): string
    {
        return $this->download_url;
    }

    /**
     * Get human readable file size
     */
    public function getFormattedFileSizeAttribute(): string
    {
        $bytes = (int) $this->file_size;
        if ($bytes <= 0) return '0 B';

        $units = ['B', 'KB', 'MB', 'GB'];
        $power = floor(log($bytes, 1024));
        return round($bytes / pow(1024, $power), 2) . ' ' . ($units[$power] ?? 'B');
    }
}
