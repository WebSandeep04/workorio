<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CallingCampaign extends Model
{
    protected $fillable = ['name'];

    public function callings()
    {
        return $this->belongsToMany(Calling::class, 'calling_campaign_calling')
            ->withPivot(['user_id', 'status', 'next_followup_date'])
            ->withTimestamps();
    }

    public function remarks()
    {
        return $this->hasMany(CallingRemark::class, 'calling_campaign_id');
    }

    public function assignmentLogs()
    {
        return $this->hasMany(CallingAssignmentLog::class, 'calling_campaign_id');
    }
}
