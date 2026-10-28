<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiSetting extends Model
{
    use HasFactory;

    protected $table = 'ai_settings';

    protected $fillable = [
        'is_gemini_byo_enabled',
        'gemini_api_key',
        'is_openai_byo_enabled',
        'openai_api_key',
    ];

    protected $casts = [
        'is_gemini_byo_enabled' => 'boolean',
        'is_openai_byo_enabled' => 'boolean',
    ];
}

