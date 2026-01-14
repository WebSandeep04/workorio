<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Remark extends Model
{
    // Removed tenant() relationship - no longer needed with separate databases
    protected $fillable = [
        'remark_date',
        'remark',
        'sales_remark_id',];

    protected $casts = [
        'remark_date' => 'date'
    ];

    public function salesRecord()
    {
        return $this->belongsTo(SalesRecord::class, 'sales_remark_id');
    }
}
