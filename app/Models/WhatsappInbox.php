<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WhatsappInbox extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_number',
        'receiver_number',
        'message_text',
        'media_url',
        'message_type',
        'msg91_message_id',
        'is_read',
        'received_at'
    ];
}
