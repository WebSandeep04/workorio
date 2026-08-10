<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsappCampaignMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'whatsapp_campaign_id',
        'source_type',
        'source_id',
        'name',
        'phone_number',
        'status',
    ];

    public function campaign()
    {
        return $this->belongsTo(WhatsappCampaign::class, 'whatsapp_campaign_id');
    }

    public function source()
    {
        return $this->morphTo(__FUNCTION__, 'source_type', 'source_id');
    }
}
