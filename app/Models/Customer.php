<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    // Removed tenant() relationship - no longer needed with separate databases
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'company_name','prospectus_id'
    ];

    public function customerProjects()
    {
        return $this->hasMany(CustomerProject::class);
    }

    public function prospectus()
    {
        return $this->belongsTo(Prospectus::class);
    }


    public function salesRecords()
    {
        return $this->hasMany(SalesRecord::class, 'customer_id');
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}
