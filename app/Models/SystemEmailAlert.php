<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemEmailAlert extends Model
{
    protected $table = 'system_email_alerts';

    protected $fillable = [
        'mail_code',
        'name',
        'category',
        'default_time',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Recipients mapped to this mail alert
     */
    public function recipients()
    {
        return $this->belongsToMany(User::class, 'system_email_alert_recipients', 'mail_code', 'user_id', 'mail_code', 'id');
    }

    /**
     * Logs for this mail alert
     */
    public function logs()
    {
        return $this->hasMany(SystemEmailLog::class, 'mail_code', 'mail_code');
    }
}
