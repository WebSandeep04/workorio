<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiApiLog extends Model
{
    use HasFactory;

    protected $table = 'ai_api_logs';

    protected $fillable = [
        'endpoint',
        'model',
        'prompt_tokens',
        'completion_tokens',
        'total_tokens',
        'payload',
        'response',
        'error_message',
    ];

    protected $casts = [
        'payload' => 'array',
        'response' => 'array',
    ];
}
