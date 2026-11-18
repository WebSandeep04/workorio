<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemEmailLog extends Model
{
    protected $table = 'system_email_logs';

    protected $fillable = [
        'mail_code',
        'recipient_email',
        'recipient_name',
        'user_id',
        'subject',
        'status',
        'error_message',
        'meta',
        'sent_at',
    ];

    protected $casts = [
        'meta' => 'array',
        'sent_at' => 'datetime',
    ];

    /**
     * Relationship with SystemEmailAlert (definition / master)
     */
    public function alert()
    {
        return $this->belongsTo(SystemEmailAlert::class, 'mail_code', 'mail_code');
    }

    /**
     * Optional relationship to tenant User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
