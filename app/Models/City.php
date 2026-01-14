<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    // Removed tenant() relationship - no longer needed with separate databases
    protected $fillable = ['state_id', 'city_name'];

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function salesRecords()
    {
        return $this->hasMany(SalesRecord::class);
    }

    public function prospectuses()
    {
        return $this->hasMany(Prospectus::class);
    }
}
