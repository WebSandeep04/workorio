<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Calling extends Model
{
    // The user removed timestamps from the callings migration
    public $timestamps = false;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'city',
        'state',
    ];

    /**
     * Many-to-Many relationship with campaigns
     */
    public function campaigns()
    {
        return $this->belongsToMany(CallingCampaign::class, 'calling_campaign_calling')
            ->withPivot(['user_id', 'status', 'next_followup_date'])
            ->withTimestamps();
    }

    /**
     * All remarks for this person
     */
    public function remarks()
    {
        return $this->hasMany(CallingRemark::class, 'calling_id');
    }

    /**
     * Get the latest remark recorded for this person
     */
    public function latestRemark()
    {
        return $this->hasOne(CallingRemark::class, 'calling_id')->latestOfMany();
    }

    /**
     * All assignment logs for this person
     */
    public function assignmentLogs()
    {
        return $this->hasMany(CallingAssignmentLog::class, 'calling_id')->latest();
    }
}
