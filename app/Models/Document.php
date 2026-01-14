<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use App\Models\DocumentUserAccess;

class Document extends Model
{
    protected $fillable = [
        'category_id',
        'subcategory_id',
        'title',
        'description',
        'filename',
        'original_filename',
        'file_path',
        'file_extension',
        'file_size',
        'mime_type',
        'uploaded_by',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'file_size' => 'integer'
    ];

    protected $appends = ['formatted_file_size'];

    /**
     * Get the category that owns the document
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(DocumentCategory::class, 'category_id');
    }

    /**
     * Get the subcategory that owns the document
     */
    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(DocumentSubcategory::class, 'subcategory_id');
    }

    /**
     * Get the user who uploaded the document
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get the document user access
     */
    public function documentAccess()
    {
        return $this->hasMany(DocumentUserAccess::class, 'document_id');
    }

    /**
     * Scope to get only active documents
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the full file URL
     */
    public function getFileUrlAttribute()
    {
        return Storage::url($this->file_path);
    }

    /**
     * Get formatted file size
     */
    public function getFormattedFileSizeAttribute()
    {
        $bytes = $this->file_size;
        if ($bytes === 0) return '0 Bytes';
        
        $k = 1024;
        $sizes = ['Bytes', 'KB', 'MB', 'GB'];
        $i = floor(log($bytes) / log($k));
        
        return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
    }
}
