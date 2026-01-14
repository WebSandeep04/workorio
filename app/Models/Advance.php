<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Advance extends Model
{
    protected $fillable = [
        'sales_record_id',
        'amount',
        'payment_date',
        'transaction_id',
        'notes',
        'next_followup_date'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
        'next_followup_date' => 'date'
    ];

    public function salesRecord()
    {
        return $this->belongsTo(SalesRecord::class);
    }
}

