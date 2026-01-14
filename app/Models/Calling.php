<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Calling extends Model
{
    protected $fillable = [
        'user_id',
        'calling_type_id',
        'status_id',
        'name',
        'email',
        'phone',
        'address',
        'state_id',
        'city_id',
        'next_follow_up_date',
    ];

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function remarks()
    {
        return $this->hasMany(CallingRemark::class);
    }

    public function latestRemark()
    {
        return $this->hasOne(CallingRemark::class)->latestOfMany();
    }

    public function callingType()
    {
        return $this->belongsTo(CallingType::class);
    }

    public function status()
    {
        return $this->belongsTo(SalesStatus::class, 'status_id');
    }
}
