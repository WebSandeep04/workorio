<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Prospectus extends Model
{
    // Removed tenant() relationship - no longer needed with separate databases
    use HasFactory;

    protected $table = 'prospectuses';

    protected $fillable = [
        'prospectus_name',
        'contact_person',
        'contact_number',
        'address',
        'state_id',
        'city_id',
        'email',
        'website_link',
        'business_type_id',];

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

    public function salesRecords()
    {
        return $this->hasMany(SalesRecord::class);
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }
}
