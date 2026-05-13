<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesRecord extends Model
{
    protected $table = 'sales_records';
    public $timestamps = false; // Disable automatic timestamps since we have custom columns
    
    protected $fillable = [
        'user_id',
        'leads_name',
        'contact_person',
        'contact_number',
        'address',
        'state_id',
        'city_id',
        'email',
        'business_type_id',
        'lead_source_id',
        'status_id',
        'next_follow_up_date',
        'products_id',
        'prospectus_id',
        'customer_id',
        'updatedat',
        'update_remark',
        'status_update_remark',
        'status_updatedat',
        'createdat',
        'ticket_value',
        'website_link'
    ];

    protected $casts = [
        'next_follow_up_date' => 'date',
        'createdat' => 'date',
        'updatedat' => 'datetime',
        'status_updatedat' => 'datetime',
        'ticket_value' => 'integer'
    ];

    // Removed tenant() relationship - no longer needed with separate databases

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function businessType()
    {
        return $this->belongsTo(SalesBusinessType::class, 'business_type_id');
    }

    public function leadSource()
    {
        return $this->belongsTo(SalesLeadSource::class, 'lead_source_id');
    }

    public function status()
    {
        return $this->belongsTo(SalesStatus::class, 'status_id');
    }

    public function product()
    {
        return $this->belongsTo(SalesProduct::class, 'products_id');
    }

    public function prospectus()
    {
        return $this->belongsTo(Prospectus::class);
    }

    public function remarks()
    {
        return $this->hasMany(Remark::class, 'sales_remark_id');
    }

    /**
     * Check if a customer exists for this sales record's prospectus
     */
    public function customer()
    {
        return $this->hasOne(Customer::class, 'prospectus_id', 'prospectus_id');
    }


    public function directCustomer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function latestRemark()
    {
        return $this->hasOne(Remark::class, 'sales_remark_id')
                    ->latest('remark_date');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function advances()
    {
        return $this->hasMany(Advance::class);
    }

    public function assignmentLogs()
    {
        return $this->hasMany(LeadAssignmentLog::class, 'sales_record_id')->latest();
    }

    public function creatorLog()
    {
        return $this->hasOne(LeadAssignmentLog::class, 'sales_record_id')->oldestOfMany();
    }
}
