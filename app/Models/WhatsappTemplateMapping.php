<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappTemplateMapping extends Model
{
    protected $fillable = [
        'template_name',
        'mappings',
        'media_urls',
    ];

    protected $casts = [
        'mappings' => 'array',
        'media_urls' => 'array',
    ];
}
