<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesLeadSource extends Model
{
    // Removed tenant() relationship - no longer needed with separate databases
    protected $table = 'sales_lead_sources';
    protected $fillable = ['source_name',];

    public function salesRecords()
    {
        return $this->hasMany(SalesRecord::class, 'lead_source_id');
    }
}
