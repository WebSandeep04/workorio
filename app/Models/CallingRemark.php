<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CallingRemark extends Model
{
    protected $fillable = [
        'calling_id',
        'calling_campaign_id',
        'user_id',
        'remark',
    ];

    public function calling()
    {
        return $this->belongsTo(Calling::class, 'calling_id');
    }

    public function campaign()
    {
        return $this->belongsTo(CallingCampaign::class, 'calling_campaign_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
