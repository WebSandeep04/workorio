<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesStatus extends Model
{
    // Removed tenant() relationship - no longer needed with separate databases
    protected $table = 'sales_status';
    protected $fillable = ['status_name',];

    public function salesRecords()
    {
        return $this->hasMany(SalesRecord::class, 'status_id');
    }

}
