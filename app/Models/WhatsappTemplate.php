<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class WhatsappTemplate extends Model
{
    protected $fillable = ['name', 'text'];

    /**
     * Get the dedicated WhatsApp template documents associated with this template.
     */
    public function documents(): BelongsToMany
    {
        return $this->belongsToMany(
            WhatsappTemplateDocument::class,
            'whatsapp_template_documents',
            'whatsapp_template_id',
            'document_id'
        );
    }
}
