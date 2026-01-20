<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class BusinessCardScan extends TenantModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'designation',
        'company_name',
        'industry',
        'email',
        'phone_primary',
        'phone_secondary',
        'website',
        'address',
        'city',
        'state',
        'pincode',
        'country',
        'social_links',
        'raw_text',
        'card_image_url',
        'raw_ai_response',
        'is_converted',
        'sales_record_id',
        'created_by'
    ];

    protected $casts = [
        'social_links' => 'array',
        'raw_ai_response' => 'array',
        'is_converted' => 'boolean',
    ];

    public function salesman()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function salesRecord()
    {
        return $this->belongsTo(SalesRecord::class, 'sales_record_id');
    }
}
