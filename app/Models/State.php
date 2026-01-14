<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    // Removed tenant() relationship - no longer needed with separate databases
    protected $fillable = ['state_name'];

    public function cities()
    {
        return $this->hasMany(City::class);
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
