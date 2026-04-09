<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CallingAssignmentLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'calling_id',
        'calling_campaign_id',
        'from_user_id',
        'to_user_id',
        'assigned_by',
        'remark'
    ];

    public function calling()
    {
        return $this->belongsTo(Calling::class, 'calling_id');
    }

    public function campaign()
    {
        return $this->belongsTo(CallingCampaign::class, 'calling_campaign_id');
    }

    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function toUser()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    public function assigner()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
